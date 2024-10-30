<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\v49\WordPress\Legacy\Shortcodes as ShortcodesBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes extends ShortcodesBase {
	public $prefix = 'breadcrumbspress';

	public static function instance() : Shortcodes {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Shortcodes();
		}

		return $instance;
	}

	public function init() {
		$this->shortcodes = array(
			'current' => array(
				'name' => __( 'Current Breadcrumbs', 'breadcrumbspress' ),
				'atts' => array(
					'rich_snippet' => breadcrumbspress_settings()->get( 'include_rich_snippets' ),
					'list'         => breadcrumbspress_settings()->get( 'markup_list_type' ),
					'id'           => '',
					'class'        => '',
					'force'        => false,
				),
				'bool' => array( 'rich_snippet', 'force' ),
			),
			'post'    => array(
				'name' => __( 'Breadcrumbs for Post', 'breadcrumbspress' ),
				'atts' => array(
					'rich_snippet' => breadcrumbspress_settings()->get( 'include_rich_snippets' ),
					'list'         => breadcrumbspress_settings()->get( 'markup_list_type' ),
					'id'           => '',
					'class'        => '',
					'path'         => null,
					'taxonomy'     => null,
					'post'         => 0,
				),
				'bool' => array( 'rich_snippet' ),
				'int'  => array( 'post' ),
			),
			'term'    => array(
				'name' => __( 'Breadcrumbs for Term', 'breadcrumbspress' ),
				'atts' => array(
					'rich_snippet' => breadcrumbspress_settings()->get( 'include_rich_snippets' ),
					'list'         => breadcrumbspress_settings()->get( 'markup_list_type' ),
					'id'           => '',
					'class'        => '',
					'path'         => null,
					'taxonomy'     => '',
					'term'         => '',
				),
				'bool' => array( 'rich_snippet' ),
			),
		);
	}

	public function shortcode_current( $atts ) : string {
		$name = 'current';

		$atts         = $this->_atts( $name, $atts );
		$atts['echo'] = false;

		return $this->_wrapper( breadcrumbspress_current( $atts ), $name );
	}

	public function shortcode_post( $atts ) : string {
		$name = 'post';

		$atts         = $this->_atts( $name, $atts );
		$atts['echo'] = false;

		return $this->_wrapper( breadcrumbspress_build()->post( $atts['post'], $atts ), $name );
	}

	public function shortcode_term( $atts ) : string {
		$name = 'term';

		$atts         = $this->_atts( $name, $atts );
		$atts['echo'] = false;

		return $this->_wrapper( breadcrumbspress_build()->term( $atts['term'], $atts['taxonomy'], $atts ), $name );
	}
}
