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

	public static function onBruinenEndCode( $includeDir, $skin, &$value ) {
		ob_start();
		include( $includeDir . '/../TeamLiquidFooter.inc' );
		$value .= ob_get_contents();
		ob_end_clean();
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

	private static $adboxHeading = null;
	private static $hasAddedAdbox = false;

	private static function setAdboxHeading( $pageHeadings ) {
		if ( is_null( self::$adboxHeading ) ) {
			$rawHeadings = wfMessage( 'adbox-headings' )->plain();
			$keyHeadings = explode( "\n", $rawHeadings );
			// Filter headings
			$filteredPageHeadings = array_values( array_filter( $pageHeadings, function( $var ) {
					// Only allow <h1> and <h2>
					if ( intval( $var[ 'level' ] ) <= 2 ) {
						return true;
					}
					return false;
				} ) );
			foreach ( $keyHeadings as $keyHeading ) {
				foreach ( $filteredPageHeadings as $filteredPageHeading ) {
					if ( $keyHeading === $filteredPageHeading[ 'line' ] ) {
						self::$adboxHeading = [
							'text' => $filteredPageHeading[ 'line' ],
							'anchor' => $filteredPageHeading[ 'anchor' ],
						];
						return;
					}
				}
			}
			$middleHeading = $filteredPageHeadings[ ceil( ( count( $filteredPageHeadings ) - 1 ) / 2 ) ];
			self::$adboxHeading = [
				'text' => $middleHeading[ 'line' ],
				'anchor' => $middleHeading[ 'anchor' ],
			];
		}
	}

	public static function onParserSectionCreate( $parser, $section, &$sectionContent, $showEditLinks ) {
		if ( self::shouldShowAds( $parser->getUser(), $parser->getTitle() ) ) {
			// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
			if ( $parser->getTitle()->getNamespace() >= NS_MAIN && $parser->getOptions()->getEnableLimitReport() ) {
				self::setAdboxHeading( $parser->getOutput()->getSections() );
				if ( !self::$hasAddedAdbox && strpos( $sectionContent, 'id="' . self::$adboxHeading[ 'anchor' ] . '"' ) !== false ) {
					self::$hasAddedAdbox = true;
					$adboxCode = '<div class="content-ad navigation-not-searchable">' . AdCode::get( '728x90_BTF' ) . '</div>';
					preg_match( '/<(h[1-6])/', $sectionContent, $matches );
					$headingType = $matches[ 1 ];
					$sectionContent = preg_replace( '</' . $headingType . '>', '</' . $headingType . '>' . "\n" . $adboxCode . "\n", $sectionContent, 1 );
				}
			}
		}
	}

}
