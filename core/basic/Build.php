<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Archives;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Builder;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Content;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Generator;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Render;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Visibility;
use Dev4Press\v49\Core\Quick\WPR;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Build {
	private $current_json = false;

	public function __construct() {
	}

	public static function instance() : Build {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Build();
		}

		return $instance;
	}

	private function args( $args = array(), $current = false, $menu = false ) : array {
		$defaults = array(
			'rich_snippet' => breadcrumbspress_settings()->get( 'include_rich_snippets' ),
			'list'         => breadcrumbspress_settings()->get( 'markup_list_type' ),
			'id'           => '',
			'class'        => '',
			'font_size'    => '',
			'align'        => '',
			'hide'         => null,
			'renderer'     => false,
			'echo'         => true,
		);

		if ( $current ) {
			$defaults['force'] = false;
		} else {
			$defaults['path']     = null;
			$defaults['taxonomy'] = null;
		}

		return shortcode_atts( $defaults, $args );
	}

	private function render( $breadcrumbs, $args ) : string {
		$render = '';

		if ( ! empty( $breadcrumbs ) ) {
			$renderer = $args['renderer'] === false ? Render::instance() : $args['renderer'];

			if ( $args['rich_snippet'] && ! $this->current_json ) {
				$render .= $renderer->single_jsonld( $breadcrumbs );

				$this->current_json = true;
			}

			$attrs = array(
				'id'        => $args['id'],
				'class'     => $args['class'],
				'list'      => $args['list'],
				'font_size' => $args['font_size'],
				'align'     => $args['align'],
				'hide'      => $args['hide'],
			);

			$render .= $renderer->html( $breadcrumbs, $attrs );
		}

		return $render;
	}

	public function current( array $args = array() ) : string {
		$args = $this->args( $args, true );

		$render = '';

		if ( $args['force'] === true || Visibility::instance()->is_visible() ) {
			$breadcrumbs = Generator::instance()->build();

			$render = $this->render( $breadcrumbs, $args );
			breadcrumbspress_settings()->track_current();
		}

		if ( $args['echo'] ) {
			echo $render;
		}

		return $render;
	}

	public function current_json_only( array $args = array() ) : string {
		$args = $this->args( $args, true );

		$render = '';

		if ( ! $this->current_json ) {
			if ( $args['force'] === true || Visibility::instance()->is_visible() ) {
				$breadcrumbs = Generator::instance()->build();

				$renderer = $args['renderer'] === false ? Render::instance() : $args['renderer'];

				$render .= $renderer->single_jsonld( $breadcrumbs );
				breadcrumbspress_settings()->track_current( true );

				$this->current_json = true;
			}
		}

		if ( $args['echo'] ) {
			echo $render;
		}

		return $render;
	}

	/**
	 * @param \WP_Post|int $post
	 * @param array        $args
	 *
	 * @return string
	 */
	public function post( $post, array $args = array() ) : string {
		$post = get_post( $post );

		if ( $post ) {
			$args = $this->args( $args );

			$breadcrumbs = Content::instance()->single( $post, $args['path'], $args['taxonomy'] );

			$render = $this->render( $breadcrumbs, $args );
		} else {
			$render = '';
		}

		if ( $args['echo'] ) {
			echo $render;
		}

		return $render;
	}

	/**
	 * @param \WP_Term|int $term
	 * @param string       $taxonomy
	 * @param array        $args
	 *
	 * @return string
	 */
	public function term( $term, string $taxonomy = '', array $args = array() ) : string {
		$term = WPR::get_term( $term, $taxonomy );

		if ( $term ) {
			$args = $this->args( $args );

			$breadcrumbs = Archives::instance()->single( $term, $args['path'] );

			$render = $this->render( $breadcrumbs, $args );
		} else {
			$render = '';
		}

		if ( $args['echo'] ) {
			echo $render;
		}

		return $render;
	}

	/**
	 * @param \Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb[] $breadcrumbs
	 * @param array                                           $args
	 *
	 * @return string
	 */
	public function custom( array $breadcrumbs, array $args = array() ) : string {
		$args = $this->args( $args );

		$render = $this->render( $breadcrumbs, $args );

		if ( $args['echo'] ) {
			echo $render;
		}

		return $render;
	}

	private function jsonld_args( array $args = array() ) : array {
		$defaults = array(
			'renderer' => false,
			'echo'     => true,
		);

		return shortcode_atts( $defaults, $args );
	}

	private function jsonld_render( array $breadcrumbs, $args, $single = true ) : string {
		$args     = $this->jsonld_args( $args );
		$renderer = $args['renderer'] === false ? Render::instance() : $args['renderer'];
		$render   = $renderer->single_jsonld( $breadcrumbs );

		if ( $args['echo'] ) {
			echo $render;
		}

		return $render;
	}

	public function jsonld_custom_single( $breadcrumbs, $args = array() ) : string {
		return $this->jsonld_render( $breadcrumbs, $args );
	}
}
