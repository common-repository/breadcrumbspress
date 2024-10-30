<?php

use Dev4Press\Plugin\BreadcrumbsPress\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\BreadcrumbsPress\Basic\Build;
use Dev4Press\Plugin\BreadcrumbsPress\Basic\Plugin;
use Dev4Press\Plugin\BreadcrumbsPress\Basic\Settings;
use Dev4Press\Plugin\BreadcrumbsPress\Crumbs\Generator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function breadcrumbspress() : Plugin {
	return Plugin::instance();
}

function breadcrumbspress_settings() : Settings {
	return Settings::instance();
}

function breadcrumbspress_admin() : AdminPlugin {
	return AdminPlugin::instance();
}

function breadcrumbspress_build() : Build {
	return Build::instance();
}

function breadcrumbspress_current( $args = array() ) : string {
	return breadcrumbspress_build()->current( $args );
}

function breadcrumbspress_is_current() : bool {
	return Generator::instance()->is();
}
