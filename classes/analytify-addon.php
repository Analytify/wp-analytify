<?php

class AnalytifyPro_Addon extends Analytify_Base {

	function __construct( $plugin_file_path ) {
		$this->is_addon = true;
		parent::__construct( $plugin_file_path );
	}
}
