<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Crumbs;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Helper;
use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;
use Dev4Press\Plugin\BreadcrumbsPress\Data\PostType;
use WP_Post;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @method string get_orientation()
 * @method string get_separator()
 * @method string get_url_home()
 * @method string get_url_forums()
 * @method string get_url_store()
 * @method string get_title_home()
 * @method string get_title_forums()
 * @method string get_title_store()
 */
class Builder {
	private $orientation;
	private $separator = '';

	private $url_home;
	private $url_forums;
	private $url_store;
	private $title_home;
	private $title_forums;
	private $title_store;

	public function __construct() {
		$this->orientation = is_rtl() ? 'rtl' : 'ltr';

		$this->url_home = breadcrumbspress_settings()->get( 'home_url' );

		if ( empty( $this->url_home ) ) {
			$this->url_home = site_url();
		}

		$this->title_home   = breadcrumbspress_settings()->get( 'home_element' ) == 'title' ? get_option( 'blogname' ) : breadcrumbspress_settings()->get( 'home_custom' );
		$this->title_forums = breadcrumbspress_settings()->get( 'bbpress_root_title' );
		$this->title_store  = breadcrumbspress_settings()->get( 'woocommerce_root_title' );

		if ( breadcrumbspress()->has_bbpress() && function_exists( 'bbp_get_forums_url' ) ) {
			$this->url_forums = bbp_get_forums_url();
		}

		if ( breadcrumbspress()->has_woocommerce() ) {
			$this->url_store = get_post_type_archive_link( 'product' );
		}

		switch ( breadcrumbspress_settings()->get( 'separator_type', 'plain' ) ) {
			case 'icon':
				$icon = breadcrumbspress_settings()->get( 'separator_icon', 'plain' );
				$icon = $this->orientation == 'rtl' ? 'left-' . $icon : 'right-' . $icon;

				$this->separator = Helper::instance()->icon( $icon, 'icon', 'i', 'breadcrumbs-separator breadcrumbs-sep-icon' );
				break;
			case 'ascii':
				$icon = breadcrumbspress_settings()->get( 'separator_ascii', 'plain' );
				$icon = $this->orientation == 'rtl' ? 'left-' . $icon : 'right-' . $icon;

				$this->separator = Helper::instance()->icon( $icon, 'ascii', 'i', 'breadcrumbs-separator breadcrumbs-sep-ascii' );
				break;
			case 'empty':
				$this->separator = '<span class="breadcrumbs-separator breadcrumbs-sep-empty" aria-hidden="true"></span>';
				break;
			case 'char':
				$this->separator = '<span class="breadcrumbs-separator breadcrumbs-sep-char" aria-hidden="true">' . breadcrumbspress_settings()->get( 'separator_char', 'plain' ) . '</span>';
				break;
			case 'none':
				$this->separator = '';
				break;
		}
	}

	public static function instance() : Builder {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Builder();
		}

