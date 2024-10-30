<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Data;

use WP_Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @method string get_path()
 * @method string get_hierarchy()
 * @method string get_with_cpt_archive()
 * @method bool has_hierarchy()
 * @method bool get_with_posts()
 */
final class Taxonomy {
	/** @var string */
	private $name;
	/** @var WP_Taxonomy|null */
	private $object;
	/** @var array */
	private $post_types = array();

	private $rules = array(
		'path'             => '',
		'hierarchy'        => '',
		'with_cpt_archive' => '',
		'with_posts'       => false,
	);

	private $data = array(
		'hierarchy' => false,
	);

	public function __construct( $taxonomy ) {
		$this->name   = $taxonomy;
		$this->object = get_taxonomy( $taxonomy );

		if ( $this->object ) {
			$this->post_types = $this->object->object_type;

			$this->data['hierarchy'] = $this->object->hierarchical;

			add_action( 'breadcrumbspress_plugin_core_ready', array( $this, 'update' ) );
		}
	}

	public static function instance( $taxonomy ) : Taxonomy {
		static $instance = array();

		if ( ! isset( $instance[ $taxonomy ] ) ) {
			$instance[ $taxonomy ] = new Taxonomy( $taxonomy );
		}

		return $instance[ $taxonomy ];
	}

	public function __call( $name, $arguments ) {
		$name = strtolower( $name );

		if ( substr( $name, 0, 4 ) == 'has_' ) {
			$condition = substr( $name, 4 );

			if ( isset( $this->data[ $condition ] ) ) {
				return (bool) $this->data[ $condition ];
			}

			return false;
		} else if ( substr( $name, 0, 4 ) == 'get_' ) {
			$condition = substr( $name, 4 );

			if ( isset( $this->rules[ $condition ] ) ) {
				return $this->rules[ $condition ];
			}

			return '';
		}

		return false;
	}

	public function update() {
		$this->rules['path']             = breadcrumbspress_settings()->get( 'tax_' . $this->name, 'path' );
		$this->rules['hierarchy']        = breadcrumbspress_settings()->get( 'tax_hierarchy_' . $this->name, 'rules' );
		$this->rules['with_posts']       = breadcrumbspress_settings()->get( 'tax_with_posts_' . $this->name, 'rules' );
		$this->rules['with_cpt_archive'] = breadcrumbspress_settings()->get( 'tax_with_cpt_archive_' . $this->name, 'rules' );

		if ( ! $this->has_hierarchy() ) {
			$this->rules['hierarchy'] = 'no';
		}
	}

	public function get_name() : string {
		return $this->name;
	}

	public function get_label() : string {
		return $this->object->label;
	}

	public function get_label_singular() : string {
		return $this->object->labels->singular_name;
	}

	public function get_object() : WP_Taxonomy {
		return $this->object;
	}

	public function get_post_types() : array {
		return $this->post_types;
	}
}
