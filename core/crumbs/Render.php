<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Crumbs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Render {
	private $seq = 0;

	public function __construct() {
	}

	public static function instance() : Render {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Render();
		}

		return $instance;
	}

	private function _aria_label() {
		return apply_filters( 'breadcrumbspress_nav_aria_label', 'breadcrumb' );
	}

	private function _sequence_id() : string {
		$this->seq ++;

		return 'breadcrumbs-seq-' . $this->seq;
	}

	/**
	 * @param \Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb[] $breadcrumbs
	 * @param array                                           $args
	 *
	 * @return string
	 */
	public function html( array $breadcrumbs, array $args = array() ) : string {
		$defaults = array(
			'list'      => 'ol',
			'id'        => '',
			'class'     => '',
			'font_size' => '',
			'align'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$id      = empty( $args['id'] ) ? $this->_sequence_id() : $args['id'];
		$list    = $args['list'] === 'ol' ? 'ol' : 'li';
		$classes = array( 'breadcrumbs-block-wrapper' );

		if ( ! empty( $args['class'] ) ) {
			$classes[] = $args['class'];
		}

		$render = '<div id="' . $id . '" class="' . join( ' ', $classes ) . '">';
		$render .= '<nav role="navigation" aria-label="' . $this->_aria_label() . '">';
		$render .= '<' . $list . '>';

		$i = 0;

		foreach ( $breadcrumbs as $crumb ) {
			if ( $crumb ) {
				$extra = '';

				$render .= '<li class="' . $crumb->get_css_classes( $i + 1, $extra ) . '">';

				if ( ! $crumb->is_current() && ! empty( $crumb->url ) ) {
					$render .= '<a href="' . $crumb->url . '"' . $crumb->get_tag_attributes() . '>' . $crumb->get_display() . '</a>';

					if ( $i < count( $breadcrumbs ) - 1 ) {
						$render .= Builder::instance()->get_separator();
					}
				} else {
					$render .= '<span aria-current="page">' . $crumb->get_display() . '</span>';
				}

				$render .= '</li>';

				$i ++;
			}
		}

		$render .= '</' . $list . '>';
		$render .= '</nav>';
		$render .= $this->vars_override( $args, $id );
		$render .= '</div>';

		return $render;
	}

	/**
	 * @param \Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb[] $breadcrumbs
	 * @param string                                          $name
	 * @param bool                                            $pretty_print
	 *
	 * @return string
	 */
	public function single_jsonld( array $breadcrumbs, string $name = '', bool $pretty_print = true ) : string {
		$jsonld = $this->jsonld_section( $breadcrumbs, $name );

		$render = '<script type="application/ld+json">';

		if ( $pretty_print ) {
			$render .= json_encode( $jsonld, JSON_PRETTY_PRINT );
		} else {
			$render .= json_encode( $jsonld );
		}

		$render .= '</script>';

		return $render;
	}

	public function vars_override( $args, $id ) : string {
		$vars = array();

		if ( ! empty( $args['align'] ) ) {
			$vars[] = '--breadcrumbspress-base-block-align: ' . $args['align'] . ';';
		}

		if ( ! empty( $args['font_size'] ) ) {
			$vars[] = '--breadcrumbspress-base-font-size: ' . $args['font_size'] . 'px;';
		}

		if ( ! empty( $vars ) ) {
			$var_id = '#' . $id;

			return '<style>' . PHP_EOL . $var_id . ' {' . PHP_EOL . D4P_TAB . join( PHP_EOL . D4P_TAB, $vars ) . PHP_EOL . '}' . PHP_EOL . '</style>';
		}

		return '';
	}

	private function jsonld_section( array $breadcrumbs, string $name = '' ) : array {
		$items = array();
		$order = 1;

		foreach ( $breadcrumbs as $crumb ) {
			if ( ! empty( $crumb->url ) ) {
				$item = array(
					'@type'    => 'ListItem',
					'position' => $order,
					'name'     => $crumb->get_title(),
				);

				if ( ! $crumb->is_current() ) {
					$item['item'] = $crumb->url;
				}

				$items[] = $item;

				$order ++;
			}
		}

		$jsonld = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);

		if ( ! empty( $name ) ) {
			$jsonld['name'] = $name;
		}

		return $jsonld;
	}
}
