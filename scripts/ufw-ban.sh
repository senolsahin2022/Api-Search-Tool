#!/bin/bash

LOGFILE="/var/log/apache2/access.log"
BANLIST="/var/log/ufw-banned.log"
THRESHOLD=50
TIMEWINDOW=60

GOOGLE_RANGES=(
    "64.233.160.0/19"
    "66.102.0.0/20"
    "66.249.64.0/19"
    "72.14.192.0/18"
    "74.125.0.0/16"
    "108.177.8.0/21"
    "108.177.96.0/19"
    "130.211.0.0/22"
    "172.217.0.0/19"
    "172.217.32.0/20"
    "172.217.128.0/19"
    "172.217.160.0/20"
    "172.217.192.0/19"
    "172.253.56.0/21"
    "172.253.112.0/20"
    "209.85.128.0/17"
    "216.58.192.0/19"
    "216.239.32.0/19"
    "34.64.0.0/10"
    "35.190.247.0/24"
    "35.191.0.0/16"
    "142.250.0.0/15"
    "142.251.0.0/16"
)

WHITELIST_FILE="/etc/ufw-google-whitelist.txt"

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
    ufw status | grep -q "$ip" && return 0
    return 1
}

URL_PATTERN='(GET|POST|HEAD) (/|/search|/user/|/hashtag/|/status/[0-9]+|/downloader|/widget|/assets/)'

ban_abusive_ips() {
    local now=$(date +%s)
    local since=$(( now - TIMEWINDOW ))
    local since_date=$(date -d "@$since" '+%d/%b/%Y:%H:%M:%S' 2>/dev/null || date -r "$since" '+%d/%b/%Y:%H:%M:%S' 2>/dev/null)

    declare -A ip_counts

    while IFS= read -r line; do
        local ip=$(echo "$line" | awk '{print $1}')
        if [[ "$ip" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
            ip_counts[$ip]=$(( ${ip_counts[$ip]:-0} + 1 ))
        fi
    done < <(grep -E "$URL_PATTERN" "$LOGFILE" 2>/dev/null | tail -5000)

    for ip in "${!ip_counts[@]}"; do
        local count=${ip_counts[$ip]}

        if (( count >= THRESHOLD )); then
            if is_google_ip "$ip"; then
                echo "[$(date)] SKIP Google IP: $ip ($count requests)" >> "$BANLIST"
                continue
            fi

            if is_already_banned "$ip"; then
                continue
            fi

            ufw deny from "$ip" to any comment "auto-ban: $count reqs in ${TIMEWINDOW}s"
            echo "[$(date)] BANNED: $ip ($count requests)" >> "$BANLIST"
            logger "ufw-ban: Banned $ip with $count requests"
        fi
    done
}

show_status() {
    echo "=== UFW Auto-Ban Status ==="
    echo "Log file: $LOGFILE"
    echo "Threshold: $THRESHOLD requests / ${TIMEWINDOW}s"
    echo ""
    echo "=== Currently Banned IPs (auto-ban) ==="
    ufw status | grep "auto-ban" || echo "No auto-banned IPs"
    echo ""
    echo "=== Recent Bans ==="
    tail -20 "$BANLIST" 2>/dev/null || echo "No ban history"
}

unban_ip() {
    local ip="$1"
    if [[ -z "$ip" ]]; then
        echo "Usage: $0 unban <IP>"
        exit 1
    fi
    ufw delete deny from "$ip"
    echo "[$(date)] UNBANNED: $ip (manual)" >> "$BANLIST"
    echo "Unbanned: $ip"
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

case "${1:-run}" in
    run)
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
        unban_ip "$2"
        ;;
    setup)
        setup_cron
        echo "Setup complete. Script will run every minute via cron."
        ;;
    *)
        echo "TwitExplorer UFW Auto-Ban Script"
        echo ""
        echo "Usage: sudo $0 [command]"
        echo ""
        echo "Commands:"
        echo "  run      - Scan logs and ban abusive IPs (default)"
        echo "  status   - Show banned IPs and recent activity"
        echo "  unban IP - Remove ban for specific IP"
        echo "  setup    - Install cron job (runs every minute)"
        echo ""
        echo "Config:"
        echo "  THRESHOLD=$THRESHOLD requests in ${TIMEWINDOW}s window"
        echo "  Log: $LOGFILE"
        echo "  Google IPs are whitelisted automatically"
        ;;
esac
