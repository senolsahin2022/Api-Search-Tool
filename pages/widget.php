<?php
$pageTitle = __('widget_title') . ' - TwitExplorer';
$pageDescription = __('widget_desc');
$canonicalUrl = '/widget';
$currentPage = 'widget';
require __DIR__ . '/../includes/header.php';
?>

<section class="hero-small" style="text-align:center; padding: 40px 20px 20px;">
    <h1 style="font-size: 2.5rem; font-weight: 800; background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 12px;">
        <i class="fa-solid fa-cube" style="-webkit-text-fill-color: initial; color: var(--primary);"></i> <?= e(__('widget_title')) ?>
    </h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto;"><?= e(__('widget_desc')) ?></p>
</section>

<div class="widget-builder">
    <div class="widget-builder-grid">
        <div class="widget-config-panel">
            <h3><i class="fa-solid fa-sliders"></i> <?= e(__('widget_settings')) ?></h3>

            <div class="config-group">
                <label><?= e(__('widget_type')) ?></label>
                <div class="config-tabs" id="widgetTypeTabs">
                    <button class="config-tab active" data-type="user"><i class="fa-solid fa-user"></i> <?= e(__('widget_type_user')) ?></button>
                    <button class="config-tab" data-type="hashtag"><i class="fa-solid fa-hashtag"></i> <?= e(__('widget_type_hashtag')) ?></button>
                    <button class="config-tab" data-type="trends"><i class="fa-solid fa-fire"></i> <?= e(__('widget_type_trends')) ?></button>
                    <button class="config-tab" data-type="search"><i class="fa-solid fa-magnifying-glass"></i> <?= e(__('widget_type_search')) ?></button>
                </div>
            </div>

            <div class="config-group" id="sourceGroup">
                <label id="sourceLabel"><?= e(__('widget_username')) ?></label>
                <div class="config-input-wrap">
                    <span id="sourcePrefix">@</span>
                    <input type="text" id="widgetSource" value="elonmusk" placeholder="elonmusk">
                </div>
            </div>

            <div class="config-group">
                <label><?= e(__('widget_theme')) ?></label>
                <div class="config-tabs" id="themeTabs">
                    <button class="config-tab active" data-theme="dark"><i class="fa-solid fa-moon"></i> <?= e(__('widget_dark')) ?></button>
                    <button class="config-tab" data-theme="light"><i class="fa-solid fa-sun"></i> <?= e(__('widget_light')) ?></button>
                    <button class="config-tab" data-theme="auto"><i class="fa-solid fa-circle-half-stroke"></i> Auto</button>
                </div>
            </div>

            <div class="config-group">
                <label><?= e(__('widget_accent_color')) ?></label>
                <div class="color-picker-row">
                    <div class="color-swatch active" data-color="#1d9bf0" style="background:#1d9bf0"></div>
                    <div class="color-swatch" data-color="#7856ff" style="background:#7856ff"></div>
                    <div class="color-swatch" data-color="#f91880" style="background:#f91880"></div>
                    <div class="color-swatch" data-color="#00ba7c" style="background:#00ba7c"></div>
                    <div class="color-swatch" data-color="#ff6b35" style="background:#ff6b35"></div>
                    <div class="color-swatch" data-color="#ffd700" style="background:#ffd700"></div>
                    <input type="color" id="customColor" value="#1d9bf0" title="Custom color">
                </div>
            </div>

            <div class="config-row">
                <div class="config-group config-half">
                    <label><?= e(__('widget_width')) ?></label>
                    <div class="range-wrap">
                        <input type="range" id="widgetWidth" min="280" max="800" value="400" step="10">
                        <span id="widgetWidthVal">400px</span>
                    </div>
                </div>
                <div class="config-group config-half">
                    <label><?= e(__('widget_height')) ?></label>
                    <div class="range-wrap">
                        <input type="range" id="widgetHeight" min="300" max="900" value="600" step="10">
                        <span id="widgetHeightVal">600px</span>
                    </div>
                </div>
            </div>

            <div class="config-row">
                <div class="config-group config-half">
                    <label><?= e(__('widget_count')) ?></label>
                    <div class="range-wrap">
                        <input type="range" id="widgetCount" min="1" max="20" value="5">
                        <span id="widgetCountVal">5</span>
                    </div>
                </div>
                <div class="config-group config-half">
                    <label><?= e(__('widget_radius')) ?></label>
                    <div class="range-wrap">
                        <input type="range" id="widgetRadius" min="0" max="24" value="16">
                        <span id="widgetRadiusVal">16px</span>
                    </div>
                </div>
            </div>

            <div class="config-group">
                <label><?= e(__('widget_options')) ?></label>
                <div class="config-checks">
                    <label class="check-label"><input type="checkbox" id="showHeader" checked> <?= e(__('widget_show_header')) ?></label>
                    <label class="check-label"><input type="checkbox" id="showMedia" checked> <?= e(__('widget_show_media')) ?></label>
                    <label class="check-label"><input type="checkbox" id="showStats" checked> <?= e(__('widget_show_stats')) ?></label>
                    <label class="check-label"><input type="checkbox" id="showBranding" checked> TwitExplorer <?= e(__('widget_branding')) ?></label>
                </div>
            </div>
        </div>

        <div class="widget-preview-panel">
            <h3><i class="fa-solid fa-eye"></i> <?= e(__('widget_preview')) ?></h3>
            <div class="preview-device-bar">
                <button class="device-btn active" data-device="desktop"><i class="fa-solid fa-desktop"></i></button>
                <button class="device-btn" data-device="tablet"><i class="fa-solid fa-tablet-screen-button"></i></button>
                <button class="device-btn" data-device="mobile"><i class="fa-solid fa-mobile-screen-button"></i></button>
            </div>
            <div class="preview-container" id="previewContainer">
                <div id="widgetPreview" class="widget-live-preview"></div>
            </div>

            <div class="embed-code-section">
                <h4><i class="fa-solid fa-code"></i> <?= e(__('widget_embed_code')) ?></h4>
                <div class="code-block-wrap">
                    <pre id="embedCode" class="code-block"></pre>
                    <button class="copy-btn" id="copyBtn" onclick="copyEmbedCode()">
                        <i class="fa-regular fa-copy"></i> <?= e(__('widget_copy')) ?>
                    </button>
                </div>
                <p class="embed-hint"><i class="fa-solid fa-circle-info"></i> <?= e(__('widget_embed_hint')) ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.widget-builder { margin-top: 20px; }
