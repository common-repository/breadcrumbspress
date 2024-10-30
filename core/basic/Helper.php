<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\v49\Core\Quick\Arr;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helper {
	public function __construct() {
	}

	public static function instance() : Helper {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Helper();
		}

		return $instance;
	}

	public function user_title( int $user_id ) : string {
		$user = get_user_by( 'id', $user_id );
		$name = $user instanceof WP_User ? $user->display_name : _x( 'Unknown', 'Unknown User title', 'breadcrumbspress' );

		return apply_filters( 'breadcrumbspress_builder_user_title', $name, $user_id );
	}

	public function day_title( int $year, int $month, int $day ) : string {
		$date = date_i18n( get_option( 'date_format' ), strtotime( $day . '-' . $month . '-' . $year ) );

		return apply_filters( 'breadcrumbspress_builder_day_title', $date, $year, $month, $day );
	}

	public function month_title( int $year, int $month ) : string {
		global $wp_locale;

		$my_year  = $year;
		$my_month = $wp_locale->get_month( $month );

		return apply_filters( 'breadcrumbspress_builder_month_title', $my_month . ' ' . $my_year, $year, $month );
	}

	public function chain_terms( int $term_id, string $taxonomy, array $chain = array() ) : array {
		$term = get_term( $term_id, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			$chain[] = $term;

			$parent = $term->parent;

			if ( $parent != 0 ) {
				$chain = $this->chain_terms( $parent, $taxonomy, $chain );
			}
		}

		return $chain;
	}

	public function icon( string $name, string $type = 'icon', string $tag = 'i', string $class = '', bool $aria_hidden = true ) : string {
		$attributes = array(
			'class' => trim( 'breadcrumbspress-icon breadcrumbspress-' . $type . '-' . $name . ' ' . $class ),
		);

		if ( $aria_hidden ) {
			$attributes['aria-hidden'] = true;
		}

		return '<' . $tag . ' ' . Arr::to_html_attributes( $attributes ) . '></' . $tag . '>';
	}
}