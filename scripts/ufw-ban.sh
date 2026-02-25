#!/bin/bash

LOGFILE="/var/log/apache2/access.log"
BANLIST="/var/log/ufw-banned.log"
OFFSET_FILE="/var/tmp/ufw-ban-offset"
GOOGLE_CACHE="/var/tmp/google-ip-ranges.txt"
GOOGLE_CACHE_TTL=86400
THRESHOLD=50
TIMEWINDOW=60
USE_IPSET=true
IPSET_NAME="banned_ips"

URL_PATTERN='(GET|POST|HEAD) (/|/\?[^ ]*|/search(\?[^ ]*)?|/user/[^ ]*|/hashtag/[^ ]*|/status/[0-9]+(\?[^ ]*)?|/downloader(\?[^ ]*)?|/widget(\?[^ ]*)?|/assets/[^ ]*|/favicon\.ico|/robots\.txt|/sitemap\.xml|/\.well-known/[^ ]*) HTTP/'

fetch_google_ranges() {
    local now=$(date +%s)
    local update_needed=true

    if [[ -f "$GOOGLE_CACHE" ]]; then
        local cache_age=$(( now - $(stat -c %Y "$GOOGLE_CACHE" 2>/dev/null || echo 0) ))
        if (( cache_age < GOOGLE_CACHE_TTL )); then
            update_needed=false
        fi
    fi

    if $update_needed; then
        echo "[$(date)] Updating Google IP ranges..." >> "$BANLIST"

        local tmp_file=$(mktemp)
        local success=false

        if curl -sf --max-time 10 "https://www.gstatic.com/ipranges/goog.json" -o "$tmp_file" 2>/dev/null; then
            if command -v jq &>/dev/null; then
                jq -r '.prefixes[].ipv4Prefix // empty' "$tmp_file" > "${GOOGLE_CACHE}.new" 2>/dev/null
            else
                grep -oP '"ipv4Prefix":\s*"\K[^"]+' "$tmp_file" > "${GOOGLE_CACHE}.new" 2>/dev/null
            fi
            success=true
        fi

        local tmp_file2=$(mktemp)
        if curl -sf --max-time 10 "https://www.gstatic.com/ipranges/cloud.json" -o "$tmp_file2" 2>/dev/null; then
            if command -v jq &>/dev/null; then
                jq -r '.prefixes[].ipv4Prefix // empty' "$tmp_file2" >> "${GOOGLE_CACHE}.new" 2>/dev/null
            else
                grep -oP '"ipv4Prefix":\s*"\K[^"]+' "$tmp_file2" >> "${GOOGLE_CACHE}.new" 2>/dev/null
            fi
            success=true
        fi

        rm -f "$tmp_file" "$tmp_file2"

        if $success && [[ -s "${GOOGLE_CACHE}.new" ]]; then
            sort -u "${GOOGLE_CACHE}.new" > "$GOOGLE_CACHE"
            rm -f "${GOOGLE_CACHE}.new"
            echo "[$(date)] Google IP ranges updated: $(wc -l < "$GOOGLE_CACHE") ranges" >> "$BANLIST"
        else
            rm -f "${GOOGLE_CACHE}.new"
            if [[ ! -f "$GOOGLE_CACHE" ]]; then
                cat > "$GOOGLE_CACHE" << 'FALLBACK'
64.233.160.0/19
66.102.0.0/20
66.249.64.0/19
72.14.192.0/18
74.125.0.0/16
108.177.8.0/21
108.177.96.0/19
130.211.0.0/22
172.217.0.0/19
172.217.32.0/20
172.217.128.0/19
172.217.160.0/20
172.217.192.0/19
172.253.56.0/21
172.253.112.0/20
209.85.128.0/17
216.58.192.0/19
216.239.32.0/19
34.64.0.0/10
35.190.247.0/24
35.191.0.0/16
142.250.0.0/15
142.251.0.0/16
FALLBACK
                echo "[$(date)] Google IP fetch failed, using fallback list" >> "$BANLIST"
            fi
        fi
    fi
}

load_google_ranges() {
    GOOGLE_RANGES=()
    if [[ -f "$GOOGLE_CACHE" ]]; then
        while IFS= read -r line; do
            [[ -n "$line" ]] && GOOGLE_RANGES+=("$line")
        done < "$GOOGLE_CACHE"
    fi
}

