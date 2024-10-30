<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Data;

use Dev4Press\Plugin\GDKOB\Front\Link;
use Dev4Press\Plugin\GDKOB\KB\Content;
use WP_Post;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @method string get_year_formatted()
 * @method string get_month_formatted()
 * @method string get_day_formatted()
 * @method int get_year()
 * @method int get_month()
 * @method int get_day()
 * @method int get_id()
 * @method int get_parent_id()
 * @method string get_post_type()
 */
final class Post {
	/** @var WP_Post|null */
	private $object;
	/** @var PostType|null */
	private $post_type;
	/** @var \WP_User|false */
	private $author;
	/** @var bool */
	private $is_kob = false;

	private $data = array(
		'id'              => 0,
		'parent_id'       => 0,
		'post_type'       => '',
		'day'             => 0,
		'month'           => 0,
		'year'            => 0,
		'day_formatted'   => 0,
		'month_formatted' => 0,
		'year_formatted'  => 0,
	);

	public function __construct( $post = null ) {
		$this->object = $post;

		if ( $post instanceof WP_Post ) {
			$this->post_type = PostType::instance( $post->post_type );
			$this->author    = get_user_by( 'id', $post->post_author );

			$this->data['id']              = $post->ID;
			$this->data['parent_id']       = $post->post_parent !== $post->ID ? $post->post_parent : 0;
			$this->data['post_type']       = $post->post_type;
			$this->data['day']             = absint( get_the_date( 'd', $this->object ) );
			$this->data['month']           = absint( get_the_date( 'm', $this->object ) );
			$this->data['year']            = absint( get_the_date( 'Y', $this->object ) );
			$this->data['day_formatted']   = str_pad( get_the_date( 'd', $this->object ), 2, '0', STR_PAD_LEFT );
			$this->data['month_formatted'] = str_pad( get_the_date( 'm', $this->object ), 2, '0', STR_PAD_LEFT );
			$this->data['year_formatted']  = get_the_date( 'Y', $this->object );
		}
	}

	public static function instance( $post ) : Post {
		static $instance = array();

		$post = get_post( $post );

		if ( $post instanceof WP_Post ) {
			if ( ! isset( $instance[ $post->ID ] ) ) {
				$instance[ $post->ID ] = new Post( $post );
			}

			return $instance[ $post->ID ];
		}

		return new Post( null );
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
		return $this->object instanceof WP_Post;
	}

	public function is_author_valid() : bool {
		return $this->author !== false;
	}

	public function title() : string {
		return $this->is_valid() ? get_the_title( $this->object ) : '';
	}

	public function url() : string {
		$url = $this->is_valid() ? get_permalink( $this->object ) : '';

		return is_string( $url ) ? $url : '';
	}

	public function author_url() : string {
		if ( $this->is_valid() && $this->is_author_valid() ) {
			if ( $this->is_kob ) {
				return Link::instance()->author_archive( $this->object->post_author );
			} else {
				return get_author_posts_url( $this->object->post_author );
			}
		}

		return '';
	}

	public function author_display() : string {
		return $this->is_valid() && $this->is_author_valid() ? $this->author->display_name : '';
	}

	public function author_nicename() : string {
		return $this->is_valid() && $this->is_author_valid() ? $this->author->user_nicename : '';
	}

	/** @return WP_Term[] */
	public function get_terms( string $taxonomy ) : array {
		$_raw = get_the_terms( $this->object->ID, $taxonomy );

		return is_array( $_raw ) && ! empty( $_raw ) ? $_raw : array();
	}

	/** @return WP_Term|bool */
	public function get_first_term( string $taxonomy ) {
		$terms = $this->get_terms( $taxonomy );

		return reset( $terms );
	}
}
