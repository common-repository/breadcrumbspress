<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Render {
	public function __construct() {
	}

	public static function instance() : Render {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Render();
		}

		return $instance;
	}

	public function single( $post_id = 0, $attributes = array() ) : string {
		$attributes['echo'] = false;
		$attributes['id']   = 'breadcrumbs-block-' . $attributes['block_id'];

		if ( $post_id > 0 ) {
			return breadcrumbspress_build()->post( $post_id, $attributes );
		} else {
			return breadcrumbspress_build()->current( $attributes );
		}
	}
}
