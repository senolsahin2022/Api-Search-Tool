# TwitExplorer - Sosyal Medya Keşif Sitesi

## Overview
PHP-based social media explorer that displays Twitter/X trends, user profiles, tweet search results, and hashtag-based content using an external API proxy.

## Architecture
- **Language**: PHP 8.2
- **Server**: PHP built-in development server
- **Styling**: Custom CSS (dark theme), Font Awesome icons, Google Fonts (Inter)
- **API**: External REST API at `autumn-bush-ac99.senolsahin2022.workers.dev` with auth header

## Project Structure
```
index.php              - Main router (handles URL routing)
includes/
  api.php              - API helper functions (apiRequest, getUser, searchPosts, etc.)
  header.php           - Shared HTML header with nav, search bar, SEO meta tags
  footer.php           - Shared HTML footer
  tweet_card.php       - Reusable tweet card component
pages/
  home.php             - Homepage with trends and quick search tags
  search.php           - Search results page
  user.php             - User profile page with tweets
  hashtag.php          - Hashtag results page
  404.php              - Not found page
assets/
  css/style.css        - Main stylesheet (dark theme)
  js/app.js            - Client-side JavaScript (animations)
```

## Routes
- `/` - Homepage with trending topics
- `/search?q=query` - Search results (redirects @user to /user/, #tag to /hashtag/)
- `/user/{username}` - User profile and tweets
- `/hashtag/{tag}` - Hashtag tweet results

## API Endpoints Used
- `GET /api/trends` - Returns trending topics (array of strings)
- `GET /api/user?user={username}` - Returns user tweets with author info
- `GET /api/search?q={query}` - Returns search results
- `GET /api/hashtag?tag={tag}` - Returns hashtag tweets
- All require header: `X-Pentest-Auth: authorized-pentest-2026`

## Recent Changes
- 2026-02-13: Initial build - full site with trends, search, user profiles, hashtags, SEO
