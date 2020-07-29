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

	/**
	 * Get analytics code
	 * @return string
	 */
	public static function getAnalytics() {
		$analyticsCode = '';
		$analyticsCode .= "\n" . '<!-- GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-576564-4"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag() { dataLayer.push( arguments ); }
	gtag( \'js\', new Date() );
	gtag( \'config\', \'UA-576564-4\', { \'anonymize_ip\': true } );
	gtag( \'config\', \'UA-576564-21\', { \'anonymize_ip\': true } );
</script>';
		if ( !strstr( filter_input( INPUT_SERVER, 'REQUEST_URI' ), '.php' ) ) {
			$analyticsCode .= "\n" . '<!-- TL -->
<script>
	( function() {
		if ( typeof window.fetch === \'function\' ) {
			var w = window.location.href;
			if ( w.indexOf( \'.php\' ) == -1 ) {
				var data = \'action=wikiPageView&url=\' + encodeURIComponent( w );
				window.fetch(
					"/stats/",
					{
						method: \'POST\',
						headers: new Headers(
							{
								\'Content-Type\': \'application/x-www-form-urlencoded\'
							}
						),
						body: data
					}
				);
			}
		}
	} )();
</script>';
		}
		return $analyticsCode;
	}

}
