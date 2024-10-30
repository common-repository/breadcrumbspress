<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Blocksy extends Theme {
	protected $theme = 'blocky';

	private $wrappers = array(
		'blocksy:header:after',
		'blocksy:content:before',
		'blocksy:content:top',
		'blocksy:content:bottom',
		'blocksy:content:after',
		'blocksy:footer:before',
	);

	protected $actions = array(
		'blocksy:header:after',
		'blocksy:content:before',
		'blocksy:content:top',
		'blocksy:content:bottom',
		'blocksy:content:after',
		'blocksy:footer:before',
	);

	public function load() {
		$this->priority = $this->priorities[ $this->name ] ?? 10;

		if ( in_array( $this->name, $this->wrappers ) ) {
			$this->args['class'] = 'ct-container';
		}

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : Blocksy {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Blocksy();
		}

		return $instance;
	}
}
