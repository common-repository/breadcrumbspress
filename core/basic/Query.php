<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Generator;
use Dev4Press\v49\Core\Quick\URL;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @method int get_year()
 * @method int get_month()
 * @method int get_day()
 * @method int get_author()
 * @method int get_post_id()
 * @method int get_term_id()
 * @method int get_bbp_user()
 * @method int get_bp_group()
 * @method int get_bp_user()
 * @method string get_search()
 * @method string get_url()
 * @method string get_post_type()
 * @method string get_taxonomy()
 * @method string get_title()
 * @method string get_woo_endpoint()
 * @method string get_bp_group_slug()
 * @method string get_bp_action()
 * @method string get_bp_action_var()
 * @method string get_bp_stack_component()
 * @method string get_bp_stack_action()
 * @method string get_bp_step();
 * @method string get_bbp_view()
 * @method string get_bbp_search()
 * @method string get_bbp_profile()
 * @method bool is_home()
 * @method bool is_front()
 * @method bool is_posts()
 * @method bool is_404()
 * @method bool is_search()
 * @method bool is_author()
 * @method bool is_archive()
 * @method bool is_date()
 * @method bool is_day()
 * @method bool is_month()
 * @method bool is_year()
 * @method bool is_post_type_single()
 * @method bool is_post_type_archive()
 * @method bool is_tax_archive()
 * @method bool is_bbpress()
 * @method bool is_bbpress_edit()
 * @method bool is_bbpress_root()
 * @method bool is_bbpress_user()
 * @method bool is_bbpress_search()
 * @method bool is_bbpress_search_results()
 * @method bool is_bbpress_view()
 * @method bool is_bbpress_topic_tag()
 * @method bool is_bbpress_profile()
 * @method bool is_bbpress_topic_prefix()
 * @method bool is_bbpress_directory()
 * @method bool is_woocommerce()
 * @method bool is_woocommerce_taxonomy()
 * @method bool is_woocommerce_product()
 * @method bool is_woocommerce_shop()
 * @method bool is_woocommerce_cart()
 * @method bool is_woocommerce_endpoint()
 * @method bool is_woocommerce_checkout()
 * @method bool is_woocommerce_account_page()
 * @method bool is_buddypress()
 * @method bool is_buddypress_groups_directory()
 * @method bool is_buddypress_group()
 * @method bool is_buddypress_group_create()
 * @method bool is_buddypress_members_directory()
 * @method bool is_buddypress_activity_directory()
 * @method bool is_buddypress_user()
 */
final class Query {
	private $home_page;
	private $home_page_id;
	private $posts_page_id;
	private $queried_object;
	private $post_type;
	private $taxonomy;

	private $data = array(
		'url'                => '',
		'title'              => '',
		'post_type'          => '',
		'taxonomy'           => '',
		'search'             => '',
		'woo_endpoint'       => '',
		'bp_group_slug'      => '',
		'bp_action'          => '',
		'bp_action_var'      => '',
		'bp_stack_component' => '',
		'bp_stack_action'    => '',
		'bp_step'            => '',
		'bbp_view'           => '',
		'bbp_search'         => '',
		'bbp_profile'        => '',
		'bbp_user'           => 0,
		'bp_group'           => 0,
		'bp_user'            => 0,
		'post_id'            => 0,
		'term_id'            => 0,
		'year'               => 0,
		'month'              => 0,
		'day'                => 0,
		'author'             => 0,
	);

	private $conditions = array(
		'home'                          => false,
		'front'                         => false,
		'posts'                         => false,
		'404'                           => false,
		'search'                        => false,
		'author'                        => false,
		'archive'                       => false,
		'date'                          => false,
		'day'                           => false,
		'month'                         => false,
		'year'                          => false,
		'post_type_single'              => false,
		'post_type_archive'             => false,
		'tax_archive'                   => false,
		'bbpress'                       => false,
		'bbpress_edit'                  => false,
		'bbpress_user'                  => false,
		'bbpress_root'                  => false,
		'bbpress_search'                => false,
		'bbpress_search_results'        => false,
		'bbpress_view'                  => false,
		'bbpress_topic_tag'             => false,
		'bbpress_profile'               => false,
		'bbpress_topic_prefix'          => false,
		'bbpress_directory'             => false,
		'woocommerce'                   => false,
		'woocommerce_taxonomy'          => false,
		'woocommerce_product'           => false,
		'woocommerce_shop'              => false,
		'woocommerce_cart'              => false,
		'woocommerce_endpoint'          => false,
		'woocommerce_checkout'          => false,
		'woocommerce_account_page'      => false,
		'buddypress'                    => false,
		'buddypress_groups_directory'   => false,
		'buddypress_group'              => false,
		'buddypress_group_create'       => false,
		'buddypress_members_directory'  => false,
		'buddypress_activity_directory' => false,
		'buddypress_user'               => false,
	);

