<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Astra extends Theme {
	protected $theme = 'astra';

	protected $actions = array(
		'astra_content_before',
		'astra_content_top',
		'astra_content_bottom',
		'astra_content_after',
	);

	public function load() {
		if ( $this->name == 'astra_content_before' || $this->name == 'astra_content_after' ) {
			$this->wrapper_class .= ' ast-container';
		}

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : Astra {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Astra();
		}

		return $instance;
	}
}