.widget-builder-grid { display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }
.widget-config-panel, .widget-preview-panel { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; }
.widget-config-panel h3, .widget-preview-panel h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
.widget-config-panel h3 i, .widget-preview-panel h3 i { color: var(--primary); }
.config-group { margin-bottom: 20px; }
.config-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
.config-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
.config-tab { padding: 8px 14px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-secondary); cursor: pointer; font-size: 0.82rem; font-weight: 600; transition: all 0.25s; display: flex; align-items: center; gap: 6px; font-family: inherit; }
.config-tab:hover { border-color: var(--primary); color: var(--text); }
.config-tab.active { background: var(--primary); border-color: var(--primary); color: #fff; }
.config-input-wrap { display: flex; align-items: center; background: var(--bg); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; transition: border-color 0.3s; }
.config-input-wrap:focus-within { border-color: var(--primary); }
.config-input-wrap span { padding: 10px 0 10px 14px; color: var(--text-secondary); font-weight: 600; font-size: 0.95rem; }
.config-input-wrap input { flex: 1; padding: 10px 14px 10px 4px; background: none; border: none; color: var(--text); font-size: 0.95rem; outline: none; font-family: inherit; }
.color-picker-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.color-swatch { width: 32px; height: 32px; border-radius: 50%; cursor: pointer; border: 3px solid transparent; transition: all 0.25s; }
.color-swatch:hover { transform: scale(1.15); }
.color-swatch.active { border-color: var(--text); box-shadow: 0 0 0 2px var(--bg), 0 0 0 4px var(--text); }
#customColor { width: 32px; height: 32px; border: none; border-radius: 50%; cursor: pointer; padding: 0; background: none; }
.config-row { display: flex; gap: 16px; }
.config-half { flex: 1; }
.range-wrap { display: flex; align-items: center; gap: 10px; }
.range-wrap input[type="range"] { flex: 1; accent-color: var(--primary); height: 4px; }
.range-wrap span { font-size: 0.8rem; color: var(--primary); font-weight: 700; min-width: 48px; text-align: right; }
.config-checks { display: flex; flex-direction: column; gap: 10px; }
.check-label { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer; color: var(--text); }
.check-label input { accent-color: var(--primary); width: 16px; height: 16px; }
.preview-device-bar { display: flex; gap: 6px; margin-bottom: 16px; }
.device-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--text-secondary); cursor: pointer; transition: all 0.25s; font-size: 0.9rem; }
.device-btn:hover { border-color: var(--primary); color: var(--text); }
.device-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }
.preview-container { background: repeating-conic-gradient(#1a1a2e 0% 25%, #16161d 0% 50%) 50% / 20px 20px; border-radius: 12px; padding: 24px; display: flex; justify-content: center; align-items: flex-start; min-height: 500px; overflow: auto; }
.embed-code-section { margin-top: 24px; }
.embed-code-section h4 { font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.embed-code-section h4 i { color: var(--primary); }
.code-block-wrap { position: relative; }
.code-block { background: #0d1117; border: 1px solid var(--border); border-radius: 10px; padding: 16px; font-family: 'Fira Code', monospace; font-size: 0.78rem; color: #7ee787; overflow-x: auto; line-height: 1.6; white-space: pre-wrap; word-break: break-all; }
.copy-btn { position: absolute; top: 8px; right: 8px; padding: 6px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text); cursor: pointer; font-size: 0.8rem; font-weight: 600; transition: all 0.25s; display: flex; align-items: center; gap: 6px; font-family: inherit; }
.copy-btn:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
.copy-btn.copied { background: var(--success); border-color: var(--success); color: #fff; }
.embed-hint { font-size: 0.8rem; color: var(--text-secondary); margin-top: 10px; display: flex; align-items: center; gap: 6px; }

@media (max-width: 900px) {
    .widget-builder-grid { grid-template-columns: 1fr; }
    .config-row { flex-direction: column; gap: 0; }
}
</style>

<script>
const WIDGET_BASE = 'https://freedom-x.net';

let config = {
    type: 'user',
    source: 'elonmusk',
    theme: 'dark',
    accent: '#1d9bf0',
    width: 400,
    height: 600,
    count: 5,
    radius: 16,
    showHeader: true,
    showMedia: true,
    showStats: true,
    showBranding: true
};

document.querySelectorAll('#widgetTypeTabs .config-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#widgetTypeTabs .config-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        config.type = btn.dataset.type;
        updateSourceUI();
        updatePreview();
    });
});

document.querySelectorAll('#themeTabs .config-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#themeTabs .config-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        config.theme = btn.dataset.theme;
        updatePreview();
    });
});

