<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\Plugin\BreadcrumbsPress\Blocks\Register;
use Dev4Press\Plugin\BreadcrumbsPress\Data\PostType;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Taxonomy;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\bbPress;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\BuddyPress;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\GDContentTools;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\GDKnowledgeBase;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\WooCommerce;
use Dev4Press\v49\Core\Plugins\Core;
use Dev4Press\v49\Core\Quick\BBP;
use Dev4Press\v49\Core\Quick\BP;
use Dev4Press\v49\Core\Quick\WPR;
use Dev4Press\v49\Core\Shared\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin extends Core {
	public $plugin = 'breadcrumbspress';

	private $_posts_page_post_types;
	private $_post_types_with_parent;

	private $_plugins = array(
		'bbpress'                          => false,
		'buddypress'                       => false,
		'woocommerce'                      => false,
		'gd-content-tools'                 => false,
		'gd-knowledge-base'                => false,
		'gd-topic-prefix'                  => false,
		'gd-members-directory-for-bbpress' => false,
	);

	public function __construct() {
		$this->url  = BREADCRUMBSPRESS_URL;
		$this->path = BREADCRUMBSPRESS_PATH;

		parent::__construct();
	}

	public function s() {
		return breadcrumbspress_settings();
	}

	public function f() {
		return null;
	}

	public function run() {
		add_action( 'init', array( $this, 'init' ), 20 );
		add_action( 'template_redirect', array( $this, 'query' ) );

		Register::instance();
		Enqueue::init();

		add_action( 'd4plib_shared_enqueue_prepare', array( $this, 'register_css_and_js' ) );
	}

	public function init() {
		$this->plugins_check();

		do_action( 'breadcrumbspress_plugin_preparation' );

		$this->_posts_page_post_types  = apply_filters( 'breadcrumbspress_include_posts_page_for_post_types', array( 'post' ) );
		$this->_post_types_with_parent = apply_filters( 'breadcrumbspress_listing_post_types_with_parents', array( 'attachment' ) );

		do_action( 'breadcrumbspress_load_settings' );

		if ( ! is_admin() && breadcrumbspress_settings()->get( 'method', 'integration' ) != 'manual' ) {
			Loader::instance();
		}

		if ( $this->has_buddypress() ) {
			BuddyPress::instance();
		}

		if ( $this->has_bbpress() ) {
			bbPress::instance();
		}

		if ( $this->has_woocommerce() ) {
			WooCommerce::instance();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_files' ) );

		Shortcodes::instance();

		do_action( 'breadcrumbspress_plugin_core_ready' );
	}

	public function plugins_check() {
		$this->_plugins['bbpress']                          = BBP::is_active();
		$this->_plugins['buddypress']                       = BP::is_active();
		$this->_plugins['woocommerce']                      = WPR::is_plugin_active( 'woocommerce/woocommerce.php' );
		$this->_plugins['gd-topic-prefix']                  = WPR::is_plugin_active( 'gd-topic-prefix/gd-topic-prefix.php' ) && defined( 'GDTOX_PATH' );
		$this->_plugins['gd-members-directory-for-bbpress'] = WPR::is_plugin_active( 'gd-members-directory-for-bbpress/gd-members-directory-for-bbpress.php' ) && defined( 'GDMED_PATH' );
	}

	public function allowed_for_posts_page( $post_type ) : bool {
		$post_type = (array) $post_type;

		foreach ( $post_type as $cpt ) {
			if ( in_array( $cpt, $this->_posts_page_post_types ) ) {
				return true;
			}
		}

		return false;
	}

	public function allowed_for_post_types_with_parent( $post_type ) : bool {
		return in_array( $post_type, $this->_post_types_with_parent );
	}

	public function query() {
		if ( ! is_admin() ) {
			Query::instance();

			add_action( 'debugpress-tracker-plugins-call', array( $this, 'debugpress' ) );

			if ( $this->s()->get( 'snippet', 'integration' ) ) {
				add_action( 'wp_head', array( $this, 'header_snippet' ), 90 );
			}
		}
	}

	public function header_snippet() {
		breadcrumbspress_build()->current_json_only();
	}

	public function debugpress() {
		if ( function_exists( 'debugpress_store_for_plugin' ) ) {
			debugpress_store_for_plugin( BREADCRUMBSPRESS_FILE, Query::instance()->debug() );
		}
	}

	public function register_css_and_js() {
		$file = 'breadcrumbspress' . ( Enqueue::i()->is_rtl() ? '-rtl' : '' );

		Enqueue::i()->add_css( 'breadcrumbspress', array(
			'lib'  => false,
			'url'  => $this->url . 'css/',
			'file' => $file,
			'ver'  => $this->s()->file_version(),
			'ext'  => 'css',
			'min'  => true,
			'int'  => array(),
			'req'  => array(),
		) );
	}

	public function enqueue_styling() {
		wp_enqueue_style( 'breadcrumbspress' );
		wp_add_inline_style( 'breadcrumbspress', $this->vars_styling_override() );
	}

	public function enqueue_files() {
		$this->enqueue_styling();
	}

	private function vars_styling_override() : string {
		$check = array(
			'font-size'       => 'base_font_size',
			'line-height'     => 'base_line_height',
			'crumb-margin'    => 'base_crumb_margin',
			'block-align'     => 'base_block_align',
			'link-decoration' => 'base_link_decoration',
			'wrapper-padding' => 'base_wrapper_padding',
		);

		$vars = array();

		foreach ( $check as $var => $name ) {
			$default = breadcrumbspress_settings()->get_default( $name, 'style' );
			$actual  = breadcrumbspress_settings()->get( $name, 'style' );

			if ( $default != $actual ) {
				$vars[ '--breadcrumbspress-base-' . $var ] = $actual;
			}
		}

		$vars = apply_filters( 'breadcrumbspress_css_variables', $vars );

		$render = array();

		foreach ( $vars as $var => $value ) {
			$render[] = $var . ': ' . $value . ';';
		}

		if ( empty( $render ) ) {
			return '';
		}

		return ':root {' . PHP_EOL . D4P_TAB . join( PHP_EOL . D4P_TAB, $render ) . PHP_EOL . '}';
	}

	/**
	 * @return PostType[]
	 */
	public function get_public_post_types() : array {
		global $wp_post_types;

		$list = array();

		foreach ( $wp_post_types as $post_type ) {
			if ( $post_type->public ) {
				$list[ $post_type->name ] = PostType::instance( $post_type->name );
			}
		}

		return $list;
	}

	/**
	 * @return Taxonomy[]
	 */
	public function get_public_taxonomies() : array {
		global $wp_taxonomies;

		$list = array();

		foreach ( $wp_taxonomies as $taxonomy ) {
			if ( $taxonomy->public ) {
				$list[ $taxonomy->name ] = Taxonomy::instance( $taxonomy->name );
			}
		}

		return $list;
	}

	public function normalize_title( $title ) : string {
		switch ( breadcrumbspress_settings()->get( 'crumbs_case_change' ) ) {
			case 'first':
				$title = ucfirst( $title );
				break;
			case 'words':
				$title = ucwords( $title );
				break;
			case 'lower':
				$title = strtolower( $title );
				break;
			case 'upper':
				$title = strtoupper( $title );
				break;
		}

		return $title;
	}

	public function supported_themes() : array {
		return array(
			'astra'         => __( 'Astra', 'breadcrumbspress' ),
			'blocksy'       => __( 'Blocky', 'breadcrumbspress' ),
			'generatepress' => __( 'GeneratePress', 'breadcrumbspress' ),
			'genesis'       => __( 'Genesis', 'breadcrumbspress' ),
			'oceanwp'       => __( 'OceanWP', 'breadcrumbspress' ),
			'kadence'       => __( 'Kadence', 'breadcrumbspress' ),
			'storefront'    => __( 'Storefront', 'breadcrumbspress' ),
		);
	}

	public function has_bbpress() : bool {
		return $this->_plugins['bbpress'];
	}

	public function has_buddypress() : bool {
		return $this->_plugins['buddypress'];
	}

	public function has_woocommerce() : bool {
		return $this->_plugins['woocommerce'];
	}

	public function has_gd_topic_prefix() : bool {
		return $this->_plugins['gd-topic-prefix'];
	}

	public function has_gd_members_directory() : bool {
		return $this->_plugins['gd-members-directory-for-bbpress'];
	}

	public function b() {
		return null;
	}

	public function l() {
		return null;
	}
}
