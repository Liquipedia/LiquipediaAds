<?php

namespace Liquipedia\Ads;

use MediaWiki\MediaWikiServices;
use OutputPage;
use Parser;
use PPFrame;

class Hooks {

	private static function shouldShowAds( $user, $title, $request = null ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();

		if ( $title->isSpecialPage() ) {
			$blacklistedPages = $config->get( 'LiquipediaAdsBlacklistedPages' );
			foreach ( $blacklistedPages as $page ) {
				if ( $title->isSpecial( $page ) ) { // Special pages that should never have ads
					return false;
				}
			}
		}

		if ( $user->isAnon() ) { // Anonymous people get ads
			return true;
		} elseif ( !is_null( $request ) && in_array( $request->getVal( 'action', 'view' ), [ 'edit', 'submit', 'delete', 'protect' ] ) ) { // No ads on certain utility pages for logged in users
			return false;
		} elseif ( $title->isSpecialPage() ) {
			$whitelistedPages = $config->get( 'LiquipediaAdsWhitelistedPages' );
			foreach ( $whitelistedPages as $page ) {
				if ( $title->isSpecial( $page ) ) { // Special pages that should always have ads
					return true;
				}
			}
			return false; // Other special pages should not have ads when logged in, since they are mostly used for editors
		}

		return true;
	}

	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleStyles( 'ext.liquipediaads' );
		return true;
	}

	public static function onBruinenSidebar( $skin, &$value ) {
		if ( self::shouldShowAds( $skin->getUser(), $skin->getTitle(), $skin->getRequest() ) ) {
			$value .= '<div id="sidebar-ad" class="navigation-not-searchable">';
			$value .= AdCode::get( '300x250_SATF' );
			$value .= '</div>';
		}
		return true;
	}

	public static function onBruinenTop( $skin, &$value ) {
		if ( self::shouldShowAds( $skin->getUser(), $skin->getTitle(), $skin->getRequest() ) ) {
			$value .= '<div id="top-ad" class="navigation-not-searchable">';
			$value .= AdCode::get( '728x90_ATF' );
			$value .= '</div>';
		}
		return true;
	}

	public static function onBruinenStartCode( OutputPage $out ) {
		if ( self::shouldShowAds( $out->getUser(), $out->getTitle(), $out->getRequest() ) ) {
			$tlAdCode = AdCode::getStartCode();
			$tlAdCode .= AdCode::get( 'header' );

			$out->addHeadItem( 'tlads', $tlAdCode );
		}
		return true;
	}

	public static function onBruinenEndCode( $includeDir, $skin ) {
		include( $includeDir . '/../TeamLiquidFooter.inc' );
		return true;
	}

	public static function onParserBeforeStrip( &$parser, &$text, &$mStripState ) {
		if ( self::shouldShowAds( $parser->getUser(), $parser->getTitle() ) ) {
			// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
			if ( $parser->getTitle()->getNamespace() >= NS_MAIN && $parser->getOptions()->getEnableLimitReport() ) {
				$adbox_code = '<div class="content-ad navigation-not-searchable">' . AdCode::get( '728x90_BTF' ) . '</div>';
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
		}
		return true;
	}

	public static function onParserAfterTidy( &$parser, &$text ) {
		if ( self::shouldShowAds( $parser->getUser(), $parser->getTitle() ) ) {
			// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
			if ( $parser->getTitle()->getNamespace() >= NS_MAIN && $parser->getOptions()->getEnableLimitReport() ) {
				$text .= "\n" . '<div class="content-ad navigation-not-searchable">' . AdCode::get( '728x90_FOOTER' ) . '</div>';
			}
		}
		return true;
	}

	public static function onBruinenBodyFirst( $skin, &$value ) {
		return true;
	}

	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'adbox', 'Liquipedia\\Ads\\Hooks::adboxRender' );
		return true;
	}

	public static function adboxRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		$code = '<div class="navigation-not-searchable">' . AdCode::get( '300x250_ATF' ) . '</div>';
		return [ trim( $code ), 'markerType' => 'nowiki' ];
	}

}
