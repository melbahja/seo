<?php
namespace Melbahja\Seo\Sitemap;


/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
enum OutputMode: string
{
	case TEMP   = 'temp';   // write to temporary files first if success then overwrite the files.
	case FILE   = 'file';   // overwrite files on runtime
	case STREAM = 'stream'; // output to resource and and stdout by def
	case MEMORY = 'memory'; // keep in memory only
}
