<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Integration;

use Dev4Press\Plugin\BreadcrumbsPress\Extend\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kadence extends Theme {
	protected $theme = 'kadence';

	private $wrappers = array(
		'kadence_after_header',
		'kadence_before_content',
		'kadence_after_content',
		'kadence_before_footer',
	);
	protected $priorities = array(
		'kadence_after_header'        => 20,
		'kadence_before_content'      => 5,
		'kadence_before_main_content' => 10,
		'kadence_after_content'       => 20,
		'kadence_before_footer'       => 5,
	);

	protected $actions = array(
		'kadence_after_header',
		'kadence_before_content',
		'kadence_before_main_content',
		'kadence_after_content',
		'kadence_before_footer',
	);

	public function load() {
		$this->priority = $this->priorities[ $this->name ] ?? 10;

		if ( in_array( $this->name, $this->wrappers ) ) {
			$this->wrapper_class .= ' site-container';
		}

		add_action( $this->name, array( $this, 'breadcrumbs' ), $this->priority );
	}

	public static function instance() : Kadence {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Kadence();
		}

		return $instance;
	}
}
