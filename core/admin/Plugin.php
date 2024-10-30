<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Admin;

use Dev4Press\v49\Core\Admin\Submenu\Plugin as BasePlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin extends BasePlugin {
	public $plugin = 'breadcrumbspress';
	public $plugin_prefix = 'breadcrumbspress';
	public $plugin_menu = 'BreadcrumbsPress';
	public $plugin_title = 'Breadcrumbs Press';

	public $auto_mod_interface_colors = true;

	public $enqueue_wp = array( 'color_picker' => true );

	public static function instance() : Plugin {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	public function constructor() {
		$this->url  = BREADCRUMBSPRESS_URL;
		$this->path = BREADCRUMBSPRESS_PATH;
	}

	public function after_setup_theme() {
		$this->setup_items = array(
			'install' => array(
				'title' => __( 'Install', 'breadcrumbspress' ),
				'icon'  => 'ui-traffic',
				'type'  => 'setup',
				'info'  => __( 'Before you continue, make sure plugin installation was successful.', 'breadcrumbspress' ),
				'class' => '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Panel\\Install',
			),
			'update'  => array(
				'title' => __( 'Update', 'breadcrumbspress' ),
				'icon'  => 'ui-traffic',
				'type'  => 'setup',
				'info'  => __( 'Before you continue, make sure plugin was successfully updated.', 'breadcrumbspress' ),
				'class' => '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Panel\\Update',
			),
		);

		$this->menu_items = array(
			'dashboard' => array(
				'title' => __( 'Overview', 'breadcrumbspress' ),
				'icon'  => 'ui-home',
				'class' => '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Panel\\Dashboard',
			),
			'about'     => array(
				'title' => __( 'About', 'breadcrumbspress' ),
				'icon'  => 'ui-info',
				'class' => '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Panel\\About',
			),
			'settings'  => array(
				'title' => __( 'Settings', 'breadcrumbspress' ),
				'icon'  => 'ui-cog',
				'class' => '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Panel\\Settings',
			),
			'tools'     => array(
				'title' => __( 'Tools', 'breadcrumbspress' ),
				'icon'  => 'ui-wrench',
				'class' => '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Panel\\Tools',
			),
		);
	}

	public function run_getback() {
		new GetBack( $this );
	}

	public function run_postback() {
		new PostBack( $this );
	}

	public function settings() {
		return breadcrumbspress_settings();
	}

	public function settings_definitions() : Settings {
		return Settings::instance();
	}

	public function plugin() {
		return breadcrumbspress();
	}

	public function wizard() {
		return null;
	}

	public function register_scripts_and_styles() {
		$this->enqueue->register( 'css', 'breadcrumbspress-admin',
			array(
				'path' => 'css/',
				'file' => 'admin',
				'ext'  => 'css',
				'min'  => true,
				'ver'  => breadcrumbspress_settings()->file_version(),
				'src'  => 'plugin',
			) );
	}

	protected function extra_enqueue_scripts_plugin() {
		$this->enqueue->css( 'breadcrumbspress-admin' );
	}
}