document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', () => {
        document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        swatch.classList.add('active');
        config.accent = swatch.dataset.color;
        document.getElementById('customColor').value = config.accent;
        updatePreview();
    });
});

document.getElementById('customColor').addEventListener('input', function() {
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
    config.accent = this.value;
    updatePreview();
});

document.getElementById('widgetSource').addEventListener('input', function() {
    config.source = this.value.replace(/^[@#]/, '');
    clearTimeout(this._debounce);
    this._debounce = setTimeout(() => updatePreview(), 500);
});

['widgetWidth', 'widgetHeight', 'widgetCount', 'widgetRadius'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('input', function() {
        const key = id.replace('widget', '').toLowerCase();
        config[key] = parseInt(this.value);
        document.getElementById(id + 'Val').textContent = this.value + (key === 'count' ? '' : 'px');
        updatePreview();
    });
});

['showHeader', 'showMedia', 'showStats', 'showBranding'].forEach(id => {
    document.getElementById(id).addEventListener('change', function() {
        config[id] = this.checked;
        updatePreview();
    });
});

document.querySelectorAll('.device-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const container = document.getElementById('previewContainer');
        container.style.maxWidth = btn.dataset.device === 'mobile' ? '375px' : btn.dataset.device === 'tablet' ? '768px' : '100%';
        container.style.margin = btn.dataset.device !== 'desktop' ? '0 auto' : '';
    });
});

