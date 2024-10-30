=== l10n cache ===
Author: macbre
Tags: l10n, cache, performance
Requires at least: 3.0
Tested up to: 3.0.5
Stable tag: 0.3

Improves site performance by providing caching for localisation objects (*.mo files).

== Description ==

This extension has been inspired by http://devel.kostdoktorn.se/cache-translation-object and gives up to 5x speed increase when using Wordpress with "custom" (i.e. non English) localisation files.

MO object created by Wordpress is cached and stored in the `/wp-content/cache/` directory and then used when loading localisations.

== Installation ==

1. Upload `l10n-cache.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress