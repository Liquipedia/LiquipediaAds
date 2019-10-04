<?php

namespace Liquipedia\Ads;

class AdCode {

	public static function get( $code ) {
		global $liquipedia_ads;
		if ( array_key_exists( $code, $liquipedia_ads ) ) {
			return $liquipedia_ads[ $code ];
		}
		return '';
	}

	public static function getStartCode() {
		$startCode = '';

		$startCode .= <<<END_HTML
<script async='async' src='https://securepubads.g.doubleclick.net/tag/js/gpt.js'></script>
<script>
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	var advelvetTargeting = [];
	advelvetTargeting.push((Math.floor(Math.random() * 20) + 1) + "");
	googletag.cmd.push(function () {
		googletag.pubads().setTargeting('advelvet', advelvetTargeting)
		.setTargeting ('url', location.pathname);
	});
</script>
END_HTML;
		return $startCode;
	}

}
