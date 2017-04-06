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
<script>
!function e(t,n,o){function r(i,u){if(!n[i]){if(!t[i]){var c="function"==typeof require&&require;if(!u&&c)return c(i,!0);if(a)return a(i,!0);var d=new Error("Cannot find module '"+i+"'");throw d.code="MODULE_NOT_FOUND",d}var f=n[i]={exports:{}};t[i][0].call(f.exports,function(e){var n=t[i][1][e];return r(n?n:e)},f,f.exports,e,t,n,o)}return n[i].exports}for(var a="function"==typeof require&&require,i=0;i<o.length;i++)r(o[i]);return r}({1:[function(e,t,n){(function(e){function n(){try{var e=new o("cat",{detail:{foo:"bar"}});return"cat"===e.type&&"bar"===e.detail.foo}catch(t){}return!1}var o=e.CustomEvent;t.exports=n()?o:"undefined"!=typeof document&&"function"==typeof document.createEvent?function(e,t){var n=document.createEvent("CustomEvent");return t?n.initCustomEvent(e,t.bubbles,t.cancelable,t.detail):n.initCustomEvent(e,!1,!1,void 0),n}:function(e,t){var n=document.createEventObject();return n.type=e,t?(n.bubbles=Boolean(t.bubbles),n.cancelable=Boolean(t.cancelable),n.detail=t.detail):(n.bubbles=!1,n.cancelable=!1,n.detail=void 0),n}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],2:[function(e,t,n){function o(e,t,n,o){"addEventListener"in e?e.addEventListener(t,n,o):"attachEvent"in e&&e.attachEvent("on"+t,n)}var r=e(1);!function(){window.adonis=window.adonis||{},window.adonis.scriptStatuses={};var e=function(){var e="NEW_SCRIPT_EVENT",t=new r(e,{detail:{}});window.dispatchEvent(t)};o(document,"load",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="load",e())},!0),o(document,"error",function(t){t.target&&"SCRIPT"===t.target.nodeName&&(window.adonis.scriptStatuses[t.target.src]="error",e())},!0)}()},{1:1}]},{},[2]);var adonis = adonis || {};adonis.conditionalAdRendering = true;
</script>
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

		$rc = new RequestContext;
		if($rc->getSkin()->getSkinName() != 'liquiflow') {
			return true;
		}
		$adbox_tag = "\n" . $adbox_tag . "\n";
		
		$title = $parser->getTitle();
		if($title->getNamespace() != NS_MAIN) {
			return true;
		}
		$wikipage = WikiPage::factory($title);
		$revision = $wikipage->getRevision();
		$content = $revision->getContent(Revision::FOR_PUBLIC);
		$contenttext = ContentHandler::getContentText($content);
		if ($contenttext != $text)
			return;
		$has_added_adbox = false;
		if (preg_match_all("/\n\s*==([^=]+)==\s*\n/", "\n" . $text, $findings)) {
			//$number_of_adboxes = 1;
			$pages = wfMessage( 'adbox-headings' )->plain();
			$key_headings = explode("\n", $pages);
			//echo '<pre>'.print_r(str_replace("\n","NNN",$text),true).'</pre>';
			foreach($key_headings as $key_heading) {
				foreach($findings[1] as $findingid => $finding) {
					if(!$has_added_adbox) {
						$pos = strpos($text, $findings[0][$findingid]);
						if (trim($finding) == trim($key_heading, "* \t\n\r\0\x0B")) {
							$text = substr_replace($text, $findings[0][$findingid] . $adbox_tag, $pos, strlen($findings[0][$findingid]));
							$has_added_adbox = true;
							break 2;
						}
					}
				}
			}
		}
		if(!$has_added_adbox) {
			if (count($findings[0]) <= 2) {
				$text = $text . $adbox_tag;
			} else {
				$pos = strpos($text, $findings[0][ceil((count($findings[0]) - 1) / 2)]);
				$text = substr_replace($text, $findings[0][ceil((count($findings[0]) - 1) / 2)] . $adbox_tag, $pos, strlen($findings[0][ceil((count($findings[0]) - 1) / 2)]));
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
		return '(((adbox)))';
	}
}

?>
