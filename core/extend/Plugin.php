<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Extend;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Builder;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Content;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Plugin {
	public function __construct() {
	}

	public function b() : Builder {
		return Builder::instance();
	}

	public function q() : Query {
		return Query::instance();
	}

	public function c() : Content {
		return Content::instance();
	}

	protected function run() {
		add_filter( 'breadcrumbspress_generator_build', array( $this, 'build' ) );
		add_filter( 'breadcrumbspress_generator_complete', array( $this, 'complete' ) );
		add_action( 'breadcrumbspress_plugin_core_ready', array( $this, 'ready' ) );
	}

	protected function override() {

	}

	/**
	 * @param array $breadcrumbs
	 *
	 * @return Crumb[]
	 */
	abstract public function build( $breadcrumbs ) : array;

	/**
	 * @param array $breadcrumbs
	 *
	 * @return Crumb[]
	 */
	abstract public function complete( $breadcrumbs ) : array;

	abstract public function ready();
}