ip_to_int() {
    local IFS='.'
    read -r a b c d <<< "$1"
    echo $(( (a << 24) + (b << 16) + (c << 8) + d ))
}

cidr_to_range() {
    local ip="${1%/*}"
    local prefix="${1#*/}"
    local ip_int=$(ip_to_int "$ip")
    local mask=$(( 0xFFFFFFFF << (32 - prefix) & 0xFFFFFFFF ))
    local start=$(( ip_int & mask ))
    local end=$(( start | (~mask & 0xFFFFFFFF) ))
    echo "$start $end"
}

is_google_ip() {
    local check_ip="$1"
    local check_int=$(ip_to_int "$check_ip")

    for range in "${GOOGLE_RANGES[@]}"; do
        read -r start end <<< "$(cidr_to_range "$range")"
        if (( check_int >= start && check_int <= end )); then
            return 0
        fi
    done
    return 1
}

is_already_banned() {
    local ip="$1"
    if $USE_IPSET && command -v ipset &>/dev/null; then
        ipset test "$IPSET_NAME" "$ip" 2>/dev/null && return 0
    else
        ufw status numbered | grep -q "$ip" && return 0
    fi
    return 1
}

setup_ipset() {
    if ! command -v ipset &>/dev/null; then
        echo "[$(date)] ipset not found, falling back to UFW" >> "$BANLIST"
        USE_IPSET=false
        return
    fi

    if ! ipset list "$IPSET_NAME" &>/dev/null; then
        ipset create "$IPSET_NAME" hash:ip hashsize 4096 maxelem 100000 timeout 0
        iptables -I INPUT -m set --match-set "$IPSET_NAME" src -j DROP
        echo "[$(date)] ipset '$IPSET_NAME' created" >> "$BANLIST"
    fi
}

ban_ip() {
    local ip="$1"
    local reason="$2"

    if $USE_IPSET && command -v ipset &>/dev/null; then
        ipset add "$IPSET_NAME" "$ip" 2>/dev/null
    else
        ufw deny from "$ip" to any comment "$reason"
    fi
}

unban_ip_cmd() {
    local ip="$1"

    if $USE_IPSET && command -v ipset &>/dev/null; then
        ipset del "$IPSET_NAME" "$ip" 2>/dev/null
    fi

    ufw delete deny from "$ip" 2>/dev/null

    echo "[$(date)] UNBANNED: $ip (manual)" >> "$BANLIST"
    echo "Unbanned: $ip"
}

parse_log_timestamp() {
    local raw="$1"
    raw="${raw#[}"
    raw="${raw%%]*}"
    date -d "$(echo "$raw" | sed 's|/| |g; s|:| |')" +%s 2>/dev/null || echo 0
}

