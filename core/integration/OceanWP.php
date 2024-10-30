<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OceanWP extends Theme {
	protected $theme = 'oceanwp';

	protected $actions = array(
		'ocean_before_main',
		'ocean_before_page_header_inner',
		'ocean_after_page_header_inner',
		'ocean_before_content_wrap',
		'ocean_before_primary',
		'ocean_after_primary',
		'ocean_after_content_wrap',
	);

	public function load() {
		if ( $this->name != 'ocean_before_primary' && $this->name != 'ocean_after_primary' ) {
			$this->wrapper_class .= ' container';
		}

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : OceanWP {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new OceanWP();
		}

		return $instance;
	}
}
