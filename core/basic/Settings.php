<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use DateTime;
use Dev4Press\v49\Core\Plugins\Settings as BaseSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Settings extends BaseSettings {
	public $base = 'breadcrumbspress';

	public $titles = array(
		'settings' => array(),
		'titles'   => array(),
	);

	public $settings = array(
		'core'        => array(
			'activated' => 0,
		),
		'tracker'     => array(
			'current_last_date'      => '',
			'current_json_last_date' => '',
		),
		'settings'    => array(
			'woocommerce_disable_breadcrumbs' => false,
			'bbpress_disable_breadcrumbs'     => false,
			'include_rich_snippets'           => true,
			'markup_list_type'                => 'ol',
			'crumbs_case_change'              => 'asis',
			'override_title'                  => false,
			'override_display'                => false,
			'home_crumb'                      => true,
			'home_element'                    => 'title',
			'home_custom'                     => '',
			'home_display'                    => 'element',
			'home_icon'                       => 'home',
			'home_html'                       => '',
			'home_url'                        => '',
			'woocommerce_hide_home_crumb'     => false,
			'woocommerce_show_root_crumb'     => true,
			'woocommerce_root_title'          => '',
			'woocommerce_root_display'        => 'element',
			'woocommerce_root_icon'           => 'store',
			'woocommerce_root_html'           => '',
			'bbpress_hide_home_crumb'         => false,
			'bbpress_root_title'              => '',
			'bbpress_root_display'            => 'element',
			'bbpress_root_icon'               => 'forums',
			'bbpress_root_html'               => '',
			'bbpress_directory_crumb'         => true,
		),
		'style'       => array(
			'base_font_size'       => '14px',
			'base_line_height'     => '20px',
			'base_crumb_margin'    => '.75em',
			'base_block_align'     => 'initial',
			'base_link_decoration' => 'none',
			'base_wrapper_padding' => '20px',
		),
		'plain'       => array(
			'separator_type'  => 'icon',
			'separator_char'  => '&raquo;',
			'separator_icon'  => 'crumb-double',
			'separator_ascii' => 'angle-double',
		),
		'integration' => array(
			'snippet'                       => false,
			'method'                        => 'manual',
			'action_name'                   => '',
			'action_priority'               => 10,
			'action_wrapper_class'          => '',
			'theme'                         => '',
			'theme_auto_detect_action'      => '',
			'theme_storefront_action'       => 'storefront_content_top',
			'theme_genesis_action'          => 'genesis_before_content',
			'theme_genesis_disable_default' => false,
			'theme_astra_action'            => 'astra_content_top',
			'theme_oceanwp_action'          => 'ocean_before_content_wrap',
			'theme_generatepress_action'    => 'generate_inside_site_container',
			'theme_kadence_action'          => 'kadence_before_content',
			'theme_blocksy_action'          => 'blocksy:content:before',
		),
		'visibility'  => array(
			'core_home'                => false,
			'core_front'               => false,
			'core_posts'               => true,
			'core_404'                 => true,
			'core_search'              => true,
			'core_archives'            => true,
			'core_date_archives'       => true,
			'core_author_archives'     => true,
			'bbpress_view'             => true,
			'bbpress_profile'          => true,
			'bbpress_search'           => true,
			'bbpress_topic_tag'        => true,
			'bbpress_topic_prefix'     => true,
			'bbpress_directory'        => true,
			'buddypress_members'       => true,
			'buddypress_profile'       => true,
			'buddypress_activity'      => true,
			'buddypress_group'         => true,
			'buddypress_groups'        => true,
			'buddypress_group_create'  => true,
			'woocommerce_account'      => true,
			'gd-knowledge-base_search' => true,
		),
		'title'       => array(
			'core_home'                  => '',
			'core_posts'                 => '',
			'core_404'                   => '',
			'core_search'                => '',
			'core_archives'              => '',
			'core_date_archives'         => '',
			'core_author_archives'       => '',
			'woocommerce_root'           => '',
			'woocommerce_cart'           => '',
			'woocommerce_checkout'       => '',
			'bbpress_root'               => '',
			'bbpress_view'               => '',
			'bbpress_search'             => '',
			'bbpress_search_results'     => '',
			'bbpress_directory'          => '',
			'bbpress_directory_profile'  => '',
			'bbpress_tag'                => '',
			'buddypress_groups'          => '',
			'buddypress_groups_group'    => '',
			'buddypress_group_create'    => '',
			'buddypress_group'           => '',
			'buddypress_members'         => '',
			'buddypress_members_profile' => '',
			'buddypress_activity'        => '',
			'gd-knowledge-base_search'   => '',
		),
		'display'     => array(
			'core_home'                  => '',
			'core_posts'                 => '',
			'core_404'                   => '',
			'core_search'                => '',
			'core_archives'              => '',
			'core_date_archives'         => '',
			'core_author_archives'       => '',
			'woocommerce_root'           => '',
			'woocommerce_cart'           => '',
			'woocommerce_checkout'       => '',
			'bbpress_root'               => '',
			'bbpress_view'               => '',
			'bbpress_search'             => '',
			'bbpress_search_results'     => '',
			'bbpress_directory'          => '',
			'bbpress_directory_profile'  => '',
			'bbpress_tag'                => '',
			'buddypress_groups'          => '',
			'buddypress_groups_group'    => '',
			'buddypress_group_create'    => '',
			'buddypress_group'           => '',
			'buddypress_members'         => '',
			'buddypress_members_profile' => '',
			'buddypress_activity'        => '',
			'gd-knowledge-base_search'   => '',
		),
		'path'        => array(),
		'rules'       => array(
			'date_with_posts'   => false,
			'author_with_posts' => false,
		),
	);

	protected function constructor() {
		$this->info = new Information();

		add_action( 'breadcrumbspress_load_settings', array( $this, 'init' ), 2 );
		add_action( 'breadcrumbspress_settings_init', array( $this, 'prepare' ) );

		add_filter( 'breadcrumbspress_blog_settings_get', array( $this, 'override_get' ), 10, 3 );
	}

	public function raw_get( $name, $group = 'settings', $default = null ) {
		if ( isset( $this->current[ $group ][ $name ] ) ) {
			$exit = $this->current[ $group ][ $name ];
		} else if ( isset( $this->settings[ $group ][ $name ] ) ) {
			$exit = $this->settings[ $group ][ $name ];
		} else {
			$exit = $default;
		}

		return $exit;
	}

	public function override_get( $exit, $name, $group ) {
		if ( $group == 'settings' && empty( $exit ) ) {
			if ( in_array( $name, array( 'home_custom', 'bbpress_root_title', 'woocommerce_root_title' ) ) ) {
				$exit = $this->titles['settings'][ $name ];
			}
		}

		return $exit;
	}

	public function prepare() {
		$this->titles['settings'] = array(
			'home_custom'            => _x( 'Home', 'Title for the Home page', 'breadcrumbspress' ),
			'bbpress_root_title'     => _x( 'Forums', 'Title for the bbPress main Forums page', 'breadcrumbspress' ),
			'woocommerce_root_title' => _x( 'Store', 'Title for the WooCommerce main page', 'breadcrumbspress' ),
		);

		$this->titles['titles'] = array(
			'core_posts'                 => _x( '%value%', 'Title for Posts page', 'breadcrumbspress' ),
			'core_404'                   => _x( '404 - Page Not Found', 'Title for 404 pages', 'breadcrumbspress' ),
			'core_search'                => _x( 'Search results for \'%value%\'', 'Title for Search Results pages', 'breadcrumbspress' ),
			'core_archives'              => _x( 'Archives', 'Title for Archive pages', 'breadcrumbspress' ),
			'core_var_archives'          => _x( 'Archives for \'%value%\'', 'Title for Various archive pages', 'breadcrumbspress' ),
			'core_date_archives'         => _x( 'Archives for \'%value%\'', 'Title for Date archives pages', 'breadcrumbspress' ),
			'core_author_archives'       => _x( 'Archives for \'%value%\'', 'Title for Author archives pages', 'breadcrumbspress' ),
			'woocommerce_cart'           => _x( '%value%', 'Title for WooCommerce Cart page', 'breadcrumbspress' ),
			'woocommerce_checkout'       => _x( '%value%', 'Title for WooCommerce Checkout page', 'breadcrumbspress' ),
			'buddypress_groups'          => _x( 'Groups Directory', 'Title for BuddyPress Groups Directory page', 'breadcrumbspress' ),
			'buddypress_groups_group'    => _x( 'Groups', 'Title for BuddyPress Groups crumb for Group page', 'breadcrumbspress' ),
			'buddypress_group_create'    => _x( 'Create A New Group', 'Title for BuddyPress New Group Create page', 'breadcrumbspress' ),
			'buddypress_group'           => _x( '%value%', 'Title for BuddyPress Group page', 'breadcrumbspress' ),
			'buddypress_members'         => _x( 'Members Directory', 'Title for BuddyPress Members Directory page', 'breadcrumbspress' ),
			'buddypress_members_profile' => _x( 'Members', 'Title for bbPress Members Directory crumb for Profile pages', 'breadcrumbspress' ),
			'buddypress_activity'        => _x( 'Recent Activity', 'Title for BuddyPress Activity Directory page', 'breadcrumbspress' ),
			'bbpress_search'             => _x( 'Search', 'Title for bbPress Search page', 'breadcrumbspress' ),
			'bbpress_search_results'     => _x( 'Search results for \'%value%\'', 'Title for bbPress Search Results pages', 'breadcrumbspress' ),
			'bbpress_view'               => _x( '%value%', 'Title for bbPress Topic Views pages', 'breadcrumbspress' ),
			'bbpress_tag'                => _x( '%tax%: %value%', 'Title for Topic Tags Crumb', 'breadcrumbspress' ),
			'bbpress_directory'          => _x( 'Members Directory', 'Title for bbPress Members Directory page', 'breadcrumbspress' ),
			'bbpress_directory_profile'  => _x( 'Members', 'Title for bbPress Members Directory crumb for Profile pages', 'breadcrumbspress' ),
			'gd-knowledge-base_search'   => _x( 'Search results for \'%value%\'', 'Title for GD Knowledge Base Search Results pages', 'breadcrumbspress' ),
		);

		$post_types = breadcrumbspress()->get_public_post_types();
		$taxonomies = breadcrumbspress()->get_public_taxonomies();

		foreach ( $post_types as $name => $post_type ) {
			$this->titles['titles'][ 'cpt_single_' . $name ]  = $name == 'attachment' ? _x( 'Attachment: %value%', 'Title for Single Posts pages', 'breadcrumbspress' ) : '%value%';
			$this->titles['titles'][ 'cpt_archive_' . $name ] = _x( 'Archive for \'%value%\'', 'Title for Posts Archives pages', 'breadcrumbspress' );

			$this->settings['visibility'][ 'cpt_single_' . $name ]  = true;
			$this->settings['visibility'][ 'cpt_archive_' . $name ] = $post_type->has_archive() !== false;
			$this->settings['title'][ 'cpt_single_' . $name ]       = '';
			$this->settings['title'][ 'cpt_archive_' . $name ]      = '';
			$this->settings['display'][ 'cpt_single_' . $name ]     = '';
			$this->settings['display'][ 'cpt_archive_' . $name ]    = '';
			$this->settings['rules'][ 'cpt_hierarchy_' . $name ]    = 'full';
			$this->settings['rules'][ 'cpt_taxonomy_' . $name ]     = ! empty( $post_type->has_terms() ) ? $post_type->get_first_taxonomy_name() : '';
			$this->settings['rules'][ 'cpt_term_index_' . $name ]   = 0;
			$this->settings['rules'][ 'cpt_with_posts_' . $name ]   = false;

			$_path = 'basic';

			if ( $name == 'post' ) {
				$_path = ! empty( $post_type->has_terms() ) ? 'taxonomy' : 'basic';
			} else if ( $post_type->has_archive() !== false ) {
				$_path = 'post_type';
			} else if ( breadcrumbspress()->allowed_for_post_types_with_parent( $name ) ) {
				$_path = 'parent_post';
			}

			$this->settings['path'][ 'cpt_' . $name ] = $_path;
		}

		foreach ( $taxonomies as $name => $taxonomy ) {
			if (
				breadcrumbspress()->has_bbpress() && (
					$name == bbp_get_topic_tag_tax_id() ||
					( breadcrumbspress()->has_gd_topic_prefix() && $name == gdtox()->taxonomy_prefix() )
				) ) {
				$this->titles['titles'][ 'tax_' . $name ] = _x( 'List of topics with \'%value%\' %tax%', 'Title for bbPress related Taxonomy Archives pages', 'breadcrumbspress' );
			} else if (
				breadcrumbspress()->has_woocommerce() && in_array( $name, array(
					'product_cat',
					'product_tag',
				) ) ) {
				$this->titles['titles'][ 'tax_' . $name ] = _x( 'Products with \'%value%\' %tax%', 'Title for WooCommerce related Taxonomy Archives pages', 'breadcrumbspress' );
			} else {
				$this->titles['titles'][ 'tax_' . $name ] = _x( 'Archives for \'%value%\' %tax%', 'Title for Taxonomy Archives pages', 'breadcrumbspress' );
			}

			$this->settings['visibility'][ 'tax_' . $name ]             = true;
			$this->settings['title'][ 'tax_' . $name ]                  = '';
			$this->settings['display'][ 'tax_' . $name ]                = '';
			$this->settings['rules'][ 'tax_hierarchy_' . $name ]        = 'full';
			$this->settings['rules'][ 'tax_with_posts_' . $name ]       = false;
			$this->settings['rules'][ 'tax_with_cpt_archive_' . $name ] = '';
			$this->settings['path'][ 'tax_' . $name ]                   = 'taxonomy';
		}
	}

	public function get_item_display( $name ) : string {
		if ( $this->get( 'override_display' ) ) {
			$display = $this->get( $name, 'display' );

			return _x( $display, "Display Override Translation", "breadcrumbspress" );
		} else {
			return '';
		}
	}

	public function get_item_default_title( $name ) : string {
		if ( isset( $this->titles['titles'][ $name ] ) ) {
			return $this->titles['titles'][ $name ];
		}

		return '';
	}

	public function get_item_title( $name ) : string {
		$title = $this->get( 'override_title' ) ? $this->get( $name, 'title', '' ) : '';

		if ( empty( $title ) ) {
			$title = $this->get( $name . '_title', 'settings', '' );

			if ( empty( $title ) ) {
				$title = $this->get_item_default_title( $name );
			}
		}

		return _x( $title, "Title Override Translation", "breadcrumbspress" );
	}

	public function track_current( $json = false ) {
		$day = date( 'Y-m-d' );

		if ( $json ) {
			if ( $this->get( 'current_json_last_date', 'tracker' ) !== $day ) {
				$this->set( 'current_json_last_date', $day, 'tracker', true );
			}
		} else {
			if ( $this->get( 'current_last_date', 'tracker' ) !== $day ) {
				$this->set( 'current_last_date', $day, 'tracker', true );
			}
		}
	}

	public function get_last_tracked_days() {
		$day = $this->get( 'current_last_date', 'tracker' );

		if ( empty( $day ) ) {
			return false;
		}

		$date = DateTime::createFromFormat( 'Y-m-d', $day );

		if ( $date ) {
			$today    = new DateTime();
			$interval = $date->diff( $today );

			if ( $interval ) {
				return $interval->days;
			}
		}

		return false;
	}
}