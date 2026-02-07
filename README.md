# PHP SEO

The SEO library for PHP is a simple and powerful PHP library to help developers 🍻 do better on-page SEO optimizations.

[![Build Status](https://github.com/melbahja/seo/workflows/Test/badge.svg)](https://github.com/melbahja/seo/actions?query=workflow%3ATest)
[![GitHub license](https://img.shields.io/github/license/melbahja/seo)](https://github.com/melbahja/seo/blob/master/LICENSE)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/melbahja/seo)
![Packagist Version](https://img.shields.io/packagist/v/melbahja/seo)
[![Twitter](https://img.shields.io/twitter/url/https/github.com/melbahja/seo.svg?style=social)](https://twitter.com/intent/tweet?url=https%3A%2F%2Fgithub.com%2Fmelbahja%2Fseo)


### PHP SEO features:

- [[👷]](#-generate-schemaorg) **Generate Rich Results schema.org ld+json**
- [[🛀]](#-meta-tags) **Generate Meta Tags with X (Twitter) and Open Graph Support**
- [[🌐]](#-sitemaps) **Generate XML Sitemaps (supports: 📰 News Sitemaps, 🖼 Images Sitemaps, 📹 Video Sitemaps, Index Sitemaps)**
- [[📤]](#-indexing-api) **IndexNow and Google Indexing API**
- [✅] **Schema Rich Results Validator**
- [[🧩]](https://github.com/melbahja/seo/blob/master/composer.json) **Zero Dependencies**

## Installation:
```bash
composer require melbahja/seo
```

## Documentation
You can read the docs <a href="https://elbahja.me/docs/seo/" target="_blank">Here</a>.

## Usage:

Check this simple examples.

#### 👷 Generate schema.org
```php
use Melbahja\Seo\Schema;
use Melbahja\Seo\Schema\Thing;
use Melbahja\Seo\Schema\Organization;

$schema = new Schema(
    new Organization([
        'url'          => 'https://example.com',
        'logo'         => 'https://example.com/logo.png',
        'contactPoint' => new Thing(type: 'ContactPoint', props: [
            'telephone' => '+1-000-555-1212',
            'contactType' => 'customer service'
        ])
    ])
);

echo $schema;
```

**Results:** (formatted)
```html
<script type="application/ld+json">
{
  "@type": "Organization",
  "@context": "https://schema.org",
  "url": "https://example.com",
  "logo": "https://example.com/logo.png",
  "contactPoint": {
    "@type": "ContactPoint",
    "@context": "https://schema.org",
    "telephone": "+1-000-555-1212",
    "contactType": "customer service"
  }
}
</script>
```

```php
use Melbahja\Seo\Schema;
use Melbahja\Seo\Schema\Thing;
use Melbahja\Seo\Schema\CreativeWork\WebPage;

$product = new Thing(type: 'Product');
$product->name  = "Foo Bar";
$product->sku   = "sk12";
$product->image = "/image.jpeg";
$product->description = "testing";
$product->offers = new Thing(type: 'Offer', props: [
    'availability' => 'https://schema.org/InStock',
    'priceCurrency' => 'USD',
    "price" => "119.99",
    'url' => 'https://gool.com',
]);

$webpage = new WebPage([
    '@id' => "https://example.com/product/#webpage",
    'url' => "https://example.com/product",
    'name' => 'Foo Bar',
]);


$schema = new Schema(
    $product,
    $webpage,
);

echo json_encode($schema, JSON_PRETTY_PRINT);
```

**Results:**
```json
{
    "@context": "https:\/\/schema.org",
    "@graph": [
        {
            "@type": "Product",
            "@context": "https:\/\/schema.org",
            "name": "Foo Bar",
            "sku": "sk12",
            "image": "\/image.jpeg",
            "description": "testing",
            "offers": {
                "@type": "Offer",
                "@context": "https:\/\/schema.org",
                "availability": "https:\/\/schema.org\/InStock",
                "priceCurrency": "USD",
                "price": "119.99",
                "url": "https:\/\/gool.com"
            }
        },
        {
            "@type": "WebPage",
            "@context": "https:\/\/schema.org",
            "@id": "https:\/\/example.com\/product\/#webpage",
            "url": "https:\/\/example.com\/product",
            "name": "Foo Bar"
        }
    ]
}
```

#### 🛀 Meta Tags

```php
use Melbahja\Seo\MetaTags;

$metatags = new MetaTags();

$metatags
        ->title('PHP SEO')
        ->description('This is my description')
        ->meta('author', 'Mohamed Elbahja')
        ->image('https://avatars3.githubusercontent.com/u/8259014')
        ->mobile('https://m.example.com')
        ->canonical('https://example.com')
        ->shortlink('https://git.io/phpseo')
        ->amp('https://apm.example.com')
        ->robots(['index', 'follow', 'max-snippet' => -1])
        ->robots(botName: 'bingbot', options: ['index', 'nofollow'])
        ->feed("https://example.com/feed.rss")
        ->verification("google", "token_value")
        ->verification("yandex", "token_value")
        ->hreflang("de", "https://de.example.com")
        ->og("type", "website")
        ->twitter("creator", "Mohamed Elbahja");
        // ->schema($schema)

echo $metatags;

```

**Results:**
```html
<title>PHP SEO</title>
<meta name="title" content="PHP SEO" />
<meta name="description" content="This is my description" />
<meta name="author" content="Mohamed Elbahja" />
<meta name="robots" content="index, follow, max-snippet:-1" />
<meta name="bingbot" content="index, nofollow" />
<meta name="google-site-verification" content="token_value" />
<meta name="yandex-site-verification" content="token_value" />
<link href="https://m.example.com" rel="alternate" media="only screen and (max-width: 640px)" />
<link rel="canonical" href="https://example.com" />
<link rel="shortlink" href="https://git.io/phpseo" />
<link rel="amphtml" href="https://apm.example.com" />
<link rel="alternate" type="application/rss+xml" href="https://example.com/feed.rss" />
<link rel="alternate" href="https://de.example.com" hreflang="de" />
<meta property="og:title" content="PHP SEO" />
<meta property="og:description" content="This is my description" />
<meta property="og:image" content="https://avatars3.githubusercontent.com/u/8259014" />
<meta property="og:type" content="website" />
<meta property="twitter:title" content="PHP SEO" />
<meta property="twitter:description" content="This is my description" />
<meta property="twitter:card" content="summary_large_image" />
<meta property="twitter:image" content="https://avatars3.githubusercontent.com/u/8259014" />
<meta property="twitter:creator" content="Mohamed Elbahja" />
```


# 🗺 Sitemaps

Generate XML sitemaps with support for images, videos, news, and localized URLs.

## Basic Usage

```php
use Melbahja\Seo\Sitemap;

$sitemap = new Sitemap(
    baseUrl: 'https://example.com',
    saveDir: '/path/to_save/files',
);

$sitemap->links('blog.xml', function($map)
{
    $map->loc('/blog')
            ->changeFreq('daily')
            ->priority(0.8)
            ->loc('/blog/my-new-article')
            ->changeFreq('weekly')
            ->lastMod('2024-01-15')
            ->loc('/اهلا-بالعالم')
            ->changeFreq('weekly');

    $map->loc('/blog/hello')->changeFreq('monthly');
});

$sitemap->render();
```

## Options

| Option | Description | Required | Default |
| --- | --- | --- | --- |
| `saveDir` | Generated sitemaps storage path | Yes | \-  |
| `sitemapBaseUrl` | Custom URL for generated sitemaps | No  | Base URL |
| `indexName` | Custom sitemap index name | No  | sitemap.xml |
| `mode` | Output mode (FILE, MEMORY, STREAM, TEMP) | No  | TEMP |

## URL Methods

```php
$builder->loc('/page')               // URL path relative or absolute
        ->priority(0.8)              // Priority 0.0-1.0
        ->changeFreq('weekly')       // always, hourly, daily, weekly, monthly, yearly, never
        ->lastMod('2024-01-15')      // Last modified date in string or unix ts
        ->image('/image.jpg')        // Add image (requires 'images' => true)
        ->video('Title', [...])      // Add video (requires 'videos' => true)
        ->alternate('/es/page', 'es'); // Add hreflang alternate
```

## Advanced Features

### Image Sitemaps

```php
$sitemap->links(['name' => 'gallery.xml', 'images' => true], function($builder)
{
    $builder->loc('/gallery/1')
            ->image('/images/photo1.jpg', [
                'title' => 'Photo Title',
                'caption' => 'Photo caption'
            ]);
});
```

### Video Sitemaps

```php
$sitemap->links(['name' => 'videos.xml', 'videos' => true], function($builder)
{
    $builder->loc('/video/page')
            ->video('Video Title', [
                'thumbnail' => '/thumb.jpg',
                'description' => 'Video description',
                'content_loc' => '/video.mp4'
            ]);
});
```

### News Sitemaps

```php
use Melbahja\Seo\Sitemap\NewsBuilder;

$sitemap->news('news.xml', function(NewsBuilder $builder)
{
    $builder->setPublication('Your News', 'en');

    $builder->loc('/article/1')
            ->news([
                'title' => 'Article Title',
                'publication_date' => '2024-01-15T10:00:00Z',
                'keywords' => 'news, breaking'
            ]);
});
```

### Multilingual Sitemaps

```php
$sitemap->links(['name' => 'multilang.xml', 'localized' => true], function($builder)
{
    $builder->loc('/page')
            ->alternate('/es/page', 'es')
            ->alternate('/fr/page', 'fr');
});
```

## Output Modes

### TEMP Mode (Default)

```php
$sitemap = new Sitemap('https://example.com',
[
    'saveDir' => './storage',
    'mode' => OutputMode::TEMP
]);
$sitemap->render(); // Saves to temp dir and save to disk only on generation success.
```

### File Mode

```php
$sitemap = new Sitemap('https://example.com',
[
    'saveDir' => './storage',
    'mode' => OutputMode::FILE
]);
$sitemap->render(); // Saves to disk
```

### Memory Mode

```php
$sitemap = new Sitemap('https://example.com', [
    'mode' => OutputMode::MEMORY
]);
$xml = $sitemap->render(); // Returns XML string
```

### Stream Mode

```php
$stream = fopen('sitemap.xml', 'w');
$builder = new LinksBuilder(
    baseUrl: 'https://example.com',
    stream: $stream, // defaults to stdout
    mode: OutputMode::STREAM,
);
$builder->loc('/page')->render();
fclose($stream);
```

## Complete Example

```php
$sitemap = new Sitemap(baseUrl: 'https://example.com', options: [
    'saveDir' => './sitemaps',
    'indexName' => 'sitemap-index.xml'
]);

// Regular pages y can just pass array of links
$sitemap->links('pages.xml', ['/', '/about', '/contact']);

// Products with images
$sitemap->links(['name' => 'products.xml', 'images' => true], function($builder)
{
    $builder->loc('/product/123')
            ->priority(0.9)
            ->image('/product-main.jpg', ['title' => 'Product Image']);
});

// News section
$sitemap->news('news.xml', function($builder)
{
    $builder->setPublication('Tech News', 'en');
    $builder->loc('/article/1')
            ->news(['title' => 'New Article', 'publication_date' => date('c')]);
});

// Generate everything
$sitemap->render();
// Creates: sitemap-index.xml, pages.xml, products.xml, news.xml
```

### Indexing API

Submit URLs to search engines for instant indexing using Google Indexing API and IndexNow protocol.

#### Google Indexing API

```php
use Melbahja\Seo\Indexing\GoogleIndexer;
use Melbahja\Seo\Indexing\URLIndexingType;

$indexer = new GoogleIndexer('your-google-access-token');

// Index single URL
$indexer->submitUrl('https://www.example.com/page');

// Index multiple URLs
$indexer->submitUrls([
    'https://www.example.com/page1',
    'https://www.example.com/page2'
]);

// Delete URL from index
$indexer->submitUrl('https://www.example.com/deleted-page', URLIndexingType::DELETE);
```

#### IndexNow Protocol

```php
use Melbahja\Seo\Indexing\IndexNowIndexer;

$indexer = new IndexNowIndexer('your-indexnow-api-key');

// Submit to all supported engines
$indexer->submitUrl('https://www.example.com/page');

// Submit multiple URLs
$indexer->submitUrls([
    'https://www.example.com/page1',
    'https://www.example.com/page2'
]);
```

## AI LLMs.txt Support

If you find LLMs.txt valuable for your use case, contributions are welcome! Feel free to submit a PR.

## Sponsors

Special thanks to friends who support this work financially:

[![EvoluData](https://www.evoludata.com/display208)](https://www.evoludata.com)


## References
- [Sitemaps protocol (https://www.sitemaps.org/protocol.html)](https://www.sitemaps.org/protocol.html)
- [Build Sitemaps (https://support.google.com/webmasters/answer/183668?hl=en)](https://support.google.com/webmasters/answer/183668?hl=en)
- [News Sitemaps (https://support.google.com/webmasters/answer/74288)](https://support.google.com/webmasters/answer/74288)
- [Image Sitemaps (https://support.google.com/webmasters/answer/178636)](https://support.google.com/webmasters/answer/178636)
- [Video Sitemaps (https://support.google.com/webmasters/answer/80471)](https://support.google.com/webmasters/answer/80471)
- [Mobile (https://developers.google.com/search/mobile-sites/mobile-seo/other-devices)](https://developers.google.com/search/mobile-sites/mobile-seo/other-devices)


## License
[MIT](https://github.com/melbahja/seo/blob/master/LICENSE) Copyright (c) Mohamed Elbahja
