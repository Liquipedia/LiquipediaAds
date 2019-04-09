<?php

namespace Liquipedia\Ads;

use OutputPage;
use Parser;
use PPFrame;

class Hooks {

	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleStyles( 'ext.liquipediaads' );
		return true;
	}

	public static function onBruinenSidebar( $skin ) {
		if (
			!(
			!$skin->getUser()->isAnon()
			&& (
			$skin->getTitle()->getNamespace() === NS_SPECIAL
			|| in_array( $skin->getRequest()->getVal( 'action', 'view' ), [ 'edit', 'submit', 'delete', 'protect' ] )
			)
			)
		) {
			global $liquipedia_ads;
			echo '<div id="sidebar-ad" class="navigation-not-searchable">';
			echo $liquipedia_ads[ '300x250_SATF' ];
			echo '</div>';
		}
		return true;
	}

	public static function onBruinenTop( $skin ) {
		if (
			!(
			!$skin->getUser()->isAnon()
			&& (
			$skin->getTitle()->getNamespace() === NS_SPECIAL
			|| in_array( $skin->getRequest()->getVal( 'action', 'view' ), [ 'edit', 'submit', 'delete', 'protect' ] )
			)
			)
		) {
			global $liquipedia_ads;
			echo '<div id="top-ad" class="navigation-not-searchable">';
			echo $liquipedia_ads[ '728x90_ATF' ];
			echo '</div>';
		}
		return true;
	}

	public static function onBruinenStartCode( OutputPage $out ) {
		global $liquipedia_ads;

		$tlAdCode = '';

		$tlAdCode .= <<<END_HTML
<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
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

		$tlAdCode .= $liquipedia_ads[ 'header' ];

		$out->addHeadItem( 'tlads', $tlAdCode );
		return true;
	}

	public static function onBruinenEndCode( $includeDir, $skin ) {
		include( $includeDir . '/../TeamLiquidFooter.inc' );
		return true;
	}

	public static function onParserBeforeStrip( &$parser, &$text, &$mStripState ) {
		// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
		if ( $parser->getTitle()->getNamespace() === NS_MAIN && $parser->getOptions()->getEnableLimitReport() ) {
			global $liquipedia_ads;
			$adbox_code = '<div class="content-ad">' . $liquipedia_ads[ '728x90_BTF' ] . '</div>';
			$has_added_adbox = false;
			// Check for headings as defined, if found, place ad there
			if ( preg_match_all( "/^==([^=]+)==\\s*$/m", $text, $findings ) ) {
				// $number_of_adboxes = 1;
				$pages = wfMessage( 'adbox-headings' )->plain();
				$key_headings = explode( "\n", $pages );
				foreach ( $key_headings as $key_heading ) {
					foreach ( $findings[ 1 ] as $findingid => $finding ) {
						if ( !$has_added_adbox ) {
							if ( trim( $finding ) == trim( $key_heading, "* \t\n\r\0\x0B" ) ) {
								$text = preg_replace( '/^' . str_replace( '/', '\/', preg_quote( $findings[ 0 ][ $findingid ] ) ) . '$/m', $findings[ 0 ][ $findingid ] . "\n" . $parser->insertStripItem( $adbox_code ) . "\n", $text, 1 );
								$has_added_adbox = true;
								break 2;
							}
						}
					}
				}
			}
			// If no heading found, and more than 3 headings, place in the middle of the page
			if ( !$has_added_adbox && count( $findings[ 0 ] ) >= 3 ) {
				$text = preg_replace( '/^' . str_replace( '/', '\/', preg_quote( $findings[ 0 ][ ceil( ( count( $findings[ 0 ] ) - 1) / 2 ) ] ) ) . '$/m', $findings[ 0 ][ ceil( ( count( $findings[ 0 ] ) - 1 ) / 2 ) ] . "\n" . $parser->insertStripItem( $adbox_code ) . "\n", $text, 1 );
				$has_added_adbox = true;
			}
		}
		return true;
	}

	public static function onParserAfterTidy( &$parser, &$text ) {
		// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
		if ( $parser->getTitle()->getNamespace() === NS_MAIN && $parser->getOptions()->getEnableLimitReport() ) {
			global $liquipedia_ads;
			$text .= "\n" . '<div class="content-ad">' . $liquipedia_ads[ '728x90_FOOTER' ] . '</div>';
		}
		return true;
	}

	public static function onBruinenBodyFirst() {
		global $liquipedia_ads;
		return true;
	}

	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'adbox', 'Liquipedia\\Ads\\Hooks::adboxRender' );
		return true;
	}

	public static function adboxRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		global $liquipedia_ads;
		$code = '<div class="navigation-not-searchable">' . $liquipedia_ads[ '300x250_ATF' ] . '</div>';
		return [ trim( $code ), 'markerType' => 'nowiki' ];
	}

}