function updateSourceUI() {
    const group = document.getElementById('sourceGroup');
    const label = document.getElementById('sourceLabel');
    const prefix = document.getElementById('sourcePrefix');
    const input = document.getElementById('widgetSource');

    if (config.type === 'trends') {
        group.style.display = 'none';
    } else {
        group.style.display = '';
        if (config.type === 'user') {
            label.textContent = '<?= e(__('widget_username')) ?>';
            prefix.textContent = '@';
            input.placeholder = 'elonmusk';
        } else if (config.type === 'hashtag') {
            label.textContent = '<?= e(__('widget_hashtag_label')) ?>';
            prefix.textContent = '#';
            input.placeholder = 'bitcoin';
        } else {
            label.textContent = '<?= e(__('widget_search_label')) ?>';
            prefix.textContent = '';
            input.placeholder = '<?= e(__('search_placeholder')) ?>';
        }
    }
}

let previewTimeout;
function updatePreview() {
    clearTimeout(previewTimeout);
    previewTimeout = setTimeout(() => {
        renderPreview();
        updateEmbedCode();
    }, 100);
}

async function renderPreview() {
    const preview = document.getElementById('widgetPreview');
    preview.style.width = config.width + 'px';
    preview.style.maxHeight = config.height + 'px';
    preview.style.borderRadius = config.radius + 'px';
    preview.style.overflow = 'hidden';

    const isDark = config.theme === 'dark' || (config.theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    const bg = isDark ? '#16181c' : '#ffffff';
    const bgCard = isDark ? '#1e2028' : '#f7f9fa';
    const text = isDark ? '#e7e9ea' : '#0f1419';
    const textSec = isDark ? '#71767b' : '#536471';
    const border = isDark ? '#2f3336' : '#eff3f4';

    preview.style.background = bg;
    preview.style.border = `1px solid ${border}`;
    preview.style.fontFamily = "'Inter', -apple-system, BlinkMacSystemFont, sans-serif";
    preview.style.color = text;

    let html = '';

    if (config.showHeader) {
        html += `<div style="padding:16px 20px;border-bottom:1px solid ${border};display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:28px;border-radius:4px;background:${config.accent};"></div>
            <div>
                <div style="font-weight:700;font-size:0.95rem;">${getHeaderTitle()}</div>
                <div style="font-size:0.75rem;color:${textSec};">${getHeaderSub()}</div>
            </div>
        </div>`;
    }

    html += `<div style="padding:12px;overflow-y:auto;max-height:${config.height - (config.showHeader ? 80 : 0) - (config.showBranding ? 40 : 0)}px;" class="widget-scroll">`;

    if (!config.source && config.type !== 'trends') {
        html += `<div style="text-align:center;padding:40px 20px;color:${textSec};font-size:0.9rem;">
            <i class="fa-solid fa-circle-info" style="font-size:2rem;margin-bottom:12px;display:block;color:${config.accent};"></i>
            <?= e(__('widget_enter_source')) ?>
        </div>`;
    } else {
        preview.querySelector('.widget-scroll')?.remove();
        html += `<div style="text-align:center;padding:30px;"><div class="loading"></div></div>`;
    }

    html += '</div>';

    if (config.showBranding) {
        html += `<div style="padding:8px 16px;border-top:1px solid ${border};text-align:center;">
            <a href="${WIDGET_BASE}" target="_blank" style="color:${textSec};text-decoration:none;font-size:0.7rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="${config.accent}"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                TwitExplorer
            </a>
        </div>`;
    }

    preview.innerHTML = html;

    if (config.source || config.type === 'trends') {
        loadPreviewData(preview, isDark, bg, bgCard, text, textSec, border);
    }
}

function getHeaderTitle() {
    const s = config.source || '';
    switch (config.type) {
        case 'user': return `@${s}`;
        case 'hashtag': return `#${s}`;
        case 'trends': return '<?= e(__('trending_topics')) ?>';
        case 'search': return `"${s}"`;
    }
}

function getHeaderSub() {
    switch (config.type) {
        case 'user': return '<?= e(__('last_posts')) ?>';
        case 'hashtag': return '<?= e(__('hashtag_subtitle')) ?>';
        case 'trends': return '<?= e(__('hero_subtitle')) ?>';
        case 'search': return '<?= e(__('search_results')) ?>';
    }
}

async function loadPreviewData(preview, isDark, bg, bgCard, text, textSec, border) {
    try {
        const url = `${location.origin}/widget/api?type=${config.type}&source=${encodeURIComponent(config.source)}&count=${config.count}`;
        const res = await fetch(url);
        const data = await res.json();

        if (!data || data.error) {
            setPreviewContent(preview, `<div style="text-align:center;padding:30px;color:${textSec};">
                <i class="fa-solid fa-triangle-exclamation" style="font-size:1.5rem;margin-bottom:8px;display:block;color:${config.accent};"></i>
                ${data?.error || '<?= e(__('load_error')) ?>'}
            </div>`);
            return;
        }

        let cardsHtml = '';

        if (config.type === 'trends' && data.trends) {
            data.trends.slice(0, config.count).forEach((trend, i) => {
                const name = typeof trend === 'string' ? trend : (trend.name || trend.query || '');
                cardsHtml += `<a href="${WIDGET_BASE}/search?q=${encodeURIComponent(name)}" target="_blank" style="display:block;padding:12px 16px;border-radius:${Math.max(config.radius - 4, 4)}px;background:${bgCard};margin-bottom:8px;text-decoration:none;color:${text};transition:all 0.2s;border:1px solid ${border};" onmouseover="this.style.borderColor='${config.accent}'" onmouseout="this.style.borderColor='${border}'">
                    <div style="font-size:0.7rem;color:${textSec};margin-bottom:2px;">${i + 1} · Trend</div>
                    <div style="font-weight:700;font-size:0.9rem;">${escHtml(name)}</div>
                </a>`;
            });
        } else if (data.tweets) {
            data.tweets.slice(0, config.count).forEach(tweet => {
                const author = tweet.author || tweet.user || {};
                const name = author.name || tweet.name || '';
                const handle = author.handle || author.screen_name || author.username || tweet.screen_name || '';
                const avatar = author.image || author.profile_image_url_https || author.avatar || '';
                const tweetText = tweet.content || tweet.text || tweet.full_text || '';
                const tweetId = tweet.id || tweet.tweet_id || tweet.id_str || '';
                const engagement = tweet.engagement || {};
                const likes = engagement.likes || tweet.favorite_count || tweet.likes || 0;
                const rts = engagement.retweets || tweet.retweet_count || tweet.retweets || 0;
                const media = tweet.media || [];
                const mediaUrl = media.length ? (typeof media[0] === 'string' ? media[0] : (media[0].media_url_https || media[0].url || '')) : '';

                cardsHtml += `<div style="padding:14px 16px;border-radius:${Math.max(config.radius - 4, 4)}px;background:${bgCard};margin-bottom:8px;border:1px solid ${border};cursor:pointer;" onclick="window.open('${WIDGET_BASE}/status/${tweetId}','_blank')">`;

                if (config.showHeader !== false) {
                    cardsHtml += `<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">`;
                    if (avatar) {
                        cardsHtml += `<img src="${escHtml(avatar)}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid ${border};" loading="lazy">`;
                    } else {
                        cardsHtml += `<div style="width:36px;height:36px;border-radius:50%;background:${config.accent};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.9rem;">${name.charAt(0)}</div>`;
                    }
                    cardsHtml += `<div style="flex:1;min-width:0;">
                        <div style="font-weight:700;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(name)}</div>
                        <div style="font-size:0.75rem;color:${textSec};">@${escHtml(handle)}</div>
                    </div></div>`;
                }

                cardsHtml += `<div style="font-size:0.85rem;line-height:1.55;margin-bottom:8px;word-wrap:break-word;">${escHtml(tweetText).substring(0, 280)}</div>`;

                if (config.showMedia && mediaUrl) {
                    cardsHtml += `<div style="border-radius:${Math.max(config.radius - 6, 4)}px;overflow:hidden;margin-bottom:8px;">
                        <img src="${escHtml(mediaUrl)}" style="width:100%;height:auto;display:block;" loading="lazy">
                    </div>`;
                }

                if (config.showStats) {
                    cardsHtml += `<div style="display:flex;gap:20px;font-size:0.75rem;color:${textSec};">
                        <span>❤️ ${formatNum(likes)}</span>
                        <span>🔁 ${formatNum(rts)}</span>
                    </div>`;
                }

                cardsHtml += '</div>';
            });
        }

        if (!cardsHtml) {
            cardsHtml = `<div style="text-align:center;padding:30px;color:${textSec};font-size:0.85rem;"><?= e(__('no_results')) ?></div>`;
        }

        setPreviewContent(preview, cardsHtml);
    } catch (err) {
        setPreviewContent(preview, `<div style="text-align:center;padding:30px;color:${textSec};font-size:0.85rem;"><?= e(__('load_error')) ?></div>`);
    }
}

function setPreviewContent(preview, html) {
    const scrollDiv = preview.querySelector('.widget-scroll');
    if (scrollDiv) {
        scrollDiv.innerHTML = html;
    }
}

function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
}

