(function() {
    'use strict';

    var container = document.getElementById('twitexplorer-widget');
    if (!container) return;

    var scriptTag = document.querySelector('script[src*="widget-embed.js"]');
    var origin = '';
    if (scriptTag) {
        var src = scriptTag.getAttribute('src');
        var match = src.match(/^(https?:\/\/[^\/]+)/);
        origin = match ? match[1] : '';
    }
    if (!origin) origin = 'https://freedom-x.net';

    var cfg = {
        type: container.getAttribute('data-type') || 'user',
        source: container.getAttribute('data-source') || '',
        theme: container.getAttribute('data-theme') || 'dark',
        accent: container.getAttribute('data-accent') || '#1d9bf0',
        width: parseInt(container.getAttribute('data-width')) || 400,
        height: parseInt(container.getAttribute('data-height')) || 600,
        count: parseInt(container.getAttribute('data-count')) || 5,
        radius: parseInt(container.getAttribute('data-radius')) || 16,
        showHeader: container.getAttribute('data-header') !== 'false',
        showMedia: container.getAttribute('data-media') !== 'false',
        showStats: container.getAttribute('data-stats') !== 'false',
        showBranding: container.getAttribute('data-branding') !== 'false'
    };

    var isDark = cfg.theme === 'dark' || (cfg.theme === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    var colors = isDark
        ? { bg: '#16181c', card: '#1e2028', text: '#e7e9ea', textSec: '#71767b', border: '#2f3336' }
        : { bg: '#ffffff', card: '#f7f9fa', text: '#0f1419', textSec: '#536471', border: '#eff3f4' };

    var r = Math.max(cfg.radius - 4, 4);

    var wrapper = document.createElement('div');
    wrapper.style.cssText = 'width:' + cfg.width + 'px;max-width:100%;height:' + cfg.height + 'px;background:' + colors.bg + ';border:1px solid ' + colors.border + ';border-radius:' + cfg.radius + 'px;overflow:hidden;font-family:Inter,-apple-system,BlinkMacSystemFont,sans-serif;color:' + colors.text + ';display:flex;flex-direction:column;';

    var headerH = 0;
    if (cfg.showHeader) {
        headerH = 56;
        var title = cfg.type === 'user' ? '@' + cfg.source : cfg.type === 'hashtag' ? '#' + cfg.source : cfg.type === 'trends' ? 'Trending Topics' : '"' + cfg.source + '"';
        var sub = cfg.type === 'user' ? 'Recent Posts' : cfg.type === 'hashtag' ? 'Latest Posts' : cfg.type === 'trends' ? 'What\'s happening' : 'Search Results';
        wrapper.innerHTML += '<div style="padding:14px 18px;border-bottom:1px solid ' + colors.border + ';display:flex;align-items:center;gap:10px;flex-shrink:0;"><div style="width:6px;height:26px;border-radius:3px;background:' + cfg.accent + ';"></div><div><div style="font-weight:700;font-size:0.9rem;">' + esc(title) + '</div><div style="font-size:0.7rem;color:' + colors.textSec + ';">' + esc(sub) + '</div></div></div>';
    }

    var brandingH = 0;
    var scrollArea = document.createElement('div');
    scrollArea.style.cssText = 'flex:1;overflow-y:auto;padding:10px;';
    scrollArea.innerHTML = '<div style="display:flex;justify-content:center;padding:40px;"><div style="width:28px;height:28px;border:3px solid ' + colors.border + ';border-top-color:' + cfg.accent + ';border-radius:50%;animation:twe-spin 0.7s linear infinite;"></div></div>';
    wrapper.appendChild(scrollArea);

    if (cfg.showBranding) {
        brandingH = 36;
        wrapper.innerHTML += '<div style="padding:6px 14px;border-top:1px solid ' + colors.border + ';text-align:center;flex-shrink:0;"><a href="' + origin + '" target="_blank" rel="noopener" style="color:' + colors.textSec + ';text-decoration:none;font-size:0.65rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:4px;"><svg width="10" height="10" viewBox="0 0 24 24" fill="' + cfg.accent + '"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>TwitExplorer</a></div>';
    }

    var style = document.createElement('style');
    style.textContent = '@keyframes twe-spin{to{transform:rotate(360deg)}}#twitexplorer-widget *{box-sizing:border-box;}#twitexplorer-widget div::-webkit-scrollbar{width:4px;}#twitexplorer-widget div::-webkit-scrollbar-track{background:transparent;}#twitexplorer-widget div::-webkit-scrollbar-thumb{background:' + colors.border + ';border-radius:2px;}';
    container.appendChild(style);
    container.appendChild(wrapper);

    var apiUrl = origin + '/widget/api?type=' + cfg.type + '&source=' + encodeURIComponent(cfg.source) + '&count=' + cfg.count;

    fetch(apiUrl).then(function(res) { return res.json(); }).then(function(data) {
        if (!data || data.error) {
            scrollArea.innerHTML = '<div style="text-align:center;padding:30px;color:' + colors.textSec + ';font-size:0.85rem;">' + (data && data.error ? esc(data.error) : 'Could not load data') + '</div>';
            return;
        }

        var html = '';

        if (cfg.type === 'trends' && data.trends) {
            data.trends.slice(0, cfg.count).forEach(function(trend, i) {
                var name = typeof trend === 'string' ? trend : (trend.name || trend.query || '');
                html += '<a href="' + origin + '/search?q=' + encodeURIComponent(name) + '" target="_blank" style="display:block;padding:11px 14px;border-radius:' + r + 'px;background:' + colors.card + ';margin-bottom:6px;text-decoration:none;color:' + colors.text + ';border:1px solid ' + colors.border + ';transition:border-color 0.2s;" onmouseover="this.style.borderColor=\'' + cfg.accent + '\'" onmouseout="this.style.borderColor=\'' + colors.border + '\'">' +
                    '<div style="font-size:0.65rem;color:' + colors.textSec + ';margin-bottom:1px;">' + (i + 1) + ' · Trend</div>' +
                    '<div style="font-weight:700;font-size:0.85rem;">' + esc(name) + '</div></a>';
            });
        } else if (data.tweets) {
            data.tweets.slice(0, cfg.count).forEach(function(tweet) {
                var a = tweet.author || tweet.user || {};
                var name = a.name || tweet.name || '';
                var handle = a.handle || a.screen_name || a.username || tweet.screen_name || '';
                var avatar = a.image || a.profile_image_url_https || a.avatar || '';
                var txt = tweet.content || tweet.text || tweet.full_text || '';
                var tid = tweet.id || tweet.tweet_id || tweet.id_str || '';
                var eng = tweet.engagement || {};
                var likes = eng.likes || tweet.favorite_count || tweet.likes || 0;
                var rts = eng.retweets || tweet.retweet_count || tweet.retweets || 0;
                var media = tweet.media || [];
                var mediaUrl = media.length ? (typeof media[0] === 'string' ? media[0] : (media[0].media_url_https || media[0].url || '')) : '';

                html += '<div style="padding:12px 14px;border-radius:' + r + 'px;background:' + colors.card + ';margin-bottom:6px;border:1px solid ' + colors.border + ';cursor:pointer;transition:border-color 0.2s;" onclick="window.open(\'' + origin + '/status/' + tid + '\',\'_blank\')" onmouseover="this.style.borderColor=\'' + cfg.accent + '\'" onmouseout="this.style.borderColor=\'' + colors.border + '\'">';

                html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;">';
                if (avatar) {
                    html += '<img src="' + esc(avatar) + '" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:1px solid ' + colors.border + ';" loading="lazy" onerror="this.style.display=\'none\'">';
                } else {
                    html += '<div style="width:32px;height:32px;border-radius:50%;background:' + cfg.accent + ';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;">' + esc(name.charAt(0)) + '</div>';
                }
                html += '<div style="flex:1;min-width:0;"><div style="font-weight:700;font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + esc(name) + '</div><div style="font-size:0.7rem;color:' + colors.textSec + ';">@' + esc(handle) + '</div></div></div>';

                html += '<div style="font-size:0.82rem;line-height:1.5;margin-bottom:7px;word-wrap:break-word;overflow:hidden;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;">' + esc(txt) + '</div>';

                if (cfg.showMedia && mediaUrl) {
                    html += '<div style="border-radius:' + Math.max(r - 2, 4) + 'px;overflow:hidden;margin-bottom:7px;"><img src="' + esc(mediaUrl) + '" style="width:100%;height:auto;display:block;max-height:200px;object-fit:cover;" loading="lazy" onerror="this.parentElement.style.display=\'none\'"></div>';
                }

                if (cfg.showStats) {
                    html += '<div style="display:flex;gap:16px;font-size:0.7rem;color:' + colors.textSec + ';"><span>\u2764\uFE0F ' + fmtNum(likes) + '</span><span>\uD83D\uDD01 ' + fmtNum(rts) + '</span></div>';
                }

                html += '</div>';
            });
        }

        if (!html) {
            html = '<div style="text-align:center;padding:30px;color:' + colors.textSec + ';font-size:0.85rem;">No data available</div>';
        }

        scrollArea.innerHTML = html;
    }).catch(function() {
        scrollArea.innerHTML = '<div style="text-align:center;padding:30px;color:' + colors.textSec + ';font-size:0.85rem;">Connection error</div>';
    });

    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function fmtNum(n) {
        if (!n) return '0';
        if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
        if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
        return n.toString();
    }
})();
