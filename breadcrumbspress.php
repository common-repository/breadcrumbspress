<?php
/**
 * Plugin Name:       BreadcrumbsPress
 * Plugin URI:        https://plugins.dev4press.com/breadcrumbspress/
 * Description:       Breadcrumbs based navigation, fully responsive and customizable, supporting post types, all types of archives, 404 pages, search results and third party plugins.
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       breadcrumbspress
 * Version:           2.3
 * Requires at least: 6.1
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * == Copyright ==
 * Copyright 2008 - 2024 Milan Petrovic (email: support@dev4press.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */

use Dev4Press\v49\WordPress;

const BREADCRUMBSPRESS_PATH        = __DIR__ . '/';
const BREADCRUMBSPRESS_FILE        = __FILE__;
const BREADCRUMBSPRESS_BLOCKS_PATH = BREADCRUMBSPRESS_PATH . 'build/blocks/';

if ( ! defined( 'D4P_TAB' ) ) {
	define( 'D4P_TAB', "\t" );
}

define( 'BREADCRUMBSPRESS_URL', plugins_url( '/', BREADCRUMBSPRESS_FILE ) );

require_once( BREADCRUMBSPRESS_PATH . 'd4plib/core.php' );

require_once( BREADCRUMBSPRESS_PATH . 'core/autoload.php' );
require_once( BREADCRUMBSPRESS_PATH . 'core/bridge.php' );

breadcrumbspress_settings();

breadcrumbspress();

if ( WordPress::instance()->is_admin() ) {
	breadcrumbspress_admin();
}
