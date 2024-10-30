<?php
/**
 * @package l10n cache
 * @version 0.3
 * @see http://wordpress.org/extend/plugins/l10n-cache/
 *
 * $Id: l10n-cache.php 351932 2011-02-26 17:21:22Z macbre $
 */
/*
Plugin Name: l10n cache
Description: Improves site performance by providing caching for localisation objects (*.mo files)
Author: Maciej Brencz
Version: 0.3
Author URI: http://macbre.net
*/

/**
 * Inspired by @see http://devel.kostdoktorn.se/cache-translation-object
 */
class l10nCache {
	const VERSION = '0.3';

	private $cached = false;
	private $cacheDir;
	private $cacheFile;

	private $domain;

	private $time;

	/**
	 * Setup Wordpress "hooks"
	 */
	function __construct() {
		// set up path to cache directory
		$this->cacheDir = WP_CONTENT_DIR . '/cache';

		// try to load from cache
		add_filter('override_load_textdomain', array(&$this, 'loadFromCache'), 10 /* priority */, 3 /* number of arguments */);

		// store l10n object when WP is begin shut down
		add_action('shutdown', array(&$this, 'storeInCache'));

		// add caching info to the footer
		add_action('wp_footer', array(&$this, 'addFooter'));
		add_action('admin_footer', array(&$this, 'addFooter'));
	}

	/**
	 * Try to load MO object from cache
	 */
	public function loadFromCache($override, $domain, $mofile) {
		global $l10n, $wp_version;

		// remember current domain (i.e. language code)
		$this->domain = $domain;

		// caching location
		$hash = md5($mofile . $wp_version . self::VERSION);
		$this->cacheFile = "{$this->cacheDir}/l10n-cache-{$this->domain}-{$hash}.cache";

		$time = microtime(true);

		// try to load from cache
		if (file_exists($this->cacheFile)) {
			$obj = unserialize(file_get_contents($this->cacheFile));

			if ($obj instanceof MO) {
				$l10n[$domain] = $obj;

				$this->cached = true;

				// store load time
				$this->time = round(microtime(true) - $time, 4);

				// tell WP that we've loaded MO object from cache
				return true;
			}
		}

		// cache miss
		return false;
	}

	/**
	 * Store MO object in cache when WP is unloading domain
	 */
	public function storeInCache() {
		global $l10n;

		if (!$this->cached) {
			// get localisation object
			$obj = isset($l10n[$this->domain]) ? $l10n[$this->domain] : false;

			if ($obj instanceof MO) {
				// perform object's cleanup
				unset($obj->_gettext_select_plural_form);

				// store it
				file_put_contents($this->cacheFile, serialize($obj));
			}
		}
	}

	/**
	 * Add info to the footer
	 */
	public function addFooter() {
		if ($this->cached) {
			$info = "object loaded from cache in {$this->time} s";
		}
		else {
			$info = 'object stored in cache';
		}

		echo "\n<!-- l10n cache: {$info} -->\n";
	}
}

// setup caching class
new l10nCache();