<?php

class LiquiFlowAdsHooks {
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModuleStyles( 'ext.liquiflowads' );
		return true;
	}
	public static function onLiquiFlowAdSidebar() {
		global $liquipedia_ads;
		echo '<div id="sidebar-ad">';
		echo $liquipedia_ads['300x250_SATF'];
		echo '</div>';
		return true;
	}
	public static function onLiquiFlowAdTop() {
		global $liquipedia_ads;
		echo '<div id="top-ad">';
		echo $liquipedia_ads['728x90_ATF'];
		echo '</div>';
		return true;
	}
	public static function onLiquiFlowAdStartCode(OutputPage $out) {
		$tlAdCode = <<<END_HTML
<script>/* observer-6.0.10 */!function t(e,n,o){function r(i,s){if(!n[i]){if(!e[i]){var u="function"==typeof require&&require;if(!s&&u)return u(i,!0);if(a)return a(i,!0);var c=new Error("Cannot find module '"+i+"'");throw c.code="MODULE_NOT_FOUND",c}var d=n[i]={exports:{}};e[i][0].call(d.exports,function(t){var n=e[i][1][t];return r(n?n:t)},d,d.exports,t,e,n,o)}return n[i].exports}for(var a="function"==typeof require&&require,i=0;i<o.length;i++)r(o[i]);return r}({1:[function(t,e,n){function o(t){var e="NEW_SCRIPT_EVENT",n=new r(e,{detail:{}});t.dispatchEvent(n)}const r=t(3);e.exports=o},{3:3}],2:[function(t,e,n){function o(t){t.adonis=t.adonis||{},t.adonis.scriptStatuses={},t.adonis.requestStatuses={};const e=t.XMLHttpRequest,n=function(){const n=new e,o=n.open;return n.open=function(e,a){return n.addEventListener("error",function(e){0===n.status?t.adonis.scriptStatuses[a]="error":t.adonis.scriptStatuses[a]="load"}),n.addEventListener("load",function(e){t.adonis.scriptStatuses[a]="load"}),n.addEventListener("loadend",function(e){t.adonis.requestStatuses[a]=n.status,r(t)}),o.apply(n,arguments)},n};t.XMLHttpRequest=n,e.prototype.constructor=n}const r=t(1);e.exports={wrapXMLHttpRequest:o}},{1:1}],3:[function(t,e,n){(function(t){function n(){try{var t=new o("cat",{detail:{foo:"bar"}});return"cat"===t.type&&"bar"===t.detail.foo}catch(e){}return!1}var o=t.CustomEvent;e.exports=n()?o:"undefined"!=typeof document&&"function"==typeof document.createEvent?function(t,e){var n=document.createEvent("CustomEvent");return e?n.initCustomEvent(t,e.bubbles,e.cancelable,e.detail):n.initCustomEvent(t,!1,!1,void 0),n}:function(t,e){var n=document.createEventObject();return n.type=t,e?(n.bubbles=Boolean(e.bubbles),n.cancelable=Boolean(e.cancelable),n.detail=e.detail):(n.bubbles=!1,n.cancelable=!1,n.detail=void 0),n}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],4:[function(t,e,n){function o(t,e,n,o){"addEventListener"in t?t.addEventListener(e,n,o):"attachEvent"in t&&t.attachEvent("on"+e,n)}const r=t(2),a=t(1);!function(){window.adonis=window.adonis||{},window.adonis.scriptStatuses={},r.wrapXMLHttpRequest(window),o(document,"load",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="load",a(window))},!0),o(document,"error",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="error",a(window))},!0)}()},{1:1,2:2}]},{},[4]);</script>
		<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
		<script>
		  var googletag = googletag || {};
		  googletag.cmd = googletag.cmd || [];
		</script>
END_HTML;
		global $liquipedia_ads;
		$out->addHeadItem( 'tlads', $tlAdCode . $liquipedia_ads['header']);
		return true;
	}
	public static function onLiquiFlowAdEndCode($includeDir) {
		include ($includeDir . '/TeamLiquidFooter.inc');
		return true;
	}
	public static function onParserBeforeStrip( &$parser, &$text, &$mStripState ) {
		$adbox_tag = self::getAdboxTag();
		$title = $parser->getTitle();
		if($title->getNamespace() != NS_MAIN) {
			return true;
		}
		$wikipage = WikiPage::factory($title);
		$revision = $wikipage->getRevision();
		if (!$revision) {
			return;
		}
		$content = $revision->getContent(Revision::FOR_PUBLIC);
		$contenttext = ContentHandler::getContentText($content);
		if ($contenttext != $text) {
			return;
		}
		$has_added_adbox = false;
		if (preg_match_all("/^==([^=]+)==\\s*$/m", "\n" . $text, $findings)) {
			//$number_of_adboxes = 1;
			$pages = wfMessage( 'adbox-headings' )->plain();
			$key_headings = explode("\n", $pages);
			foreach($key_headings as $key_heading) {
				foreach($findings[1] as $findingid => $finding) {
					if(!$has_added_adbox) {
						if (trim($finding) == trim($key_heading, "* \t\n\r\0\x0B")) {
							$text = preg_replace('/' . str_replace('/', '\/', preg_quote($findings[0][$findingid])) . '/', $findings[0][$findingid] . "\n" . $adbox_tag . "\n", $text);
							$has_added_adbox = true;
							break 2;
						}
					}
				}
			}
		}
		if(!$has_added_adbox) {
			if (count($findings[0]) <= 2) {
				$text = $text . "\n" . $adbox_tag;
			} else {
				$text = preg_replace('/' . str_replace('/', '\/', preg_quote($findings[0][ceil((count($findings[0]) - 1) / 2)])) . '/', $findings[0][ceil((count($findings[0]) - 1) / 2)] . "\n" . $adbox_tag . "\n", $text);
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
		$text = str_replace($adbox_tag, $adbox_code, $text);
		return true;
	}
	public static function getAdboxTag() {
		return '<div>(((adbox)))</div>';
	}
}

?>