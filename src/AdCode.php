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

}
