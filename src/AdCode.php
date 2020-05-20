<?php

namespace Liquipedia\LiquipediaAds;

class AdCode {

	/**
	 * Get code for ad slot
	 * @param string $code
	 * @return string
	 */
	public static function get( $code ) {
		// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
		global $liquipedia_ads;
		if ( array_key_exists( $code, $liquipedia_ads ) ) {
			return $liquipedia_ads[ $code ];
		}
		return '';
	}

	/**
	 * Get start code for ads
	 * @return string
	 */
	public static function getStartCode() {
		$startCode = '';
		$startCode .= '<script async="async" src="'
			. 'https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
<script>
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	googletag.cmd.push( function () {
		googletag.pubads().setTargeting( \'url\', location.pathname );
	} );
</script>';
		return $startCode;
	}

}
