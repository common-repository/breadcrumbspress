<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Genesis extends Theme {
	protected $theme = 'genesis';

	protected $actions = array(
		'genesis_before_content_sidebar_wrap',
		'genesis_before_content',
		'genesis_before_loop',
		'genesis_after_content_sidebar_wrap',
		'genesis_footer',
	);

	public function load() {
		if ( $this->name == 'genesis_footer' ) {
			$this->priority = 5;
		}

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : Genesis {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Genesis();
		}

		return $instance;
	}
}
