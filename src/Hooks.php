<?php

namespace Liquipedia\LiquipediaAds;

use MediaWiki\MediaWikiServices;
use OutputPage;
use Parser;
use PPFrame;

class Hooks {

	/**
	 * Determine if ads should be shown
	 * @param User $user
	 * @param Title $title
	 * @param WebRequest|null $request
	 * @return bool
	 */
	private static function shouldShowAds( $user, $title, $request = null ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();

		if ( $title->isSpecialPage() ) {
			$blacklistedPages = $config->get( 'LiquipediaAdsBlacklistedPages' );
			foreach ( $blacklistedPages as $page ) {
				if ( $title->isSpecial( $page ) ) {
					// Special pages that should never have ads
					return false;
				}
			}
		}

		if ( $user->isAnon() ) {
			// Anonymous people get ads
			return true;
		} elseif (
			$request !== null
			&& in_array( $request->getVal( 'action', 'view' ), [ 'edit', 'submit', 'delete', 'protect' ] )
		) {
			// No ads on certain utility pages for logged in users
			return false;
		} elseif ( $title->isSpecialPage() ) {
			$whitelistedPages = $config->get( 'LiquipediaAdsWhitelistedPages' );
			foreach ( $whitelistedPages as $page ) {
				if ( $title->isSpecial( $page ) ) {
					// Special pages that should always have ads
					return true;
				}
			}
			// Other special pages should not have ads when logged in,
			// since they are mostly used for editors
			return false;
		}

		// If no special case has occured, show ads
		return true;
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleStyles( 'ext.liquipediaads' );
		return true;
	}

	/**
	 * @param Skin $skin
	 * @param string &$value
	 * @param bool $sidebarHasContent
	 * @return bool
	 */
	public static function onBruinenSidebar( $skin, &$value, $sidebarHasContent = false ) {
		if ( self::shouldShowAds( $skin->getUser(), $skin->getTitle(), $skin->getRequest() ) ) {
			$value .= '<div id="sidebar-ad" class="navigation-not-searchable">';
			if ( $sidebarHasContent ) {
				$value .= AdCode::get( '300x250_SATF' );
			} else {
				$value .= AdCode::get( '300x250_SATF2' );
			}
			$value .= '</div>';
		}
		return true;
	}

	/**
	 * @param Skin $skin
	 * @param string &$value
	 * @return bool
	 */
	public static function onBruinenTop( $skin, &$value ) {
		if ( self::shouldShowAds( $skin->getUser(), $skin->getTitle(), $skin->getRequest() ) ) {
			$value .= '<div id="top-ad" class="navigation-not-searchable">';
			$value .= AdCode::get( '728x90_ATF' );
			$value .= '</div>';
		}
		return true;
	}

	/**
	 * @param OutputPage $out
	 * @return bool
	 */
	public static function onBruinenStartCode( OutputPage $out ) {
		if ( self::shouldShowAds( $out->getUser(), $out->getTitle(), $out->getRequest() ) ) {
			$tlAdCode = AdCode::getStartCode();
			$tlAdCode .= AdCode::get( 'header' );

			$out->addHeadItem( 'tlads', $tlAdCode );
		}
		return true;
	}

	/**
	 * @param Skin $skin
	 * @param string &$text
	 */
	public static function onSkinAfterBottomScripts( $skin, &$text ) {
		$text .= AdCode::get( 'footer' );
		$text .= AdCode::getAnalytics();
	}

	/**
	 * @param Parser &$parser
	 * @param string &$text
	 * @return bool
	 */
	public static function onParserAfterTidy( &$parser, &$text ) {
		if ( self::shouldShowAds( $parser->getUser(), $parser->getTitle() ) ) {
			// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
			if (
				$parser->getTitle()->getNamespace() >= NS_MAIN
				&& $parser->getOptions()->getEnableLimitReport()
			) {
				$text .= "\n" . '<div class="content-ad navigation-not-searchable">'
					. AdCode::get( '728x90_FOOTER' )
					. '</div>';
			}
		}
		return true;
	}

