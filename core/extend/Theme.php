<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Extend;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Theme {
	protected $theme;
	protected $name;
	protected $priority = 10;
	protected $wrapper_class = '';
	protected $args = array();
	protected $actions = array();
	protected $priorities = array();

	public function __construct() {
		$this->name = Loader::instance()->action();

		if ( ! empty( $this->actions ) && ! in_array( $this->name, $this->actions ) ) {
			$this->name = $this->actions[0];
		}

		$this->load();
	}

	public function breadcrumbs() {
		echo Loader::instance()->render( $this->theme, $this->wrapper_class, $this->args );
	}

	abstract protected function load();
}