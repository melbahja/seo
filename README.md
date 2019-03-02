# PHP SEO [![Build Status](https://img.shields.io/travis/melbahja/seo/master.svg)](https://travis-ci.org/melbahja/seo) ![PHP from Travis config](https://img.shields.io/travis/php-v/melbahja/seo.svg) [![Twitter](https://img.shields.io/twitter/url/https/github.com/melbahja/seo.svg?style=social)](https://twitter.com/intent/tweet?url=https%3A%2F%2Fgithub.com%2Fmelbahja%2Fseo)

Simple PHP library to help developers 🍻 do better on-page SEO optimization

### PHP SEO features:

- [[👷]](#-generate-schemaorg) **Generate schema.org ld+json**
- [[🛀]](#-meta-tags) **Generate meta tags with twitter and open graph support**
- [[🗺]](#-sitemaps) **Generate sitemaps xml and indexes (supports: 🖺 news, 🖼 images, 📽 videos)**
- [[📤]](#-send-sitemaps-to-search-engines) **Submit new sitempas to 🌐 search engines**
- [[🙈]](#see_composer_json) **No dependencies**
- [[🖧]](#todos) **&& more coming soon...**

## Installation:
```bash
composer require melbahja/seo
```

## Usage:
Check this simple examples. (of course the composer autoload.php file is required)  


#### 👷 Generate schema.org
```php
use Melbahja\Seo\Factory;

$schema = Factory::schema('organization')
            ->url('https://example.com')
            ->logo('https://example.com/logo.png')
                ->contactPoint
                    ->telephone('+1-000-555-1212')
                    ->contactType('customer service');

echo $schema;
```

**Results:** (formatted)
```html
<script type="application/ld+json">
{  
   "@context":"https:\/\/schema.org",
   "@type":"Organization",
   "url":"https:\/\/example.com",
   "logo":"https:\/\/example.com\/logo.png",
   "contactPoint":{  
      "@type":"ContactPoint",
      "telephone":"+1-000-555-1212",
      "contactType":"customer service"
   }
}
</script>
```

```php
use Melbahja\Seo\Factory;

$schema = Factory::schema('book')
            ->name('The Book Name')
            ->url('https://example.com/books/the-book')
            ->author
                ->set('@type', 'Person')
                ->name('J.D. Jhon')
            ->getRoot();

echo json_encode($schema, JSON_PRETTY_PRINT);
```
**Results:**
```json
{
    "@context": "https:\/\/schema.org",
    "@type": "Book",
    "name": "The Book Name",
    "url": "https:\/\/example.com\/books\/the-book",
    "author": {
        "@type": "Person",
        "name": "J.D. Jhon"
    }
}
```

```php
use Melbahja\Seo\Factory;

$schema = Factory::schema('product')
            ->image(['https://example.com/image.jpeg', 'https://example.com/2.jpeg'])
            ->name('The Product Name')
            ->description('Product description...')
            ->sku('12828127112')
            ->brand->set('@type', 'Thing')->name('Brand Name')
            ->getParent()->aggregateRating->ratingValue("4.4")->ratingCount("89")
            ->getParent()->review(
            [
                'reviewRating' => 
                [
                    '@type' => 'Rating',
                    'ratingValue' => '4',
                    'bestRating' => '5'
                ],

                'author' =>
                [
                    '@type' => 'Person',
                    'name' => "Mohamed ELbahja"
                ]
            ])
            ->offers
                ->set('@type', 'AggregateOffer')
                ->lowPrice('119.99')
                ->highPrice('200.99')
                ->priceCurrency('USD')
                ->availability('https://schema.org/InStock')
                ->offerCount('100');

echo $schema;

```

**Results:**
```html
<script type="application/ld+json">
{  
   "@context":"https:\/\/schema.org",
   "@type":"Product",
   "image":[  
      "https:\/\/example.com\/image.jpeg",
      "https:\/\/example.com\/2.jpeg"
   ],
   "name":"The Product Name",
   "description":"Product description...",
   "sku":"12828127112",
   "brand":{  
      "@type":"Thing",
      "name":"Brand Name"
   },
   "aggregateRating":{  
      "@type":"AggregateRating",
      "ratingValue":"4.4",
      "ratingCount":"89"
   },
   "review":{  
      "reviewRating":{  
         "@type":"Rating",
         "ratingValue":"4",
         "bestRating":"5"
      },
      "author":{  
         "@type":"Person",
         "name":"Mohamed ELbahja"
      }
   },
   "offers":{  
      "@type":"AggregateOffer",
      "lowPrice":"119.99",
      "highPrice":"200.99",
      "priceCurrency":"USD",
      "availability":"https:\/\/schema.org\/InStock",
      "offerCount":"100"
   }
}
</script>
```

#### 🛀 Meta Tags

```php
use Melbahja\Seo\Factory;

$metatags = Factory::metaTags(
[
	'title' => 'My new article',
	'description' => 'My new article about how php is awesome',
	'keywords' => 'php, programming',
	'robots' => 'index, nofollow',
	'author' => 'Mohamed Elbahja'
]);

echo $metatags;

```

**Results:**
```html
<meta name="title" content="My new article" />
<meta name="description" content="My new article about how php is awesome" />
<meta name="keywords" content="php, programming" />
<meta name="robots" content="index, nofollow" />
<meta name="author" content="Mohamed Elbahja" />
<meta property="twitter:title" content="My new article" />
<meta property="twitter:description" content="My new article about how php is awesome" />
<meta property="og:title" content="My new article" />
<meta property="og:description" content="My new article about how php is awesome" />
```

```php
use Melbahja\Seo\Factory;

$metatags = Factory::metaTags();

$metatags->meta('author', 'Mohamed Elabhja')
		->meta('title', 'PHP SEO')
		->meta('description', 'This is my description')
		->image('https://avatars3.githubusercontent.com/u/8259014')
		->mobile('https://m.example.com')
		->url('https://example.com')
		->shortlink('https://git.io/phpseo')
		->amp('https://amp.example.com')
		->facebook('prop', 'propValue example og')
		->twitter('prop', 'propValue example twitter');

echo $metatags;
```

**Results:**
```html
<meta name="author" content="Mohamed Elabhja" />
<meta name="title" content="PHP SEO" />
<meta name="description" content="This is my description" />
<link rel="alternate" media="only screen and (max-width: 640px)" href="https://m.example.com" />
<link rel="canonical" href="https://example.com" />
<link rel="shortlink" href="https://git.io/phpseo" />
<link rel="amphtml" href="https://amp.example.com" />
<meta property="twitter:title" content="PHP SEO" />
<meta property="twitter:description" content="This is my description" />
<meta property="twitter:card" content="summary_large_image" />
<meta property="twitter:image" content="https://avatars3.githubusercontent.com/u/8259014" />
<meta property="twitter:url" content="https://example.com" />
<meta property="twitter:prop" content="propValue example twitter" />
<meta property="og:title" content="PHP SEO" />
<meta property="og:description" content="This is my description" />
<meta property="og:image" content="https://avatars3.githubusercontent.com/u/8259014" />
<meta property="og:url" content="https://example.com" />
<meta property="og:prop" content="propValue example og" />
```

#### 🗺 Sitemaps
```php
$yourmap = Factory::sitemap(string $url, array $options = []): SitemapIndexInterface
```
| Option name   | Description   									| Required ? 	| Default 		| 
| ------------- | ------------- 									| --------- 	| -------- 		|
| save_path     | Generated sitemaps storage path 					| YES 			| 	 			|
| sitemaps_url  | Sitemap index custom url for generated sitemaps 	| NO 			| $url 			|
| index_name 	| Custom sitemap index name      					| NO 			| sitemap.xml 	|

##### Simple Example
```php
use Melbahja\Seo\Factory;

$sitemap = Factory::sitemap('https://example.com', ['save_path' => '/path/to_save/files']);

$sitemap->links('blog.xml', function($map) 
{
    $map->loc('/blog')->freq('daily')->priority('0.8')
        ->loc('/blog/my-new-article')->freq('weekly')->lastMode('2019-03-01')
        ->loc('/اهلا-بالعالم')->freq('weekly');
    $map->loc('/blog/hello')->freq('monthly');
});

// return bool
// throws SitemapException if save_path options not exists
$sitemap->save();
```

**Results:** (📂 in: /path/to_save/files/)

📁: sitemap.xml (formatted)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://example.com/blog.xml</loc>
        <lastmod>2019-03-01T14:38:02+01:00</lastmod>
    </sitemap>
</sitemapindex>
```

📁: blog.xml (formatted)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
      <loc>https://example.com/blog</loc>
      <changefreq>daily</changefreq>
      <priority>0.8</priority>
    </url>
    <url>
      <loc>https://example.com/blog/my-new-article</loc>
      <changefreq>weekly</changefreq>
      <lastmod>2019-03-01T00:00:00+01:00</lastmod>
    </url>
    <url>
      <loc>https://example.com/%D8%A7%D9%87%D9%84%D8%A7-%D8%A8%D8%A7%D9%84%D8%B9%D8%A7%D9%84%D9%85</loc>
      <changefreq>weekly</changefreq>
    </url>
    <url>
      <loc>https://example.com/blog/hello</loc>
      <changefreq>monthly</changefreq>
    </url>
</urlset>
```

##### Multipe Sitemaps && Images
```php
use Melbahja\Seo\Factory;

$sitemap = Factory::sitemap('https://example.com');

// Instead of passing save_path to the factory you can set it later via setSavePath
// also $sitemap->getSavePath() method to get the current save_path
$sitemap->setSavePath('your_save/path');

// changing sitemap index name
$sitemap->setIndexName('index.xml');

// For images you need to pass a option images => true
$sitemap->links(['name' => 'blog.xml', 'images' => true], function($map) 
{
    $map->loc('/blog')->freq('daily')->priority('0.8')
        ->loc('/blog/my-new-article')
            ->freq('weekly')
            ->lastMode('2019-03-01')
            ->image('/uploads/image.jpeg', ['caption' => 'My caption'])
        ->loc('/اهلا-بالعالم')->freq('weekly');

    // image(string $url, array $options = []), image options: caption, geo_location, title, license
    // see References -> images   
    $map->loc('/blog/hello')->freq('monthly')->image('https://cdn.example.com/image.jpeg');
});

// another file
$sitemap->links('blog_2.xml', function($map) 
{
	// Mabye you need to loop through posts form your database ?
	foreach (range(0, 4) as $i)
	{
		$map->loc("/posts/{$i}")->freq('weekly')->priority('0.7');
	}
});

$sitemap->save();

```

**Results**

📁: index.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://example.com/blog.xml</loc>
        <lastmod>2019-03-01T15:13:22+01:00</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://example.com/blog_2.xml</loc>
        <lastmod>2019-03-01T15:13:22+01:00</lastmod>
    </sitemap>
</sitemapindex>

```

📁: blog.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>https://example.com/blog</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://example.com/blog/my-new-article</loc>
        <changefreq>weekly</changefreq>
        <lastmod>2019-03-01T00:00:00+01:00</lastmod>
        <image:image>
            <image:caption>My caption</image:caption>
            <image:loc>https://example.com/uploads/image.jpeg</image:loc>
        </image:image>
    </url>
    <url>
        <loc>https://example.com/%D8%A7%D9%87%D9%84%D8%A7-%D8%A8%D8%A7%D9%84%D8%B9%D8%A7%D9%84%D9%85</loc>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>https://example.com/blog/hello</loc>
        <changefreq>monthly</changefreq>
        <image:image>
            <image:loc>https://cdn.example.com/image.jpeg</image:loc>
        </image:image>
    </url>
</urlset>
```

📁: blog_2.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://example.com/posts/0</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>https://example.com/posts/1</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>https://example.com/posts/2</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>https://example.com/posts/3</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>https://example.com/posts/4</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
</urlset>
```

##### Sitemap with videos
```php
$sitemap = Factory::sitemap('https://example.com')
                ->setSavePath('./storage/sitemaps')
                ->setSitemapsUrl('https://example.com/sitemaps')
                ->setIndexName('index.xml');

$sitemap->links(['name' => 'posts.xml', 'videos' => true], function($map) 
{
	$map->loc('/posts/clickbait-video')->video('My Clickbait Video title', 
	[
		// or thumbnail_loc 
		'thumbnail' => 'https://example.com/thumbnail.jpeg',
		'description' => 'My description',
		// player_loc or content_loc one of them is required
		'player_loc' => 'https://example.com/embed/81287127'

		// for all availabe options see References -> videos
	]);

	$map->loc('posts/bla-bla');
});

$sitemap->save();
```
**Results**

📁: index.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://example.com/sitemaps/posts.xml</loc>
        <lastmod>2019-03-01T15:30:02+01:00</lastmod>
    </sitemap>
</sitemapindex>
```
**Note:** lastmod in sitemap index files are generated automatically

📁: posts.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    <url>
        <loc>https://example.com/posts/clickbait-video</loc>
        <video:video>
            <video:description>My description</video:description>
            <video:player_loc>https://example.com/embed/81287127</video:player_loc>
            <video:title>My Clickbait Video title</video:title>
            <video:thumbnail_loc>https://example.com/thumbnail.jpeg</video:thumbnail_loc>
        </video:video>
    </url>
    <url>
        <loc>https://example.com/posts/bla-bla</loc>
    </url>
</urlset>
```

##### News Sitemaps

```php
use Melbahja\Seo\Factory;

$sitemap = Factory::sitemap('https://example.com',
[
	// You can also customize your options by passing array to the factory like this
	'save_path' => './path',
	'sitemaps_url' => 'https://example.com/maps',
	'index_name' => 'news_index.xml'
]);

$sitemap->news('my_news.xml', function($map) 
{
    // publication: name, language
    // Google quote about the name: "It must exactly match the name as 
    // it appears on your articles on news.google.com"
    $map->setPublication('PHP NEWS', 'en');

    $map->loc('/news/12')->news(
    [
       'title' => 'PHP 8 Released',
       'publication_date' => '2019-03-01T15:30:02+01:00',
    ]);

    $map->loc('/news/13')->news(
    [
        'title' => 'PHP 8 And High Performance',
        'publication_date' => '2019-04-01T15:30:02+01:00'
    ]);
});

$sitemap->save();
```

**Results**

📁: news_index.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://example.com/maps/my_news.xml</loc>
        <lastmod>2019-03-01T15:57:10+01:00</lastmod>
    </sitemap>
</sitemapindex>
```

📁: my_news.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- Generated by https://git.io/phpseo -->
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:news="https://www.google.com/schemas/sitemap-news/0.9">
    <url>
        <loc>https://example.com/news/12</loc>
        <news:news>
            <news:publication>
                <news:name>PHP NEWS</news:name>
                <news:language>en</news:language>
            </news:publication>
            <news:title>PHP 8 Released</news:title>
            <news:publication_date>2019-03-01T15:30:02+01:00</news:publication_date>
        </news:news>
    </url>
    <url>
        <loc>https://example.com/news/13</loc>
        <news:news>
            <news:publication>
                <news:name>PHP NEWS</news:name>
                <news:language>en</news:language>
            </news:publication>
            <news:title>PHP 8 And High Performance</news:title>
            <news:publication_date>2019-04-01T15:30:02+01:00</news:publication_date>
        </news:news>
    </url>
</urlset>
```

**Google quote:** ⚠ "If you submit your News sitemap before your site has been reviewed and approved by our team, you may receive errors." ⚠


#### 🤖 Send Sitemaps To Search Engines

According to the sitemaps protocol, search engines should have a url that allow you to inform them about your new sitemap files. like: <searchengine_URL>/ping?sitemap=sitemap_url

```php
use Melbahja\Seo\Factory;

// the void method send() will inform via CURL: google, bing and yandex about your new file
Factory::ping()->send('https://example.com/sitemap_file.xml');

```

## TODOs:

New features coming in v1.1
- Add robots.txt builder
- Add validation for image options
- Add support for video restriction
- Add more tests
- Add a simple integration for frameworks (🍮cakephp and 🔦laravel)
- Add a better documentation
- Your suggestions [Open new issue 🤔]

## Why >= PHP7.1 ?
\- Why you are using an old version ?

## References
- [Sitemaps protocol (https://www.sitemaps.org/protocol.html)](https://www.sitemaps.org/protocol.html)
- [Build Sitemaps (https://support.google.com/webmasters/answer/183668?hl=en)](https://support.google.com/webmasters/answer/183668?hl=en)
- [News Sitemaps (https://support.google.com/webmasters/answer/74288)](https://support.google.com/webmasters/answer/74288)
- [Image Sitempas (https://support.google.com/webmasters/answer/178636)](https://support.google.com/webmasters/answer/178636)
- [Video Sitemaps (https://support.google.com/webmasters/answer/80471)](https://support.google.com/webmasters/answer/80471)
- [Mobile (https://developers.google.com/search/mobile-sites/mobile-seo/other-devices)](https://developers.google.com/search/mobile-sites/mobile-seo/other-devices)

## Glad you made it here
⭐⭐Star it⭐⭐

## License:
[MIT](https://github.com/melbahja/seo/blob/master/LICENSE) Copyright (c) 2019 Mohamed Elbahja