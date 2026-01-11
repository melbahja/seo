# CHANGELOG

## v3 - Jan/2026

### Breaking Changes:
* [  Robots  ] [CHANGED] `bot()` method changed to `addRule()` - params structure is different
* [  Robots  ] [CHANGED] `sitemap()` method renamed to `addSitemap()`
* [  Robots  ] [CHANGED] `delay` array key renamed to `crawlDelay`
* [  Robots  ] [  NEW  ] `addComment()` - adds comments to robots.txt
* [  Robots  ] [  NEW  ] `saveTo()` - saves to file
* [  Robots  ] [  NEW  ] Implements `Stringable` interface
* [  Robots  ] [REMOVED] Robots copyrights header removed
* [ Indexing ] [REMOVED] Sitemap Ping class, /ping?sitemap deprecated/removed by major search engines.
* [ Indexing ] [CHANGED] Refactored indexer classes!
* [ MetaTags ] [CHANGED] Return type changed from `MetaTags` to `self` for all methods
* [ MetaTags ] [CHANGED] Constructor supports array syntax for `og`, `twitter`, `link`, `meta`
* [ MetaTags ] [CHANGED] `robots()` now accepts string|array for options with associative array support
* [ MetaTags ] [CHANGED] `build()` now sanitizes attribute names to prevent XSS
* [ MetaTags ] [  NEW  ] `verification()` - adds search engine verification meta tags
* [ MetaTags ] [  NEW  ] `feed()` - adds RSS/Atom feed links
* [ MetaTags ] [  NEW  ] `pagination()` - adds prev/next/first/last pagination links
* [ MetaTags ] [  NEW  ] `hreflangs()` - batch method for multiple hreflang links with x-default support
* [ MetaTags ] [  NEW  ] `articleMeta()` - adds article published/modified time and author
* [ MetaTags ] [  NEW  ] `schema()` - adds schema object to be rendered with meta tags