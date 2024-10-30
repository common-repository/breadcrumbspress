<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Action {
	private $name;
	private $priority;
	private $wrapper_class;

	public function __construct() {
		$_settings = breadcrumbspress_settings()->prefix_get( 'action_', 'integration' );

		foreach ( $_settings as $key => $value ) {
			$this->{$key} = $value;
		}

		if ( ! empty( $this->name ) ) {
			add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
		}
	}

	public static function instance() : Action {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Action();
		}

		return $instance;
	}

	public function breadcrumbs() {
		echo Loader::instance()->render( 'action', $this->wrapper_class );
	}
}
