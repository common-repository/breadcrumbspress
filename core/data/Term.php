<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Data;

use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @method int get_id()
 * @method int get_parent_id()
 * @method string get_taxonomy()
 * @method string get_slug()
 */
final class Term {
	/** @var WP_Term|null */
	private $object;

	private $data = array(
		'id'        => 0,
		'parent_id' => 0,
		'slug'      => '',
		'taxonomy'  => '',
	);

	public function __construct( $term = null ) {
		$this->object = $term;

		if ( $term instanceof WP_Term ) {
			$this->data['id']        = $term->term_id;
			$this->data['parent_id'] = $term->parent;
			$this->data['slug']      = $term->slug;
			$this->data['taxonomy']  = $term->taxonomy;
		}
	}

	public static function instance( $term ) : Term {
		static $instance = array();

		$term = get_term( $term );

		if ( $term instanceof WP_Term ) {
			if ( ! isset( $instance[ $term->term_id ] ) ) {
				$instance[ $term->term_id ] = new Term( $term );
			}

			return $instance[ $term->term_id ];
		}

		return new Term( null );
	}

	public function __call( $name, $arguments ) {
		$name = strtolower( $name );

		if ( substr( $name, 0, 4 ) == 'get_' ) {
			$condition = substr( $name, 4 );

			if ( isset( $this->data[ $condition ] ) ) {
				return $this->data[ $condition ];
			}
		}

		return false;
	}

	public function is_valid() : bool {
		return $this->object instanceof WP_Term;
	}

	public function title() : string {
		return $this->is_valid() ? $this->object->name : '';
	}

	public function url() : string {
		$url = $this->is_valid() ? get_term_link( $this->object ) : '';

		return is_string( $url ) ? $url : '';
	}

	public function url_with_post_type( $post_type ) : string {
		return $this->url();
	}

	public function has_parent() : bool {
		return $this->object->parent > 0;
	}
}