	public function __construct() {
	}

	public static function instance() : Query {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Query();
			$instance->run();
		}

		return $instance;
	}

	public function __call( $name, $arguments ) {
		$name = strtolower( $name );

		if ( substr( $name, 0, 3 ) == 'is_' ) {
			$condition = substr( $name, 3 );

			if ( isset( $this->conditions[ $condition ] ) ) {
				return (bool) $this->conditions[ $condition ];
			}

			return false;
		} else if ( substr( $name, 0, 4 ) == 'get_' ) {
			$data = substr( $name, 4 );

			if ( isset( $this->data[ $data ] ) ) {
				return $this->data[ $data ];
			}

			return '';
		}

		return null;
	}

	private function run() {
		$this->home_page     = get_option( 'show_on_front' );
		$this->posts_page_id = absint( get_option( 'page_for_posts' ) );
		$this->home_page_id  = absint( get_option( 'page_on_front' ) );

		foreach ( array_keys( $this->conditions ) as $condition ) {
			if ( is_callable( array( $this, 'check_if_' . $condition ) ) ) {
				$this->conditions[ $condition ] = $this->{'check_if_' . $condition}();
			}
		}

		foreach ( array_keys( $this->conditions ) as $condition ) {
			if ( substr( $condition, 0, 11 ) === 'buddypress_' ) {
				if ( $this->conditions[ $condition ] ) {
					$this->conditions['buddypress'] = true;
					break;
				}
			}
		}

		$this->queried_object    = $this->wp_query()->get_queried_object();
		$this->data['post_type'] = $this->query_var( 'post_type' );
		$this->data['taxonomy']  = $this->query_var( 'taxonomy' );
		$this->data['year']      = $this->query_var( 'year' );
		$this->data['month']     = $this->query_var( 'monthnum' );
		$this->data['day']       = $this->query_var( 'day' );
		$this->data['url']       = URL::current_url();

		if ( $this->conditions['post_type_single'] || $this->conditions['posts'] ) {
			if ( $this->queried_object instanceof WP_Post ) {
				$this->data['post_type'] = $this->queried_object->post_type;
				$this->data['post_id']   = $this->queried_object->ID;
			}
		} else if ( $this->conditions['tax_archive'] ) {
			if ( $this->queried_object instanceof WP_Term ) {
				$this->data['taxonomy'] = $this->queried_object->taxonomy;
				$this->data['term_id']  = $this->queried_object->term_id;
			}
		}

		if ( $this->queried_object instanceof WP_Post ) {
			$this->data['title'] = get_the_title( $this->queried_object );
		} else if ( $this->queried_object instanceof WP_User ) {
			$this->data['author'] = $this->queried_object->ID;
		} else if ( $this->queried_object instanceof WP_Term ) {
			$this->data['title'] = $this->queried_object->name;
		}

		if ( $this->conditions['search'] ) {
			$this->data['search'] = get_search_query( false );
		}

		if ( $this->conditions['bbpress'] ) {
			if ( $this->conditions['bbpress_search_results'] ) {
				$this->data['bbp_search'] = bbp_get_search_terms();
			}

			if ( $this->conditions['bbpress_view'] ) {
				$this->data['bbp_view'] = $this->query_var( 'bbp_view' );
			}

			if ( $this->conditions['bbpress_topic_prefix'] && $this->conditions['post_type_single'] ) {
				$_prefix               = $this->query_var( 'topic-prefix' );
				$_prefix               = get_term_by( 'slug', $_prefix, gdtox()->taxonomy_prefix() );
				$this->data['term_id'] = $_prefix->term_id;
			}

			if ( $this->conditions['bbpress_user'] ) {
				$_user_page = 'profile';

				if ( bbp_is_single_user_topics() ) {
					$_user_page = 'topics';
				} else if ( bbp_is_single_user_replies() ) {
					$_user_page = 'replies';
				} else if ( bbp_is_single_user_edit() ) {
					$_user_page = 'edit';
				} else if ( bbp_is_single_user_engagements() ) {
					$_user_page = 'engagements';
				} else if ( bbp_is_subscriptions() ) {
					$_user_page = 'subscriptions';
				} else if ( bbp_is_favorites() ) {
					$_user_page = 'favorites';
				}

				$this->data['bbp_profile'] = $_user_page;
				$this->data['bbp_user']    = bbp_get_user_id();
			}
		}

		if ( $this->conditions['buddypress'] ) {
			$this->data['bp_action'] = bp_current_action();

			if ( bp_action_variable() ) {
				$this->data['bp_action_var'] = bp_action_variable();
			}

			if ( $this->conditions['buddypress_group'] ) {
				$this->data['bp_group']      = bp_get_current_group_id();
				$this->data['bp_group_slug'] = bp_get_current_group_slug();
			}

			if ( $this->conditions['buddypress_user'] ) {
				$this->data['bp_user'] = buddypress()->displayed_user->id;
			}

			if ( $this->conditions['buddypress_group_create'] ) {
				$this->data['bp_step'] = bp_get_groups_current_create_step();
			}

			$this->data['bp_stack_component'] = isset( buddypress()->canonical_stack['component'] ) ? buddypress()->canonical_stack['component'] : '';
			$this->data['bp_stack_action']    = isset( buddypress()->canonical_stack['action'] ) ? buddypress()->canonical_stack['action'] : '';
		}

		if ( $this->conditions['woocommerce'] ) {
			$this->conditions['woocommerce_product'] = $this->conditions['post_type_single'] && $this->get_post_type() == 'product';
		}

		if ( $this->conditions['woocommerce_endpoint'] ) {
			$this->data['woo_endpoint'] = $this->init_woocommerce_endpoint();
		}
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function is_single( $post_type ) : bool {
		return $this->is_post_type_single() && $this->get_post_type() == $post_type;
	}

	/** @return WP_Query $wp_query */
	public function wp_query() : WP_Query {
		global $wp_query;

		return $wp_query;
	}

	public function query_var( $query_var, $default = '' ) {
		return $this->wp_query()->get( $query_var, $default );
	}

	public function get_queried_object() {
		return $this->queried_object;
	}

	public function get_all_data() : array {
		return $this->data;
	}

	public function get_all_conditions() : array {
		return $this->conditions;
	}

	public function has_posts_page() : bool {
		return $this->posts_page_id > 0;
	}

	public function get_posts_page_id() : int {
		return $this->posts_page_id;
	}

	public function get_home_page_id() : int {
		return $this->home_page_id;
	}

	private function init_woocommerce_endpoint() : string {
		global $wp;

		$wc_endpoints = WC()->query->get_query_vars();

		foreach ( array_keys( $wc_endpoints ) as $key ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}

		return '';
	}

	private function check_if_home() : bool {
		$is = $this->home_page == 'posts' && is_home();

		return (bool) apply_filters( 'breadcrumbspress_query_is_home', $is );
	}

	private function check_if_front() : bool {
		$is = $this->home_page == 'page' && is_front_page();

		return (bool) apply_filters( 'breadcrumbspress_query_is_front', $is );
	}

	private function check_if_posts() : bool {
		$is = $this->home_page == 'page' && is_home();

		return (bool) apply_filters( 'breadcrumbspress_query_is_posts', $is );
	}

	private function check_if_404() : bool {
		$is = is_404();

		return (bool) apply_filters( 'breadcrumbspress_query_is_404', $is );
	}

	private function check_if_search() : bool {
		$is = is_search();

		return (bool) apply_filters( 'breadcrumbspress_query_is_search', $is );
	}

	private function check_if_author( $author = '' ) : bool {
		$is = is_author( $author );

		return (bool) apply_filters( 'breadcrumbspress_query_is_author', $is, $author );
	}

	private function check_if_archive() : bool {
		$is = is_archive();

		return (bool) apply_filters( 'breadcrumbspress_query_is_archive', $is );
	}

	private function check_if_date() : bool {
		$is = is_date();

		return (bool) apply_filters( 'breadcrumbspress_query_is_date', $is );
	}

	private function check_if_day() : bool {
		$is = is_day();

		return (bool) apply_filters( 'breadcrumbspress_query_is_day', $is );
	}

	private function check_if_month() : bool {
		$is = is_month();

		return (bool) apply_filters( 'breadcrumbspress_query_is_month', $is );
	}

	private function check_if_year() : bool {
		$is = is_year();

		return (bool) apply_filters( 'breadcrumbspress_query_is_year', $is );
	}

	private function check_if_post_type_single() : bool {
		$is = is_singular();

		return (bool) apply_filters( 'breadcrumbspress_query_is_post_type_single', $is );
	}

	private function check_if_post_type_archive() : bool {
		$is = is_post_type_archive();

		return (bool) apply_filters( 'breadcrumbspress_query_is_post_type_archive', $is );
	}

	private function check_if_tax_archive() : bool {
		$is = is_category() || is_tag() || is_tax();

		return (bool) apply_filters( 'breadcrumbspress_query_is_tax_archive', $is );
	}

	private function check_if_bbpress() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'is_bbpress' ) && is_bbpress();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress', $is );
	}

	private function check_if_bbpress_edit() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_edit' ) && bbp_is_edit();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_edit', $is );
	}

	private function check_if_bbpress_root() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_forum_archive' ) && bbp_is_forum_archive();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_root', $is );
	}

	private function check_if_bbpress_user() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_user_home' ) && function_exists( 'bbp_is_single_user' ) && ( bbp_is_user_home() || bbp_is_single_user() );

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_user', $is );
	}

	private function check_if_bbpress_search() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_search' ) && bbp_is_search();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_search', $is );
	}

	private function check_if_bbpress_search_results() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_search_results' ) && bbp_is_search_results();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_search_results', $is );
	}

	private function check_if_bbpress_view() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_single_view' ) && bbp_is_single_view();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_view', $is );
	}

	private function check_if_bbpress_topic_tag() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_topic_tag' ) && function_exists( 'bbp_is_topic_tag_edit' ) && ( bbp_is_topic_tag() || bbp_is_topic_tag_edit() );

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_topic_tag', $is );
	}

	private function check_if_bbpress_profile() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'bbp_is_user_home' ) && function_exists( 'bbp_is_single_user' ) && ( bbp_is_user_home() || bbp_is_single_user() );

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_profile', $is );
	}

	private function check_if_bbpress_topic_prefix() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'gdtox_is_topic_prefix' ) && gdtox_is_topic_prefix();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_topic_prefix', $is );
	}

	private function check_if_bbpress_directory() : bool {
		$is = breadcrumbspress()->has_bbpress() && function_exists( 'gdmed_is_members_directory' ) && gdmed_is_members_directory();

		return (bool) apply_filters( 'breadcrumbspress_query_is_bbpress_directory', $is );
	}

	private function check_if_woocommerce() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_woocommerce' ) && is_woocommerce();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce', $is );
	}

	private function check_if_woocommerce_shop() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_shop' ) && is_shop();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce_shop', $is );
	}

	private function check_if_woocommerce_cart() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_cart' ) && is_cart();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce_cart', $is );
	}

	private function check_if_woocommerce_endpoint() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce_endpoint', $is );
	}

	private function check_if_woocommerce_checkout() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_checkout' ) && is_checkout();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce_checkout', $is );
	}

	private function check_if_woocommerce_taxonomy() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_product_taxonomy' ) && is_product_taxonomy();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce_taxonomy', $is );
	}

	private function check_if_woocommerce_account_page() : bool {
		$is = breadcrumbspress()->has_woocommerce() && function_exists( 'is_account_page' ) && is_account_page();

		return (bool) apply_filters( 'breadcrumbspress_query_is_woocommerce_account_page', $is );
	}

	private function check_if_buddypress_groups_directory() : bool {
		$is = breadcrumbspress()->has_buddypress() && function_exists( 'bp_is_groups_directory' ) && bp_is_groups_directory();

		return (bool) apply_filters( 'breadcrumbspress_query_is_buddypress_groups_directory', $is );
	}

	private function check_if_buddypress_group_create() : bool {
		$is = breadcrumbspress()->has_buddypress() && function_exists( 'bp_is_group_create' ) && bp_is_group_create();

		return (bool) apply_filters( 'breadcrumbspress_query_is_buddypress_group_create', $is );
	}

	private function check_if_buddypress_group() : bool {
		$is = breadcrumbspress()->has_buddypress() && function_exists( 'bp_is_group' ) && bp_is_group();

		return (bool) apply_filters( 'breadcrumbspress_query_is_buddypress_group', $is );
	}

	private function check_if_buddypress_members_directory() : bool {
		$is = breadcrumbspress()->has_buddypress() && function_exists( 'bp_is_members_directory' ) && bp_is_members_directory();

		return (bool) apply_filters( 'breadcrumbspress_query_is_buddypress_members_directory', $is );
	}

	private function check_if_buddypress_activity_directory() : bool {
		$is = breadcrumbspress()->has_buddypress() && function_exists( 'bp_is_activity_directory' ) && bp_is_activity_directory();

		return (bool) apply_filters( 'breadcrumbspress_query_is_buddypress_activity_directory', $is );
	}

	private function check_if_buddypress_user() : bool {
		$is = breadcrumbspress()->has_buddypress() && function_exists( 'bp_is_user' ) && bp_is_user();

		return (bool) apply_filters( 'breadcrumbspress_query_is_buddypress_user', $is );
	}

	public function debug() {
		$output = array(
			'Query/Core'       => array(),
			'Query/Data'       => array_filter( $this->data ),
			'Query/Conditions' => array_filter( $this->conditions ),
			'Crumbs/Current'   => Generator::instance()->breadcrumbs(),
		);

		foreach (
			array(
				'home_page',
				'home_page_id',
				'posts_page_id',
				'queried_object',
				'post_type',
				'taxonomy',
			) as $key
		) {
			if ( ! is_null( $this->{$key} ) ) {
				$output['Query/Core'][ $key ] = $this->{$key};
			}
		}

		return $output;
	}
}