		return $instance;
	}

	public function __call( $name, $arguments ) {
		$name = strtolower( $name );

		if ( substr( $name, 0, 4 ) == 'get_' ) {
			$condition = substr( $name, 4 );

			if ( isset( $this->{$condition} ) ) {
				return $this->{$condition};
			}
		}

		return false;
	}

	public function q() : Query {
		return Query::instance();
	}

	public function title( $name, $value = '', $default = '%value%', $tags = array() ) {
		$title = breadcrumbspress_settings()->get_item_title( $name );

		if ( empty( $title ) ) {
			$title = $default;
		}

		$output = str_replace( '%value%', $value, $title );

		foreach ( $tags as $tag => $replace ) {
			$output = str_replace( $tag, $replace, $output );
		}

		return $output;
	}

	public function display( $name, $value = '', $default = '%title%', $tags = array() ) {
		$display = breadcrumbspress_settings()->get_item_display( $name );

		if ( empty( $display ) ) {
			$display = $default;
		}

		$output = str_replace( '%value%', $value, $display );

		foreach ( $tags as $tag => $replace ) {
			$output = str_replace( $tag, $replace, $output );
		}

		return $output;
	}

	public function crumb( $crumb = array() ) : Crumb {
		return new Crumb( $crumb );
	}

	public function home() : Crumb {
		$title   = breadcrumbspress_settings()->get_item_title( 'core_home' );
		$display = breadcrumbspress_settings()->get_item_display( 'core_home' );

		if ( empty( $display ) ) {
			switch ( breadcrumbspress_settings()->get( 'home_display' ) ) {
				case 'icon':
					$display = Helper::instance()->icon( breadcrumbspress_settings()->get( 'home_icon' ) );
					break;
				case 'html':
					$display = breadcrumbspress_settings()->get( 'home_html' );
					break;
			}
		}

		if ( empty( $title ) ) {
			$title = $this->title_home;
		}

		if ( empty( $display ) ) {
			$display = '%title%';
		}

		return $this->crumb( array(
			'type'    => 'home',
			'title'   => $title,
			'display' => $display,
			'url'     => $this->url_home,
			'id'      => $this->q()->get_home_page_id(),
			'first'   => true,
		) );
	}

	public function forums() : Crumb {
		$display = '';

		if ( empty( $display ) ) {
			switch ( breadcrumbspress_settings()->get( 'bbpress_root_display' ) ) {
				case 'icon':
					$display = Helper::instance()->icon( breadcrumbspress_settings()->get( 'bbpress_root_icon' ) );
					break;
				case 'html':
					$display = breadcrumbspress_settings()->get( 'bbpress_root_html' );
					break;
			}
		}

		if ( empty( $display ) ) {
			$display = '%title%';
		}

		return $this->crumb( array(
			'type'    => 'home',
			'title'   => $this->title_forums,
			'display' => $display,
			'url'     => $this->url_forums,
		) );
	}

	public function post_type_archive( $post_type, $current = true ) : Crumb {
		$_type = PostType::instance( $post_type );

		return $this->crumb( array(
			'type'    => 'archive',
			'title'   => $current ? $this->title( 'cpt_archive_' . $post_type, $_type->get_label() ) : $_type->get_label(),
			'display' => $current ? $this->display( 'cpt_archive_' . $post_type ) : '%title%',
			'url'     => get_post_type_archive_link( $post_type ),
			'current' => $current,
		) );
	}

	/** @return Crumb[] */
	public function get_home_page() : array {
		return apply_filters( 'breadcrumbspress_builder_for_home_page', array(
			'home' => $this->home(),
		) );
	}

	/** @return Crumb[] */
	public function get_posts_page() : array {
		return apply_filters( 'breadcrumbspress_builder_for_posts_page', array(
			'home'  => $this->home(),
			'posts' => $this->crumb( array(
				'type'    => 'posts',
				'title'   => $this->title( 'core_posts', $this->q()->get_title() ),
				'display' => $this->display( 'core_posts' ),
				'url'     => get_permalink( Query::instance()->get_posts_page_id() ),
				'current' => true,
			) ),
		) );
	}

	/** @return Crumb[] */
	public function get_error_page() : array {
		return apply_filters( 'breadcrumbspress_builder_for_error', array(
			'home'  => $this->home(),
			'posts' => $this->crumb( array(
				'type'    => 'error',
				'title'   => $this->title( 'core_404' ),
				'display' => $this->display( 'core_404' ),
				'url'     => '',
				'current' => true,
			) ),
		) );
	}

	/**
	 * @param null|int $year
	 * @param bool     $use_template
	 *
	 * @return Crumb[]
	 */
	public function get_date_year_page( $year = null, $use_template = true ) : array {
		$year = is_null( $year ) ? $this->q()->get_year() : intval( $year );

		$breadcrumbs = array(
			'home'      => $this->home(),
			'date-year' => $this->crumb( array(
				'type'    => 'date-year',
				'title'   => $use_template ? $this->title( 'core_date_archives', $year ) : $year,
				'display' => $use_template ? $this->display( 'core_date_archives' ) : '%title%',
				'url'     => get_year_link( $year ),
				'current' => true,
			) ),
		);

		$breadcrumbs = $this->expand_with_posts_page( $breadcrumbs, 'date_with_posts' );

		return apply_filters( 'breadcrumbspress_builder_for_date_year_archive', $breadcrumbs, $year, $use_template );
	}

	/**
	 * @param null|int $year
	 * @param null|int $month
	 * @param bool     $use_template
	 *
	 * @return Crumb[]
	 */
	public function get_date_month_page( $year = null, $month = null, bool $use_template = true ) : array {
		$year  = is_null( $year ) ? $this->q()->get_year() : intval( $year );
		$month = is_null( $month ) ? $this->q()->get_month() : absint( $month );

		$month = $month < 1 || $month > 12 ? 1 : $month;
		$value = Helper::instance()->month_title( $year, $month );

		$breadcrumbs = $this->get_date_year_page( $year, false );
		$breadcrumbs['date-year']->set_current( false );
		$breadcrumbs['date-month'] = $this->crumb( array(
			'type'    => 'date-month',
			'title'   => $use_template ? $this->title( 'core_date_archives', $value ) : str_pad( $month, 2, '0', STR_PAD_LEFT ),
			'display' => $use_template ? $this->display( 'core_date_archives' ) : '%title%',
			'url'     => get_month_link( $year, $month ),
			'current' => true,
		) );

		$breadcrumbs = $this->expand_with_posts_page( $breadcrumbs, 'date_with_posts' );

		return apply_filters( 'breadcrumbspress_builder_for_date_month_archive', $breadcrumbs,
			$year, $month, $use_template );
	}

	/**
	 * @param null|int $year
	 * @param null|int $month
	 * @param null|int $day
	 * @param bool     $use_template
	 *
	 * @return Crumb[]
	 */
	public function get_date_day_page( $year = null, $month = null, $day = null, bool $use_template = true ) : array {
		$year  = is_null( $year ) ? $this->q()->get_year() : intval( $year );
		$month = is_null( $month ) ? $this->q()->get_month() : absint( $month );
		$day   = is_null( $day ) ? $this->q()->get_day() : absint( $day );

		$month = $month < 1 || $month > 12 ? 1 : $month;
		$day   = $day < 1 || $day > 31 ? 1 : $day;

		$value = Helper::instance()->day_title( $year, $month, $day );

		$breadcrumbs = $this->get_date_month_page( $year, $month, false );
		$breadcrumbs['date-month']->set_current( false );
		$breadcrumbs['date-day'] = $this->crumb( array(
			'type'    => 'date-day',
			'title'   => $use_template ? $this->title( 'core_date_archives', $value ) : str_pad( $day, 2, '0', STR_PAD_LEFT ),
			'display' => $use_template ? $this->display( 'core_date_archives' ) : '%title%',
			'url'     => get_day_link( $year, $month, $day ),
			'current' => true,
		) );

		$breadcrumbs = $this->expand_with_posts_page( $breadcrumbs, 'date_with_posts' );

		return apply_filters( 'breadcrumbspress_builder_for_date_day_archive', $breadcrumbs,
			$year, $month, $day, $use_template );
	}

	/**
	 * @param null|int $author
	 * @param bool     $use_template
	 *
	 * @return Crumb[]
	 */
	public function get_author_archive_page( $author = null, bool $use_template = true ) : array {
		$author = is_null( $author ) ? $this->q()->get_author() : absint( $author );

		$value = Helper::instance()->user_title( $author );

		$breadcrumbs = array(
			'home'   => $this->home(),
			'author' => $this->crumb( array(
				'type'    => 'author',
				'title'   => $use_template ? $this->title( 'core_author_archives', $value ) : $value,
				'display' => $use_template ? $this->display( 'core_author_archives' ) : '%title%',
				'url'     => get_author_posts_url( $author ),
				'current' => true,
			) ),
		);

		$breadcrumbs = $this->expand_with_posts_page( $breadcrumbs, 'author_with_posts' );

		return apply_filters( 'breadcrumbspress_builder_for_author_archive', $breadcrumbs, $author, $use_template );
	}

	/** @return Crumb[] */
	public function get_archive_page() : array {
		return apply_filters( 'breadcrumbspress_builder_for_archive', array(
			'home'  => $this->home(),
			'posts' => $this->crumb( array(
				'type'    => 'archive',
				'title'   => $this->title( 'core_archives' ),
				'display' => $this->display( 'core_archives' ),
				'url'     => '',
				'current' => true,
			) ),
		) );
	}

	/**
	 * @param string|null $post_type
	 *
	 * @return Crumb[]
	 */
	public function get_post_type_archive_page( $post_type = null ) : array {
		$post_type = is_null( $post_type ) ? $this->q()->get_post_type() : $post_type;

		$breadcrumbs = array(
			'home'  => $this->home(),
			'posts' => $this->post_type_archive( $post_type ),
		);

		$breadcrumbs = apply_filters( 'breadcrumbspress_builder_for_post_type_archive', $breadcrumbs, $post_type );
		$breadcrumbs = apply_filters( 'breadcrumbspress_builder_for_post_type_archive_' . $post_type, $breadcrumbs );

		return $breadcrumbs;
	}

	/**
	 * @param int|null $post_id
	 *
	 * @return Crumb[]
	 */
	public function get_post_type_single_page( $post_id = null ) : array {
		$post_id = is_null( $post_id ) ? $this->q()->get_post_id() : absint( $post_id );
		$post    = get_post( $post_id );

		if ( $post instanceof WP_Post ) {
			$breadcrumbs = Content::instance()->current( $post );

			$breadcrumbs = apply_filters( 'breadcrumbspress_builder_for_post_type_single', $breadcrumbs, $post_id, $post->post_type );
			$breadcrumbs = apply_filters( 'breadcrumbspress_builder_for_post_type_single_' . $post->post_type, $breadcrumbs, $post_id );

			return $breadcrumbs;
		}

		return array();
	}

	/**
	 * @param int|null    $term_id
	 * @param string|null $taxonomy
	 *
	 * @return Crumb[]
	 */
	public function get_taxonomy_term_archive_page( $term_id = null, $taxonomy = null ) : array {
		$term_id  = is_null( $term_id ) ? $this->q()->get_term_id() : absint( $term_id );
		$taxonomy = is_null( $taxonomy ) ? $this->q()->get_taxonomy() : absint( $taxonomy );
		$term     = get_term( $term_id, $taxonomy );

		if ( $term instanceof WP_Term ) {
			$breadcrumbs = Archives::instance()->current( $term );

			$breadcrumbs = apply_filters( 'breadcrumbspress_builder_for_taxonomy_term', $breadcrumbs, $term_id, $taxonomy );
			$breadcrumbs = apply_filters( 'breadcrumbspress_builder_for_taxonomy_term_' . $taxonomy, $breadcrumbs, $term_id );

			return $breadcrumbs;
		}

		return array();
	}

	/**
	 * @param string|null $query
	 * @param bool        $use_template
	 *
	 * @return Crumb[]
	 */
	public function get_search_results_page( $query = null, bool $use_template = true ) : array {
		$query = is_null( $query ) ? $this->q()->get_search() : $query;
		$value = esc_html( $query );

		return apply_filters( 'breadcrumbspress_builder_for_search_results', array(
			'home'  => $this->home(),
			'posts' => $this->crumb( array(
				'type'    => 'search',
				'title'   => $use_template ? $this->title( 'core_search', $value ) : $value,
				'display' => $use_template ? $this->display( 'core_search' ) : '%title%',
				'url'     => get_search_link( $query ),
				'current' => true,
			) ),
		), $query, $use_template );
	}

	public function expand_with_posts_page( $breadcrumbs, $key ) {
		if ( $this->q()->has_posts_page() ) {
			if ( apply_filters( 'breadcrumbspress_builder_posts_crumb_' . $key, breadcrumbspress_settings()->get( $key, 'rules', false ) ) ) {
				$breadcrumbs = array_slice( $breadcrumbs, 0, 1, true ) +
				               array( 'posts' => Content::instance()->crumbs_posts_page() ) +
				               array_slice( $breadcrumbs, 1, null, true );
			}
		}

		return $breadcrumbs;
	}
}