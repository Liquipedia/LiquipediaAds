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
		$article = WikiPage::factory($title);
		if ($article->getText(Revision::FOR_PUBLIC) != $text)
			return;
		$has_added_adbox = false;
		if (preg_match_all("/\n\s*==([^=]+)==\s*\n/", "\n" . $text, $findings)) {
			//$number_of_adboxes = 1;
			$pages = wfMessage( 'adbox-headings' )->plain();
			$key_headings = explode("\n", $pages);
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
