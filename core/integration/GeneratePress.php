<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GeneratePress extends Theme {
	protected $theme = 'generatepress';

	private $wrappers = array(
		'generate_after_header',
		'generate_before_footer',
		'generate_footer',
	);

	protected $priorities = array(
		'generate_after_header'          => 20,
		'generate_inside_site_container' => 5,
		'generate_before_footer'         => 5,
		'generate_footer'                => 7,
	);

	protected $actions = array(
		'generate_after_header',
		'generate_inside_site_container',
		'generate_before_footer',
		'generate_footer',
	);

	public function load() {
		$this->priority = $this->priorities[ $this->name ] ?? 10;

		if ( in_array( $this->name, $this->wrappers ) ) {
			$this->args['class'] = 'grid-container';
		}

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : GeneratePress {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new GeneratePress();
		}

		return $instance;
	}
}
