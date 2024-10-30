<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Data;

use Dev4Press\Plugin\BreadcrumbsPress\Expand\bbPress;
use WP_Post_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @method bool has_posts()
 * @method bool has_archive()
 * @method bool has_year()
 * @method bool has_month()
 * @method bool has_day()
 * @method bool has_author()
 * @method bool has_terms()
 * @method bool has_hierarchy()
 * @method bool has_parent_post()
 * @method string get_path()
 * @method string get_hierarchy()
 * @method string get_taxonomy()
 * @method string get_term_index()
 * @method bool get_with_posts()
 */
final class PostType {
	/** @var string */
	private $name;
	/** @var WP_Post_Type|null */
	private $object;
	/** @var \WP_Taxonomy[] */
	private $taxonomies;
	/** @var array */
	private $taxonomies_list;
	/** @var bool */
	private $knowledge = false;
	/** @var bool */
	private $knowledge_product = false;
	/** @var bool */
	private $bbpress = false;
	/** @var bool */
	private $woocommerce = false;

	private $rules = array(
		'path'       => '',
		'hierarchy'  => '',
		'taxonomy'   => '',
		'term_index' => '',
		'with_posts' => false,
	);

	private $data = array(
		'posts'       => false,
		'archive'     => false,
		'year'        => false,
		'month'       => false,
		'day'         => false,
		'author'      => false,
		'terms'       => false,
		'hierarchy'   => false,
		'parent_post' => false,
	);

	public function __construct( $post_type ) {
		$this->name   = $post_type;
		$this->object = get_post_type_object( $post_type );

		if ( $this->object ) {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );

			foreach ( $taxonomies as $taxonomy ) {
				if ( $taxonomy->public && $taxonomy->name != 'post_format' && $taxonomy->rewrite !== false ) {
					$this->taxonomies[ $taxonomy->name ]      = $taxonomy;
					$this->taxonomies_list[ $taxonomy->name ] = $taxonomy->label . ' (' . $taxonomy->name . ')';
				}
			}

			$this->data['terms'] = ! empty( $this->taxonomies );

			$this->data['archive']   = $this->object->has_archive !== false;
			$this->data['hierarchy'] = $this->object->hierarchical;

			if ( $post_type === 'post' ) {
				$this->data['posts']  = true;
				$this->data['year']   = true;
				$this->data['month']  = true;
				$this->data['day']    = true;
				$this->data['author'] = true;
			}

			if ( $post_type === 'attachment' ) {
				$this->data['parent_post'] = true;
			}

			if ( breadcrumbspress()->has_bbpress() ) {
				$this->bbpress = in_array( $post_type, bbPress::instance()->get_post_types() );
			}

			if ( breadcrumbspress()->has_woocommerce() ) {
				$this->woocommerce = $post_type === 'product';
			}

			add_action( 'breadcrumbspress_plugin_core_ready', array( $this, 'update' ) );
		}
	}

	public static function instance( $post_type ) : PostType {
		static $instance = array();

		if ( ! isset( $instance[ $post_type ] ) ) {
			$instance[ $post_type ] = new PostType( $post_type );
		}

		return $instance[ $post_type ];
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
		$this->rules['path']       = breadcrumbspress_settings()->get( 'cpt_' . $this->name, 'path' );
		$this->rules['hierarchy']  = breadcrumbspress_settings()->get( 'cpt_hierarchy_' . $this->name, 'rules' );
		$this->rules['taxonomy']   = breadcrumbspress_settings()->get( 'cpt_taxonomy_' . $this->name, 'rules' );
		$this->rules['term_index'] = breadcrumbspress_settings()->get( 'cpt_term_index_' . $this->name, 'rules' );
		$this->rules['with_posts'] = breadcrumbspress_settings()->get( 'cpt_with_posts_' . $this->name, 'rules' );

		if ( ! $this->has_hierarchy() ) {
			$this->rules['hierarchy'] = 'no';
		}

		if ( breadcrumbspress()->allowed_for_posts_page( $this->name ) ) {
			$this->data['posts'] = true;
		}
	}

	public function get_name() : string {
		return $this->name;
	}

	public function get_label() : string {
		return $this->object->label;
	}

	public function get_object() : WP_Post_Type {
		return $this->object;
	}

	public function get_taxonomies() : array {
		return $this->taxonomies;
	}

	public function get_first_taxonomy_name() : string {
		$names = $this->get_taxonomies_names();

		return empty( $names ) ? '' : $names[0];
	}

	public function get_taxonomies_list() : array {
		return $this->taxonomies_list;
	}

	public function get_taxonomies_names() : array {
		return empty( $this->taxonomies_list ) ? array() : array_keys( $this->taxonomies_list );
	}

	public function is_taxonomy_valid( $taxonomy ) : bool {
		return isset( $this->taxonomies_list[ $taxonomy ] );
	}

	public function is_bbpress() : bool {
		return $this->bbpress;
	}

	public function is_woocommerce() : bool {
		return $this->woocommerce;
	}
}