	/**
	 * @param Skin $skin
	 * @param string &$value
	 * @return bool
	 */
	public static function onBruinenBodyFirst( $skin, &$value ) {
		return true;
	}

	/**
	 * @param Parser &$parser
	 * @return bool
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'adbox', 'Liquipedia\\LiquipediaAds\\Hooks::adboxRender' );
		return true;
	}

	/**
	 * Render adbox
	 * @param string $input
	 * @param array $args
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return array
	 */
	public static function adboxRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		$code = '<div class="navigation-not-searchable">' . AdCode::get( '300x250_ATF' ) . '</div>';
		return [ trim( $code ), 'markerType' => 'nowiki' ];
	}

	private static $adboxHeading = null;
	private static $hasAddedAdbox = false;

	/**
	 * @param array $pageHeadings
	 */
	private static function setAdboxHeading( $pageHeadings ) {
		if ( count( $pageHeadings ) === 0 ) {
			return;
		}
		if ( self::$adboxHeading === null ) {
			$rawHeadings = wfMessage( 'adbox-headings' )->plain();
			$keyHeadings = explode( "\n", $rawHeadings );
			// Filter headings
			$filteredPageHeadings = array_values(
				array_filter(
					$pageHeadings,
					function ( $var ) {
					// Only allow <h1> and <h2>
					if ( intval( $var[ 'level' ] ) <= 2 ) {
						return true;
					}
					return false;
					}
				)
			);
			foreach ( $keyHeadings as $keyHeading ) {
				foreach ( $filteredPageHeadings as $filteredPageHeading ) {
					if ( trim( $keyHeading, '* ' ) === trim( $filteredPageHeading[ 'line' ] ) ) {
						self::$adboxHeading = [
							'text' => $filteredPageHeading[ 'line' ],
							'anchor' => $filteredPageHeading[ 'anchor' ],
						];
						return;
					}
				}
			}
			// Require at least 3 headings to allow an adbox in the middle
			// that is not attached to one of the key headings
			if ( count( $filteredPageHeadings ) > 3 ) {
				$middleHeading = $filteredPageHeadings[ ceil( ( count( $filteredPageHeadings ) - 1 ) / 2 ) ];
				self::$adboxHeading = [
					'text' => $middleHeading[ 'line' ],
					'anchor' => $middleHeading[ 'anchor' ],
				];
				return;
			}
			self::$adboxHeading = false;
		}
	}

	/**
	 * @param Parser $parser
	 * @param int $section
	 * @param string &$sectionContent
	 * @param bool $showEditLinks
	 */
	public static function onParserSectionCreate(
		$parser, $section, &$sectionContent, $showEditLinks
	) {
		if ( self::shouldShowAds( $parser->getUser(), $parser->getTitle() ) ) {
			// HACK: $parser->getOptions()->getEnableLimitReport() only returns true in main parsing run
			if (
				$parser->getTitle()->getNamespace() >= NS_MAIN
				&& $parser->getOptions()->getEnableLimitReport()
			) {
				self::setAdboxHeading( $parser->getOutput()->getSections() );
				if (
					self::$adboxHeading
					&& !self::$hasAddedAdbox
					&& strpos( $sectionContent, 'id="' . self::$adboxHeading[ 'anchor' ] . '"' ) !== false
				) {
					self::$hasAddedAdbox = true;
					$adboxCode = '<div class="content-ad navigation-not-searchable">'
						. AdCode::get( '728x90_BTF' )
						. '</div>';
					preg_match( '/<(h[1-6])/', $sectionContent, $matches );
					$headingType = $matches[ 1 ];
					$sectionContent = preg_replace(
						'(<\/' . $headingType . '>)',
						'</' . $headingType . '>' . "\n" . $adboxCode . "\n", $sectionContent,
						1
					);
				}
			}
		}
	}

}
