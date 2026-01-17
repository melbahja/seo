<?php
namespace Melbahja\Seo;

use Closure;
use Traversable;
use Melbahja\Seo\{
	Sitemap\SitemapIndex,
	Sitemap\NewsBuilder,
	Sitemap\LinksBuilder,
	Sitemap\IndexBuilder,
	Sitemap\OutputMode,
	Sitemap\SitemapUrl,
	Exceptions\SitemapException,
	Interfaces\SitemapBuilderInterface
};

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class Sitemap implements SitemapBuilderInterface
{

	/**
	 * Sitemaps map of generated files.
	 *
	 * @var array
	 */
	protected array $sitemaps  = [];

	/**
	 * Sitemaps data generators.
	 *
	 * @var array
	 */
	protected array $dSources  = [];


	/**
	 * Initialize new sitemap builder
	 */
	public function __construct(

		/**
		 * @param string $baseUrl  The base URL for sitemap urls.
		 */
		public readonly string $baseUrl,

		/**
		 * @param string|null $saveDir Local directory where generated XML files will be written
		 */
		public readonly ?string $saveDir = null,

		/**
		 * @param string $indexName Filename for the root sitemap index file
		 */
		public readonly string $indexName = 'sitemap.xml',

		/**
		 * @param string|null $sitemapBaseUrl The generated sitemaps base url in the index sitemap defaults to $baseUrl
		 */
		private readonly ?string $sitemapBaseUrl = null,

		/**
		 * Sitemap builders map
		 *
		 * @var array<string, SitemapBuilderInterface>
		 */
		private array $builders = [
			'news'  => NewsBuilder::class,
			'links' => LinksBuilder::class,
			'index' => IndexBuilder::class
		],

		/**
		 * The output mode of generated sitemaps.
		 *
		 * @var OutputMode
		 */
		protected OutputMode $mode = OutputMode::TEMP,

		/**
		 * Pretty print indent
		 * @param string|null
		 */
		protected readonly ?string $indent = ' '
	) {}


	/**
	 * Register a builder alias
	 *
	 * @param  string $alias   the builder alias name
	 * @param  string $builder The actual builder class namespace.
	 * @return self
	 */
	public function register(string $alias, string $builder): self
	{
		if (is_subclass_of($builder, SitemapBuilderInterface::class) === false) {
			throw new \InvalidArgumentException('The builder must implement SitemapBuilderInterface');
		}

		$this->builders[$alias] = $builder;
		return $this;
	}


	/**
	 * Get sitemaps base url
	 *
	 * @return string
	 */
	public function getSitemapBaseUrl(): string
	{
		return $this->sitemapBaseUrl ?? $this->baseUrl;
	}

	/**
	 * Set sitemaps to a file name.
	 *
	 * @param  string|null $uriPath URI path to render the sitemap into, or null will return the xml
	 * @return bool|string boolean when uri oath passed or a generated xml as string
	 */
	public function render(?string $uriPath = null): bool|string
	{
		$index =  new IndexBuilder(
			mode:     $this->mode,
			baseUrl:  $this->getSitemapBaseUrl(),
			filePath: $this->saveDir . DIRECTORY_SEPARATOR . $this->indexName,
			options:  ['indent' => $this->indent],
		);

		foreach ($this->sitemaps as $k => $sitemap)
		{
			if ($sitemap->mode !== OutputMode::MEMORY) {
				$this->generate($k)->render();
			}

			$index->addSitemap($k);
		}

		return $index->render($uriPath);
	}

	public function __toString(): string
	{
		return $this->render();
	}

	/**
	 * Generate sitemaps
	 *
	 * @param  array    $name    the name of registred sitemap.
	 * @return SitemapBuilderInterface retuns the generated sitemap object.
	 */
	public function generate(string $name): SitemapBuilderInterface
	{
		$dataSource = $this->dSources[$name] ?? null;
		if ($dataSource === null || isset($this->sitemaps[$name]) === false) {
			throw new SitemapException("There is no data source or registred sitemap for {$name}!");
		}

		$builder = $this->sitemaps[$name];

		if (is_callable($dataSource)) {
			call_user_func_array($dataSource, [$builder]);
			return $builder;
		}

		foreach ($dataSource as $item)
		{
			// in case of array or even Traversable yeild as string
			if (is_string($item)) {
				$builder->loc($item);
				continue;
			}

			if (($item instanceof SitemapUrl) === false) {
				throw new SitemapException("Traversable yeilds can be strings or SitemapUrl object only");
			}

			$builder->addItem($item);
		}

		return $builder;
	}

	/**
	 * Initialize sitemaps generator from alias
	 *
	 * @param  string $alias    The builder alias (e.g. 'links', 'news', 'index', 'yourBuilderAlias')
	 * @param  array  $args      [$config, $dataSource] where $config is string|array
	 * @return SitemapIndexInterface
	 */
	public function __call(string $alias, array $args): self
	{
		$builder = $this->builders[$alias] ?? null;

		if ($builder === null) {
			throw new SitemapException("The builder alias: '{$alias}' not found.");
		}

		if (count($args) !== 2) {
			throw new SitemapException("{$alias}() expects 2 arguments: [string|array \$options, callable|Traversable|array \$dataSource]");
		}

		$options = is_string($args[0]) ? [ 'name' => $args[0] ] : $args[0];
		if (isset($options['name']) === false) {
			throw new SitemapException("The {$alias} name is missing!");
		}

		if (isset($this->sitemaps[$options['name']])) {
			throw new SitemapException("The sitemap {$options['name']} already registred!");
		}

		if (is_array($args[1]) === false && is_callable($args[1]) === false && ($args[1] instanceof Traversable) === false) {
			 throw new SitemapException("{$alias}() Argument[1] must be array, callable, or Traversable");
		}

		$name = $options['name'];
		unset($options['name']);

		$options['indent'] = $options['indent'] ?? $this->indent;

		$this->dSources[$name] = $args[1];
		$this->sitemaps[$name] = new $builder(
			mode:     $this->mode,
			baseUrl:  $this->baseUrl,
			filePath: $this->saveDir . DIRECTORY_SEPARATOR . $name,
			options:  $options,
		);

		return $this;
	}
}
