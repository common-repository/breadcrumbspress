<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Admin\Panel;

use Dev4Press\v49\Core\UI\Admin\PanelSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends PanelSettings {
	public $settings_class = '\\Dev4Press\\Plugin\\BreadcrumbsPress\\Admin\\Settings';

	public function __construct( $admin ) {
		parent::__construct( $admin );

		$this->subpanels = $this->subpanels + array(
				'markup'      => array(
					'title'      => __( 'Markup', 'breadcrumbspress' ),
					'icon'       => 'ui-code',
					'break'      => __( 'Basic', 'breadcrumbspress' ),
					'break-icon' => 'ui-tasks',
					'info'       => __( 'Control markup aspects of the breadcrumbs, including separator for crumbs.', 'breadcrumbspress' ),
				),
				'tweaks'      => array(
					'title' => __( 'Tweaks', 'breadcrumbspress' ),
					'icon'  => 'ui-puzzle',
					'info'  => __( 'Control various other things related to displaying the breadcrumbs.', 'breadcrumbspress' ),
				),
				'integration' => array(
					'title' => __( 'Integration', 'breadcrumbspress' ),
					'icon'  => 'ui-plug',
					'info'  => __( 'Control integration of the plugin with support for various themes and frameworks.', 'breadcrumbspress' ),
				),
				'styling'     => array(
					'title' => __( 'Styling', 'breadcrumbspress' ),
					'icon'  => 'ui-palette',
					'info'  => __( 'Control visual elements, including sizes and colors for the breadcrumbs.', 'breadcrumbspress' ),
				),
				'advanced'    => array(
					'title' => __( 'Advanced', 'breadcrumbspress' ),
					'icon'  => 'ui-sliders',
					'info'  => __( 'Control various advanced settings related to the breadcrumbs and plugin.', 'breadcrumbspress' ),
				),
				'controls'    => array(
					'title'      => __( 'Controls', 'breadcrumbspress' ),
					'icon'       => 'ui-play',
					'break'      => __( 'Breadcrumbs', 'breadcrumbspress' ),
					'break-icon' => 'ui-chevron-right',
					'info'       => __( 'Basic settings for the breadcrumbs and special crumbs markup.', 'breadcrumbspress' ),
				),
				'visibility'  => array(
					'title' => __( 'Visibility', 'breadcrumbspress' ),
					'icon'  => 'ui-sun',
					'info'  => __( 'Control types of pages where the breadcrumbs will be visible.', 'breadcrumbspress' ),
				),
				'path'        => array(
					'title' => __( 'Main Paths', 'breadcrumbspress' ),
					'icon'  => 'ui-paper-plane',
					'info'  => __( 'Main breadcrumbs path for some types of pages.', 'breadcrumbspress' ),
				),
			);

		if ( breadcrumbspress_settings()->get( 'override_title' ) ) {
			$this->subpanels['title'] = array(
				'title' => __( 'Titles', 'breadcrumbspress' ),
				'icon'  => 'ui-ribbon',
				'info'  => __( 'Settings related to the individual crumbs titles format. If any of the titles is left empty, it will use auto generated value.', 'breadcrumbspress' ),
			);
		}

		if ( breadcrumbspress_settings()->get( 'override_display' ) ) {
			$this->subpanels['display'] = array(
				'title' => __( 'Display', 'breadcrumbspress' ),
				'icon'  => 'ui-code',
				'info'  => __( 'Settings related to the individual crumbs display override format. If any of the display values is left empty, it will use title value. Allowed use of HTML.', 'breadcrumbspress' ),
			);
		}
	}
}
