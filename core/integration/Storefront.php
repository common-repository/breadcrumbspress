<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Storefront extends Theme {
	protected $theme = 'storefront';

	private $wrappers = array(
		'storefront_header',
		'storefront_before_content',
		'storefront_before_footer',
	);
	protected $priorities = array(
		'storefront_header'         => 90,
		'storefront_before_content' => 10,
		'storefront_content_top'    => 10,
		'storefront_before_footer'  => 10,
		'storefront_footer'         => 5,
	);

	protected $actions = array(
		'storefront_header',
		'storefront_before_content',
		'storefront_content_top',
		'storefront_before_footer',
		'storefront_footer',
	);

	public function load() {
		$this->priority      = $this->priorities[ $this->name ] ?? 10;
		$this->wrapper_class = in_array( $this->name, $this->wrappers ) ? 'col-full' : '';

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : Storefront {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Storefront();
		}

		return $instance;
	}
}
