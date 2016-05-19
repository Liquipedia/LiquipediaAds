<?php

class LiquiFlowAdsHooks {
	public static function onLiquiFlowAdSidebar($includeDir) {
		echo '<div id="sidebar-ad">';
		include ($includeDir . '/TeamLiquidStickyAd.inc');
		echo '</div>';
		return true;
	}
	public static function onLiquiFlowAdTop($includeDir) {
		echo '<div class="top-ad">';
		include ($includeDir . '/TeamLiquidTopAd.inc');
		echo '</div>';
		return true;
	}
	public static function onLiquiFlowAdStartCode($includeDir, OutputPage $out) {
		global $tlAdCode;
		include ($includeDir . '/TLAdHeader.inc');
		$out->addHeadItem( 'tlads', $tlAdCode);
		return true;
	}
	public static function onLiquiFlowAdEndCode($includeDir) {
		include ($includeDir . '/TeamLiquidFooter.inc');
		return true;
	}
}

?>