function formatNum(n) {
    if (!n) return '0';
    if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
    if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
    return n.toString();
}

function updateEmbedCode() {
    const attrs = [
        `data-type="${config.type}"`,
        config.type !== 'trends' ? `data-source="${config.source}"` : '',
        `data-theme="${config.theme}"`,
        config.accent !== '#1d9bf0' ? `data-accent="${config.accent}"` : '',
        config.width !== 400 ? `data-width="${config.width}"` : '',
        config.height !== 600 ? `data-height="${config.height}"` : '',
        config.count !== 5 ? `data-count="${config.count}"` : '',
        config.radius !== 16 ? `data-radius="${config.radius}"` : '',
        !config.showHeader ? 'data-header="false"' : '',
        !config.showMedia ? 'data-media="false"' : '',
        !config.showStats ? 'data-stats="false"' : '',
        !config.showBranding ? 'data-branding="false"' : '',
    ].filter(Boolean).join('\n  ');

    const code = `<!-- TwitExplorer Widget -->\n<div id="twitexplorer-widget"\n  ${attrs}></div>\n<script src="${WIDGET_BASE}/assets/js/widget-embed.js"><\/script>`;
    document.getElementById('embedCode').textContent = code;
}

function copyEmbedCode() {
    const code = document.getElementById('embedCode').textContent;
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.classList.add('copied');
        btn.innerHTML = '<i class="fa-solid fa-check"></i> <?= e(__('widget_copied')) ?>';
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = '<i class="fa-regular fa-copy"></i> <?= e(__('widget_copy')) ?>';
        }, 2000);
    });
}

updatePreview();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