ban_invalid_requests() {
    local current_offset=0
    if [[ -f "${OFFSET_FILE}.invalid" ]]; then
        current_offset=$(cat "${OFFSET_FILE}.invalid")
    fi

    local total_lines=$(wc -l < "$LOGFILE" 2>/dev/null || echo 0)

    if (( current_offset > total_lines )); then
        current_offset=0
    fi

    local new_lines=$(( total_lines - current_offset ))
    if (( new_lines <= 0 )); then
        echo "$total_lines" > "${OFFSET_FILE}.invalid"
        return
    fi

    declare -A post_ips
    declare -A invalid_ips
    declare -A invalid_reasons

    while IFS= read -r line; do
        local ip=$(echo "$line" | awk '{print $1}')
        if [[ ! "$ip" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
            continue
        fi

        if echo "$line" | grep -qE '"POST '; then
            post_ips[$ip]=1
        elif ! echo "$line" | grep -qE "$URL_PATTERN"; then
            invalid_ips[$ip]=1
            local req_path=$(echo "$line" | grep -oP '"(GET|POST|HEAD|PUT|DELETE|PATCH|OPTIONS) \K[^ ]*')
            invalid_reasons[$ip]="${req_path:-unknown}"
        fi
    done < <(tail -n +"$((current_offset + 1))" "$LOGFILE" | grep -E '"(GET|POST|HEAD|PUT|DELETE|PATCH|OPTIONS) ')

    echo "$total_lines" > "${OFFSET_FILE}.invalid"

    local banned=0

    for ip in "${!post_ips[@]}"; do
        if is_google_ip "$ip"; then
            echo "[$(date)] SKIP Google POST: $ip" >> "$BANLIST"
            continue
        fi
        if is_already_banned "$ip"; then
            continue
        fi
        ban_ip "$ip" "auto-ban: POST request"
        echo "[$(date)] BANNED (POST): $ip" >> "$BANLIST"
        logger "ufw-ban: Banned $ip for POST request"
        ((banned++))
    done

    for ip in "${!invalid_ips[@]}"; do
        if is_google_ip "$ip"; then
            echo "[$(date)] SKIP Google INVALID: $ip (${invalid_reasons[$ip]})" >> "$BANLIST"
            continue
        fi
        if is_already_banned "$ip"; then
            continue
        fi
        ban_ip "$ip" "auto-ban: invalid path ${invalid_reasons[$ip]}"
        echo "[$(date)] BANNED (INVALID PATH): $ip -> ${invalid_reasons[$ip]}" >> "$BANLIST"
        logger "ufw-ban: Banned $ip for invalid path ${invalid_reasons[$ip]}"
        ((banned++))
    done

    if (( banned > 0 )); then
        echo "[$(date)] Invalid request scan: $banned IPs banned" >> "$BANLIST"
    fi
}

ban_abusive_ips() {
    local now=$(date +%s)
    local since=$(( now - TIMEWINDOW ))

    fetch_google_ranges
    load_google_ranges

    if $USE_IPSET; then
        setup_ipset
    fi

    ban_invalid_requests

    declare -A ip_counts
    declare -A ip_first_seen

    local current_offset=0
    if [[ -f "$OFFSET_FILE" ]]; then
        current_offset=$(cat "$OFFSET_FILE")
    fi

    local total_lines=$(wc -l < "$LOGFILE" 2>/dev/null || echo 0)

    if (( current_offset > total_lines )); then
        current_offset=0
    fi

    local new_lines=$(( total_lines - current_offset ))
    if (( new_lines <= 0 )); then
        return
    fi

    while IFS= read -r line; do
        local ip=$(echo "$line" | awk '{print $1}')
        if [[ ! "$ip" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
            continue
        fi

        local timestamp_raw=$(echo "$line" | grep -oP '\[\K[^\]]+')
        if [[ -n "$timestamp_raw" ]]; then
            local log_ts=$(date -d "$(echo "$timestamp_raw" | sed 's|/| |g; s|:| |')" +%s 2>/dev/null || echo 0)
            if (( log_ts > 0 && log_ts < since )); then
                continue
            fi
        fi

        ip_counts[$ip]=$(( ${ip_counts[$ip]:-0} + 1 ))

    done < <(tail -n +"$((current_offset + 1))" "$LOGFILE" | grep -E "$URL_PATTERN")

    echo "$total_lines" > "$OFFSET_FILE"

    local banned=0
    local skipped=0

    for ip in "${!ip_counts[@]}"; do
        local count=${ip_counts[$ip]}

        if (( count >= THRESHOLD )); then
            if is_google_ip "$ip"; then
                echo "[$(date)] SKIP Google IP: $ip ($count requests)" >> "$BANLIST"
                ((skipped++))
                continue
            fi

            if is_already_banned "$ip"; then
                continue
            fi

            ban_ip "$ip" "auto-ban: $count reqs in ${TIMEWINDOW}s"
            echo "[$(date)] BANNED: $ip ($count requests)" >> "$BANLIST"
            logger "ufw-ban: Banned $ip with $count requests"
            ((banned++))
        fi
    done

    if (( banned > 0 || skipped > 0 )); then
        echo "[$(date)] Scan complete: $banned banned, $skipped skipped (Google), $new_lines lines processed" >> "$BANLIST"
    fi
}

show_status() {
    echo "=== UFW Auto-Ban Status ==="
    echo "Log file: $LOGFILE"
    echo "Threshold: $THRESHOLD requests / ${TIMEWINDOW}s"
    echo "Mode: $( $USE_IPSET && command -v ipset &>/dev/null && echo 'ipset' || echo 'UFW' )"
    echo "Google ranges: $(wc -l < "$GOOGLE_CACHE" 2>/dev/null || echo 'not loaded') CIDRs"
    echo "Log offset: $(cat "$OFFSET_FILE" 2>/dev/null || echo '0') / $(wc -l < "$LOGFILE" 2>/dev/null || echo '0') lines"
    echo ""

    if $USE_IPSET && command -v ipset &>/dev/null && ipset list "$IPSET_NAME" &>/dev/null; then
        echo "=== ipset Banned IPs ==="
        local count=$(ipset list "$IPSET_NAME" | grep -c "^[0-9]")
        echo "Total: $count IPs"
        ipset list "$IPSET_NAME" | grep "^[0-9]" | head -20
        if (( count > 20 )); then
            echo "... and $((count - 20)) more"
        fi
    else
        echo "=== UFW Banned IPs (auto-ban) ==="
        ufw status | grep "auto-ban" || echo "No auto-banned IPs"
    fi

    echo ""
    echo "=== Recent Activity (last 20) ==="
    tail -20 "$BANLIST" 2>/dev/null || echo "No ban history"
}

flush_bans() {
    if $USE_IPSET && command -v ipset &>/dev/null; then
        ipset flush "$IPSET_NAME" 2>/dev/null
        echo "[$(date)] FLUSHED: All ipset bans cleared" >> "$BANLIST"
        echo "All ipset bans cleared."
    else
        echo "Manual UFW cleanup needed. Use: sudo ufw status numbered"
    fi
}

setup_cron() {
    local script_path=$(readlink -f "$0")
    local cron_entry="* * * * * $script_path run >> /var/log/ufw-ban-cron.log 2>&1"

    if crontab -l 2>/dev/null | grep -q "ufw-ban.sh"; then
        echo "Cron job already exists."
    else
        (crontab -l 2>/dev/null; echo "$cron_entry") | crontab -
        echo "Cron job added: runs every minute"
    fi
}

update_google() {
    rm -f "$GOOGLE_CACHE"
    fetch_google_ranges
    load_google_ranges
    echo "Google IP ranges updated: ${#GOOGLE_RANGES[@]} CIDRs loaded"
}

case "${1:-help}" in
    run)
        if [[ $EUID -ne 0 ]]; then
            echo "Error: This script must be run as root (sudo)"
            exit 1
        fi
        if [[ ! -f "$LOGFILE" ]]; then
            echo "Error: Log file not found: $LOGFILE"
            exit 1
        fi
        ban_abusive_ips
        ;;
    status)
        show_status
        ;;
    unban)
        if [[ $EUID -ne 0 ]]; then
            echo "Error: This script must be run as root (sudo)"
            exit 1
        fi
        unban_ip_cmd "$2"
        ;;
    flush)
        if [[ $EUID -ne 0 ]]; then
            echo "Error: This script must be run as root (sudo)"
            exit 1
        fi
        flush_bans
        ;;
    setup)
        if [[ $EUID -ne 0 ]]; then
            echo "Error: This script must be run as root (sudo)"
            exit 1
        fi
        setup_cron
        echo "Setup complete. Script will run every minute via cron."
        ;;
    update-google)
        update_google
        ;;
    *)
        echo "TwitExplorer UFW Auto-Ban Script v2.0"
        echo ""
        echo "Usage: sudo $0 [command]"
        echo ""
        echo "Commands:"
        echo "  run            - Scan new logs and ban abusive IPs"
        echo "  status         - Show banned IPs, stats and recent activity"
        echo "  unban <IP>     - Remove ban for specific IP"
        echo "  flush          - Remove all auto-bans (ipset only)"
        echo "  setup          - Install cron job (runs every minute)"
        echo "  update-google  - Force update Google IP whitelist"
        echo ""
        echo "Config:"
        echo "  Threshold:  $THRESHOLD requests in ${TIMEWINDOW}s"
        echo "  Log:        $LOGFILE"
        echo "  ipset:      $( $USE_IPSET && echo 'enabled' || echo 'disabled' )"
        echo "  Google IPs: auto-updated from gstatic.com (24h cache)"
        echo ""
        echo "Features:"
        echo "  - Query parameter aware URL matching"
        echo "  - Auto-updated Google IP whitelist (goog.json + cloud.json)"
        echo "  - ipset support for 100K+ IP bans"
        echo "  - Incremental log reading (no re-scanning)"
        echo "  - Timestamp-based time window filtering"
        echo "  - Root permission check"
        ;;
esac
