<?php

class LiquiFlowAdsHooks {
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleStyles( 'ext.liquiflowads' );
		return true;
	}
	public static function onLiquiFlowSidebar( $skin ) {
		if(
			(
				!in_array( $skin->getRequest()->getVal( 'action', 'view' ), [ 'edit', 'submit' ] )
				|| $skin->getTitle()->getNamespace() === NS_SPECIAL 
			)
			&& !$skin->getUser()->isAnon()
		) {
			global $liquipedia_ads;
			echo '<div id="sidebar-ad">';
			echo $liquipedia_ads['300x250_SATF'];
			echo '</div>';
			echo '<script>'
			. 'var screen_width = Math.max(document.body.scrollWidth, document.documentElement.scrollWidth, document.body.offsetWidth, document.documentElement.offsetWidth, document.documentElement.clientWidth);
if(screen_width < 1304) {
	document.querySelector(\'#sidebar-ad div\').removeAttribute(\'adonis-marker\');
}'
			. '</script>';
		}
		return true;
	}
	public static function onLiquiFlowTop( $skin ) {
		if(
			(
				!in_array( $skin->getRequest()->getVal( 'action', 'view' ), [ 'edit', 'submit' ] )
				|| $skin->getTitle()->getNamespace() === NS_SPECIAL 
			)
			&& !$skin->getUser()->isAnon()
		) {
			global $liquipedia_ads;
			echo '<div id="top-ad">';
			echo $liquipedia_ads['728x90_ATF'];
			echo '</div>';
		}
		return true;
	}
	public static function onLiquiFlowStartCode( OutputPage $out ) {
		global $liquipedia_ads;

		$tlAdCode = '';

		if( isset( $liquipedia_ads['no_adonis'] ) && $liquipedia_ads['no_adonis'] ) {
		} elseif( isset( $liquipedia_ads['adonis_v2'] ) && $liquipedia_ads['adonis_v2'] ) {
			$tlAdCode .= <<<END_HTML
<script>!function t(e,n,o){function r(a,s){if(!n[a]){if(!e[a]){var u="function"==typeof require&&require;if(!s&&u)return u(a,!0);if(i)return i(a,!0);var d=new Error("Cannot find module '"+a+"'");throw d.code="MODULE_NOT_FOUND",d}var c=n[a]={exports:{}};e[a][0].call(c.exports,function(t){var n=e[a][1][t];return r(n||t)},c,c.exports,t,e,n,o)}return n[a].exports}for(var i="function"==typeof require&&require,a=0;a<o.length;a++)r(o[a]);return r}({1:[function(t,e,n){"use strict";function o(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"NEW_SCRIPT_EVENT",n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},o=new r(e,{detail:n});t.dispatchEvent(o)}var r=t(3);e.exports=o},{3:3}],2:[function(t,e,n){"use strict";function o(t){t.adonis=t.adonis||{},t.adonis.scriptStatuses={},t.adonis.requestStatuses={};var e=t.XMLHttpRequest.prototype.open;t.XMLHttpRequest.prototype.open=function(n,o){this.addEventListener("error",function(e){0===this.status?t.adonis.scriptStatuses[o]="error":t.adonis.scriptStatuses[o]="load"}),this.addEventListener("load",function(e){t.adonis.scriptStatuses[o]="load"}),this.addEventListener("loadend",function(e){t.adonis.requestStatuses[o]=this.status,r(t)});var i=[].slice.call(arguments,0);return e.apply(this,i)}}var r=t(1);e.exports={wrapXMLHttpRequest:o}},{1:1}],3:[function(t,e,n){(function(t){var n=t.CustomEvent;e.exports=function(){try{var t=new n("cat",{detail:{foo:"bar"}});return"cat"===t.type&&"bar"===t.detail.foo}catch(t){}return!1}()?n:"undefined"!=typeof document&&"function"==typeof document.createEvent?function(t,e){var n=document.createEvent("CustomEvent");return e?n.initCustomEvent(t,e.bubbles,e.cancelable,e.detail):n.initCustomEvent(t,!1,!1,void 0),n}:function(t,e){var n=document.createEventObject();return n.type=t,e?(n.bubbles=Boolean(e.bubbles),n.cancelable=Boolean(e.cancelable),n.detail=e.detail):(n.bubbles=!1,n.cancelable=!1,n.detail=void 0),n}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],4:[function(t,e,n){"use strict";function o(t,e,n,o){"addEventListener"in t?t.addEventListener(e,n,o):"attachEvent"in t&&t.attachEvent("on"+e,n)}var r=t(2),i=t(1);!function(){window.adonis=window.adonis||{},window.adonis.scriptStatuses={},r.wrapXMLHttpRequest(window),o(document,"load",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="load",i(window))},!0),o(document,"error",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="error",i(window))},!0)}()},{1:1,2:2}]},{},[4]);var adonis = adonis || {};adonis.conditionalAdRendering = true;adonis.transport = 'https://www.adiode.com/http';</script>
END_HTML;
		} else {
			$tlAdCode .= <<<END_HTML
<script>/* observer-6.0.10 */!function t(e,n,o){function r(i,s){if(!n[i]){if(!e[i]){var u="function"==typeof require&&require;if(!s&&u)return u(i,!0);if(a)return a(i,!0);var c=new Error("Cannot find module '"+i+"'");throw c.code="MODULE_NOT_FOUND",c}var d=n[i]={exports:{}};e[i][0].call(d.exports,function(t){var n=e[i][1][t];return r(n?n:t)},d,d.exports,t,e,n,o)}return n[i].exports}for(var a="function"==typeof require&&require,i=0;i<o.length;i++)r(o[i]);return r}({1:[function(t,e,n){function o(t){var e="NEW_SCRIPT_EVENT",n=new r(e,{detail:{}});t.dispatchEvent(n)}const r=t(3);e.exports=o},{3:3}],2:[function(t,e,n){function o(t){t.adonis=t.adonis||{},t.adonis.scriptStatuses={},t.adonis.requestStatuses={};const e=t.XMLHttpRequest,n=function(){const n=new e,o=n.open;return n.open=function(e,a){return n.addEventListener("error",function(e){0===n.status?t.adonis.scriptStatuses[a]="error":t.adonis.scriptStatuses[a]="load"}),n.addEventListener("load",function(e){t.adonis.scriptStatuses[a]="load"}),n.addEventListener("loadend",function(e){t.adonis.requestStatuses[a]=n.status,r(t)}),o.apply(n,arguments)},n};t.XMLHttpRequest=n,e.prototype.constructor=n}const r=t(1);e.exports={wrapXMLHttpRequest:o}},{1:1}],3:[function(t,e,n){(function(t){function n(){try{var t=new o("cat",{detail:{foo:"bar"}});return"cat"===t.type&&"bar"===t.detail.foo}catch(e){}return!1}var o=t.CustomEvent;e.exports=n()?o:"undefined"!=typeof document&&"function"==typeof document.createEvent?function(t,e){var n=document.createEvent("CustomEvent");return e?n.initCustomEvent(t,e.bubbles,e.cancelable,e.detail):n.initCustomEvent(t,!1,!1,void 0),n}:function(t,e){var n=document.createEventObject();return n.type=t,e?(n.bubbles=Boolean(e.bubbles),n.cancelable=Boolean(e.cancelable),n.detail=e.detail):(n.bubbles=!1,n.cancelable=!1,n.detail=void 0),n}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],4:[function(t,e,n){function o(t,e,n,o){"addEventListener"in t?t.addEventListener(e,n,o):"attachEvent"in t&&t.attachEvent("on"+e,n)}const r=t(2),a=t(1);!function(){window.adonis=window.adonis||{},window.adonis.scriptStatuses={},r.wrapXMLHttpRequest(window),o(document,"load",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="load",a(window))},!0),o(document,"error",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="error",a(window))},!0)}()},{1:1,2:2}]},{},[4]);</script>
END_HTML;
		}

		if( isset( $liquipedia_ads['curse_test'] ) && $liquipedia_ads['curse_test'] ) {
			$tlAdCode .= <<<END_HTML
<script>
var script = document.createElement('script');
var tstamp = new Date();
var cachebust = '' + (1900 + tstamp.getYear()) + '-' + tstamp.getMonth() + '-' + tstamp.getDate() + '-' + tstamp.getHours();
script.id = 'factorem';
script.src = '//cdm.cursecdn.com/js/teamliquid/cdmfactorem_min.js?' + cachebust;
script.async = false;
script.type = 'text/javascript';
document.head.appendChild(script);
</script>
END_HTML;
		} else {
			$tlAdCode .= <<<END_HTML
<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
<script>
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	var advelvetTargeting = [];
	advelvetTargeting.push((Math.floor(Math.random() * 20) + 1) + "");
	googletag.cmd.push(function () {
		googletag.pubads().setTargeting('advelvet', advelvetTargeting);
	});
</script>
END_HTML;
		}

		$tlAdCode .= $liquipedia_ads['header'];

		$out->addHeadItem( 'tlads', $tlAdCode );
		return true;
	}
	public static function onLiquiFlowEndCode( $includeDir, $skin ) {
		include( $includeDir . '/TeamLiquidFooter.inc' );
		return true;
	}
	public static function onParserBeforeStrip( &$parser, &$text, &$mStripState ) {
		$adbox_tag = self::getAdboxTag();
		$title = $parser->getTitle();
		if( $title->getNamespace() != NS_MAIN ) {
			return true;
		}
		$wikipage = WikiPage::factory( $title );
		$revision = $wikipage->getRevision();
		if ( !$revision ) {
			return;
		}
		$content = $revision->getContent( Revision::FOR_PUBLIC );
		$contenttext = ContentHandler::getContentText( $content );
		if( $contenttext != $text ) {
			return;
		}
		$has_added_adbox = false;
		if( preg_match_all( "/^==([^=]+)==\\s*$/m", $text, $findings ) ) {
			//$number_of_adboxes = 1;
			$pages = wfMessage( 'adbox-headings' )->plain();
			$key_headings = explode( "\n", $pages );
			foreach( $key_headings as $key_heading ) {
				foreach( $findings[1] as $findingid => $finding ) {
					if( !$has_added_adbox ) {
						if( trim( $finding ) == trim( $key_heading, "* \t\n\r\0\x0B" ) ) {
							$text = preg_replace( '/^' . str_replace( '/', '\/', preg_quote( $findings[0][$findingid] ) ) . '$/m', $findings[0][$findingid] . "\n" . $adbox_tag . "\n", $text, 1 );
							$has_added_adbox = true;
							break 2;
						}
					}
				}
			}
		}
		if( !$has_added_adbox ) {
			if ( count( $findings[0] ) <= 2 ) {
				$text = $text . "\n" . $adbox_tag;
			} else {
				$text = preg_replace( '/^' . str_replace( '/', '\/', preg_quote( $findings[0][ceil( ( count( $findings[0]) - 1) / 2)] ) ) . '$/m', $findings[0][ceil( ( count( $findings[0] ) - 1 ) / 2 )] . "\n" . $adbox_tag . "\n", $text, 1 );
				$has_added_adbox = true;
			}
		}
		return true;
	}
	public static function onParserAfterTidy( &$parser, &$text ) {
		global $liquipedia_ads;
		$adbox_tag = self::getAdboxTag();
		$adbox_code = '<div class="content-ad">'
			. $liquipedia_ads['728x90_BTF']
			. '</div>';
		$text = str_replace( $adbox_tag, $adbox_code, $text );
		return true;
	}
	public static function getAdboxTag() {
		return '<div>(((adbox)))</div>';
	}
	public static function onLiquiFlowBodyFirst() {
		global $liquipedia_ads;
		if( isset($liquipedia_ads['no_adonis']) && $liquipedia_ads['no_adonis'] ) {
		} elseif( isset($liquipedia_ads['adonis_v2']) && $liquipedia_ads['adonis_v2'] ) {
			echo '<script src="/starcraft/resources/assets/w2.1.js"></script>';
		} else {
			echo '<script src="/starcraft/resources/assets/w.1.js"></script>';
		}
		return true;
	}
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'adbox', 'LiquiFlowAdsHooks::adboxRender' );
		return true;
	}
	public static function adboxRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		global $liquipedia_ads;
		$code = $liquipedia_ads['300x250_ATF'];
		return [ trim( $code ), 'markerType' => 'nowiki' ];
	}
}

?>
