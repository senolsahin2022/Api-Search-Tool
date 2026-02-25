# TwitExplorer - Sosyal Medya Keşif Sitesi

## Overview
PHP-based social media explorer that displays Twitter/X trends, user profiles, tweet search results, hashtag-based content, video downloads, and embeddable widgets using an external API proxy. Supports 6 languages (TR, EN, AR, ZH, RU, FA).

## Architecture
- **Language**: PHP 8.2
- **Server**: PHP built-in development server
- **Styling**: Custom CSS (dark theme), Font Awesome icons, Google Fonts (Inter)
- **API**: External REST API at `autumn-bush-ac99.senolsahin2022.workers.dev` with auth header
- **Tweet API**: `xx.senolsahin2022.workers.dev` for individual tweet fetching
- **Deployment**: Apache2 + Cloudflare Flexible SSL on `freedom-x.net`

## Project Structure
```
index.php              - Main router (handles URL routing)
.htaccess              - Apache rewrite rules, security headers, attack blocking
sitemap.xml.php        - Dynamic sitemap with trends, users, hashtags, hreflang
robots.txt             - Search engine crawl rules
includes/
  api.php              - API helper functions (apiRequest, getUser, searchPosts, getTweet, etc.)
  header.php           - Shared HTML header with nav, search bar, SEO meta tags, hreflang
  footer.php           - Shared HTML footer with cookie consent
  tweet_card.php       - Reusable tweet card component with media/video support
  lang.php             - Multi-language translations (TR, EN, AR, ZH, RU, FA)
pages/
  home.php             - Homepage with trends, FAQ, JSON-LD schema
  search.php           - Search results page
  user.php             - User profile page with tweets and FAQ
  hashtag.php          - Hashtag results page
  post.php             - Individual tweet/status page
  downloader.php       - Video downloader tool
  widget.php           - Widget builder page (Elfsight-style configurator)
  widget-api.php       - Widget JSON API endpoint (CORS-enabled)
  404.php              - Not found page
assets/
  css/style.css        - Main stylesheet (dark theme)
  js/app.js            - Client-side JavaScript (animations)
  js/widget-embed.js   - Self-contained embeddable widget script
  images/og-image.png  - OG social sharing image (1200x630)
  favicon.svg          - Site favicon
```

## Routes
- `/` - Homepage with trending topics
- `/search?q=query` - Search results (redirects @user to /user/, #tag to /hashtag/)
- `/user/{username}` - User profile and tweets
- `/hashtag/{tag}` - Hashtag tweet results
- `/status/{id}` - Individual tweet page
- `/downloader` - Video downloader
- `/widget` - Widget builder (configurator UI)
- `/widget/api` - Widget data API (JSON, CORS-enabled)
- `/sitemap.xml` - Dynamic XML sitemap

## Widget System
- Builder page at `/widget` with live preview and embed code generation
- Supports 4 widget types: User Feed, Hashtag Feed, Trends, Search
- Customizable: theme (dark/light/auto), accent color, dimensions, border radius, post count
- Toggle options: header, media, stats, branding
- Embed via single `<script>` tag + `<div>` with data attributes
- Widget API at `/widget/api` serves JSON with CORS headers for cross-origin embedding

## API Endpoints Used
- `GET /api/trends` - Returns trending topics
- `GET /api/user?user={username}` - Returns user tweets with author info
- `GET /api/search?q={query}` - Returns search results
- `GET /api/hashtag?tag={tag}` - Returns hashtag tweets
- `GET /?id={tweet_id}` (xx domain) - Returns individual tweet data
- All require header: `X-Pentest-Auth: authorized-pentest-2026`

## SEO Features
- Dynamic og:type per page (website/profile/article)
- Dynamic og:image (tweet media on status pages)
- JSON-LD structured data (WebSite, Organization, FAQPage, Person, Article)
- Hreflang alternate links for 6 languages
- Dynamic sitemap with trending content

## Security (.htaccess)
- 22+ attack vector blocking (SQL injection, XSS, SSRF, shell/backdoor, directory traversal)
- Cloudflare Flexible SSL support (X-Forwarded-Proto)
- Security headers (X-Frame-Options, X-Content-Type-Options, etc.)
- Asset whitelist by file extension

## Recent Changes
- 2026-02-25: Widget builder system added (Elfsight-style, 4 types, 6 languages, embeddable JS)
- 2026-02-25: Fixed /assets/favicon.svg 403 error (.htaccess asset rules updated)
- 2026-02-25: SEO enhancements (OG image PNG, dynamic sitemap, JSON-LD Organization schema)
- 2026-02-13: Initial build - full site with trends, search, user profiles, hashtags, SEO
