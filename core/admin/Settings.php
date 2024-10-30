<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Admin;

use Dev4Press\Plugin\BreadcrumbsPress\Data\PostType;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Taxonomy;
use Dev4Press\v49\Core\Options\Element as EL;
use Dev4Press\v49\Core\Options\Settings as BaseSettings;
use Dev4Press\v49\Core\Options\Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends BaseSettings {
	public $posts_page = 0;

	protected function value( $name, $group = 'settings', $default = null ) {
		return breadcrumbspress_settings()->raw_get( $name, $group, $default );
	}

	protected function init() {
		$this->posts_page = absint( get_option( 'page_for_posts' ) );

		$post_types = breadcrumbspress()->get_public_post_types();
		$taxonomies = breadcrumbspress()->get_public_taxonomies();

		$theme = get_template();
		$valid = $this->get_integration_themes();

		if ( isset( $valid[ $theme ] ) ) {
			$method            = 'get_' . $theme . '_actions';
			$_integration_auto = array(
				EL::info( __( 'Theme', 'breadcrumbspress' ), sprintf( __( 'You are using %s theme', 'breadcrumbspress' ), '<strong>' . $valid[ $theme ] . '</strong>' ) ),
				EL::i( 'integration', 'theme_auto_detect_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_auto_detect_action', 'integration' ) )->data( 'array', $this->$method() ),
			);
		} else {
			$_integration_auto = array(
				EL::info( __( 'Error', 'breadcrumbspress' ), __( 'The theme you are using is not supported for automatic integration.', 'breadcrumbspress' ) . ' ' . __( 'If you think that you are using one of the supported themes, please change the Integration option and choose your theme manually.', 'breadcrumbspress' ) ),
				EL::info( __( 'Themes', 'breadcrumbspress' ), sprintf( __( 'The currently supported themes are: %s.', 'breadcrumbspress' ), join( ', ', $valid ) ) ),
			);
		}

		$this->settings = array(
			'markup'      => array(
				'markup_root'     => array(
					'name'     => __( 'Breadcrumbs Separator', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'plain', 'separator_type', __( 'Type', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'separator_type', 'plain' ) )->data( 'array', $this->get_separator_types() )->switch( array(
									'role' => 'control',
									'name' => 'bcprs-switch-separator-type',
								) ),
								EL::i( 'plain', 'separator_icon', __( 'Icon', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'separator_icon', 'plain' ) )->data( 'array', $this->get_separator_icons() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-separator-type',
									'value' => 'icon',
									'ref'   => $this->value( 'separator_type', 'plain' ),
								) ),
								EL::i( 'plain', 'separator_ascii', __( 'ASCII Icon', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'separator_ascii', 'plain' ) )->data( 'array', $this->get_separator_ascii() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-separator-type',
									'value' => 'ascii',
									'ref'   => $this->value( 'separator_type', 'plain' ),
								) ),
								EL::i( 'plain', 'separator_char', __( 'Character', 'breadcrumbspress' ), '', Type::TEXT, $this->value( 'separator_char', 'plain' ) )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-separator-type',
									'value' => 'char',
									'ref'   => $this->value( 'separator_type', 'plain' ),
								) ),
							),
						),
					),
				),
				'snippets_jsonld' => array(
					'name'     => __( 'JSON-LD Rich Snippets', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'include_rich_snippets', __( 'Include snippets', 'breadcrumbspress' ), __( 'Rich Snippet version of the breadcrumbs are highly recommended, because search engines can use them to better understand your website structure and hierarchy, and show that throguh search engine results.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'include_rich_snippets' ) ),
							),
						),
					),
				),
			),
			'tweaks'      => array(
				'tweaks_format' => array(
					'name'     => __( 'HTML Wrapper Format', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'markup_list_type', __( 'List Type', 'breadcrumbspress' ), __( 'Choose ordered or unordered list types. It is is just HTML tag used, it has no other effect on the markup.', 'breadcrumbspress' ), Type::SELECT, $this->value( 'markup_list_type' ) )->data( 'array', $this->get_list_type() ),
							),
						),
					),
				),
				'tweaks_misc'   => array(
					'name'     => __( 'Additional Tweaks', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => __( 'Titles Normalization', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'crumbs_case_change', __( 'Normalize titles', 'breadcrumbspress' ), __( 'Automatically pass all crumbs titles through normalization process and change the crumb title case.', 'breadcrumbspress' ), Type::SELECT, $this->value( 'crumbs_case_change' ) )->data( 'array', $this->get_crumb_case() ),
							),
						),
					),
				),
			),
			'integration' => array(
				'integration_method'  => array(
					'name'     => __( 'Integration Control', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => __( 'Method', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'integration', 'method', __( 'Integrate', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'method', 'integration' ) )->data( 'array', $this->get_integration_methods() )->switch( array(
									'role' => 'control',
									'type' => 'section',
									'name' => 'bcprs-switch-integrate-method',
								) ),
							),
						),
						array(
							'label'    => __( 'Action', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'switch'   => array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-integrate-method',
								'value' => 'action',
								'ref'   => $this->value( 'method', 'integration' ),
							),
							'settings' => array(
								EL::i( 'integration', 'action_name', __( 'Action Name', 'breadcrumbspress' ), '', Type::TEXT, $this->value( 'action_name', 'integration' ) ),
								EL::i( 'integration', 'action_priority', __( 'Action Priority', 'breadcrumbspress' ), '', Type::INTEGER, $this->value( 'action_priority', 'integration' ) ),
								EL::i( 'integration', 'action_wrapper_class', __( 'Extra Wrapper CSS class', 'breadcrumbspress' ), '', Type::TEXT, $this->value( 'action_wrapper_class', 'integration' ) ),
							),
						),
						array(
							'label'    => __( 'Detected Theme', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'switch'   => array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-integrate-method',
								'value' => 'auto',
								'ref'   => $this->value( 'method', 'integration' ),
							),
							'settings' => $_integration_auto,
						),
						array(
							'label'    => __( 'Theme', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'switch'   => array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-integrate-method',
								'value' => 'theme',
								'ref'   => $this->value( 'method', 'integration' ),
							),
							'settings' => array(
								EL::i( 'integration', 'theme', __( 'Theme or framework in use', 'breadcrumbspress' ), __( 'Integration has been done based on the default, unmodified themes listed here, tested with the last version of the theme available at the time of latest plugin update. There is no guarantee that this integration will work, and in some cases, it might require additional styling to adjust the breadcrumbs to the theme.', 'breadcrumbspress' ), Type::SELECT, $this->value( 'theme', 'integration' ) )->data( 'array', $this->get_integration_themes() )->switch( array(
									'role' => 'control',
									'name' => 'bcprs-switch-integration-theme',
								) ),
								EL::i( 'integration', 'theme_storefront_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_storefront_action', 'integration' ) )->data( 'array', $this->get_storefront_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'storefront',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
								EL::i( 'integration', 'theme_genesis_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_genesis_action', 'integration' ) )->data( 'array', $this->get_genesis_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'genesis',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
								EL::i( 'integration', 'theme_astra_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_astra_action', 'integration' ) )->data( 'array', $this->get_astra_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'astra',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
								EL::i( 'integration', 'theme_blocksy_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_blocksy_action', 'integration' ) )->data( 'array', $this->get_blocksy_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'blocksy',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
								EL::i( 'integration', 'theme_oceanwp_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_oceanwp_action', 'integration' ) )->data( 'array', $this->get_oceanwp_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'oceanwp',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
								EL::i( 'integration', 'theme_generatepress_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_generatepress_action', 'integration' ) )->data( 'array', $this->get_generatepress_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'generatepress',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
								EL::i( 'integration', 'theme_kadence_action', __( 'Location / Action', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'theme_kadence_action', 'integration' ) )->data( 'array', $this->get_kadence_actions() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-integration-theme',
									'value' => 'kadence',
									'ref'   => $this->value( 'theme', 'integration' ),
								) ),
							),
						),
					),
				),
				'integration_snippet' => array(
					'name'     => __( 'Only Rich Snippet', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'integration', 'snippet', __( 'Integrate', 'breadcrumbspress' ), __( 'This method will be run inside the page HEAD tag before the page content is displayed. This doesn\'t affect theme integration, but it can be useful if you want to include breadcrumbs rich snippet on the page only without actually rendering breadcrumbs control.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'snippet', 'integration' ) ),
							),
						),
					),
				),
			),
			'styling'     => array(
				'styling_basic'   => array(
					'name'     => __( 'Basic Breadcrumbs block styling', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'style', 'base_font_size', __( 'Base Font Size', 'breadcrumbspress' ), '', Type::CSS_SIZE, $this->value( 'base_font_size', 'style' ) ),
								EL::i( 'style', 'base_line_height', __( 'Base Line Height', 'breadcrumbspress' ), '', Type::CSS_SIZE, $this->value( 'base_line_height', 'style' ) ),
							),
						),
					),
				),
				'styling_wrapper' => array(
					'name'     => __( 'Breadcrumbs wrapper styling', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'style', 'base_wrapper_padding', __( 'Padding', 'breadcrumbspress' ), '', Type::CSS_SIZE, $this->value( 'base_wrapper_padding', 'style' ) ),
							),
						),
					),
				),
				'styling_extra'   => array(
					'name'     => __( 'Additional Breadcrumbs block styling', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'style', 'base_crumb_margin', __( 'Margins around Separator', 'breadcrumbspress' ), '', Type::CSS_SIZE, $this->value( 'base_crumb_margin', 'style' ) ),
								EL::i( 'style', 'base_block_align', __( 'Block Alignment', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'base_block_align', 'style' ) )->data( 'array', $this->get_style_align() ),
								EL::i( 'style', 'base_link_decoration', __( 'Link Decoration', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'base_link_decoration', 'style' ) )->data( 'array', $this->get_style_decoration() ),
							),
						),
					),
				),
			),
			'advanced'    => array(
				'advanced_disable'  => array(
					'name'     => __( 'Disable Third Party Breadcrumbs', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'woocommerce_disable_breadcrumbs', __( 'WooCommerce', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'woocommerce_disable_breadcrumbs' ) ),
								EL::i( 'settings', 'bbpress_disable_breadcrumbs', __( 'bbPress', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_disable_breadcrumbs' ) ),
							),
						),
					),
				),
				'advanced_controls' => array(
					'name'     => __( 'Breadcrumbs titles and display override', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'override_title', __( 'Override Titles', 'breadcrumbspress' ), __( 'New settings panel will be added where you can set titles for various breadcrumb types.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'override_title' ) ),
								EL::i( 'settings', 'override_display', __( 'Override Display', 'breadcrumbspress' ), __( 'New settings panel will be added where you can set display variants for various breadcrumb types. If empty, display value will be equal to title.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'override_display' ) ),
							),
						),
					),
				),
			),
			'controls'    => array(
				'controls_home' => array(
					'name'     => __( 'Home Breadcrumb', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'home_crumb', __( 'Include Home', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'home_crumb' ) ),
								EL::i( 'settings', 'home_url', __( 'Home URL', 'breadcrumbspress' ), __( 'If empty, website default URL will be used.', 'breadcrumbspress' ), Type::LINK, $this->value( 'home_url' ) ),
							),
						),
						array(
							'label'    => __( 'Title', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'home_element', __( 'Home', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'home_element' ) )->data( 'array', array(
									'title'  => __( 'Website Title', 'breadcrumbspress' ),
									'custom' => __( 'Custom Title', 'breadcrumbspress' ),
								) )->switch( array(
									'role' => 'control',
									'name' => 'bcprs-switch-home-element',
								) ),
								EL::i( 'settings', 'home_custom', __( 'Custom Title', 'breadcrumbspress' ), __( 'If empty, default value \'Home\' will be used.', 'breadcrumbspress' ), Type::TEXT, $this->value( 'home_custom' ) )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-home-element',
									'value' => 'custom',
									'ref'   => $this->value( 'home_element' ),
								) ),
							),
						),
						array(
							'label'    => __( 'First crumb display', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'settings', 'home_display', __( 'Home', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'home_display' ) )->data( 'array', array(
									'element' => __( 'Title', 'breadcrumbspress' ),
									'icon'    => __( 'Icon', 'breadcrumbspress' ),
									'html'    => __( 'HTML', 'breadcrumbspress' ),
								) )->switch( array(
									'role' => 'control',
									'name' => 'bcprs-switch-home-display',
								) ),
								EL::i( 'settings', 'home_icon', __( 'Icon', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'home_icon' ) )->data( 'array', $this->get_icons() )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-home-display',
									'value' => 'icon',
									'ref'   => $this->value( 'home_display' ),
								) ),
								EL::i( 'settings', 'home_html', __( 'Custom HTML', 'breadcrumbspress' ), __( 'Allowed use of HTML.', 'breadcrumbspress' ), Type::TEXT_HTML, $this->value( 'home_html' ) )->switch( array(
									'role'  => 'value',
									'name'  => 'bcprs-switch-home-display',
									'value' => 'html',
									'ref'   => $this->value( 'home_display' ),
								) ),
							),
						),
					),
				),
				'controls_cpt'  => array(
					'name'     => __( 'Post Types Hierarchy', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'controls_tax'  => array(
					'name'     => __( 'Taxonomies Hierarchy', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
			),
			'visibility'  => array(
				'visibility_home'        => array(
					'name'     => __( 'WordPress Home', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'visibility', 'core_home', __( 'Home or Front Page', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_home', 'visibility' ) ),
								EL::i( 'visibility', 'core_posts', __( 'Posts Page', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_posts', 'visibility' ) ),
							),
						),
					),
				),
				'visibility_core'        => array(
					'name'     => __( 'WordPress Core', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'visibility', 'core_404', __( '404', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_404', 'visibility' ) ),
								EL::i( 'visibility', 'core_search', __( 'Search', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_search', 'visibility' ) ),
								EL::i( 'visibility', 'core_archives', __( 'Archives', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_archives', 'visibility' ) ),
								EL::i( 'visibility', 'core_date_archives', __( 'Date Archives', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_date_archives', 'visibility' ) ),
								EL::i( 'visibility', 'core_author_archives', __( 'Author Archives', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'core_author_archives', 'visibility' ) ),
							),
						),
					),
				),
				'visibility_cpt'         => array(
					'name'     => __( 'Post Types Singular', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'visibility_cpt_archive' => array(
					'name'     => __( 'Post Types Archive', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'visibility_tax'         => array(
					'name'     => __( 'Taxonomies Archive', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
			),
			'display'     => array(
				'display_home'        => array(
					'name'     => __( 'WordPress Home', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'display', 'core_home', __( 'Home Page', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_home', 'display' ) ),
								EL::i( 'display', 'core_front', __( 'Front Page', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_front', 'display' ) ),
								EL::i( 'display', 'core_posts', __( 'Posts Page', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_posts', 'display' ) ),
							),
						),
					),
				),
				'display_core'        => array(
					'name'     => __( 'WordPress Core', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'display', 'core_404', __( '404', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_404', 'display' ) ),
								EL::i( 'display', 'core_search', __( 'Search', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_search', 'display' ) ),
								EL::i( 'display', 'core_archives', __( 'Archives', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_archives', 'display' ) ),
								EL::i( 'display', 'core_date_archives', __( 'Date Archives', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_date_archives', 'display' ) ),
								EL::i( 'display', 'core_author_archives', __( 'Author Archives', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'core_author_archives', 'display' ) ),
							),
						),
					),
				),
				'display_cpt_single'  => array(
					'name'     => __( 'Post Types Singular', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'display_cpt_archive' => array(
					'name'     => __( 'Post Types Archive', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'display_tax'         => array(
					'name'     => __( 'Taxonomies Archives', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
			),
			'title'       => array(
				'title_home'        => array(
					'name'     => __( 'WordPress Home', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'title', 'core_home', __( 'Home/Front Page', 'breadcrumbspress' ), __( 'Default depends on the settings from Controls panel.', 'breadcrumbspress' ), Type::TEXT, $this->value( 'core_home', 'title' ) ),
								EL::i( 'title', 'core_posts', __( 'Posts Page', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "core_posts" ) ) . '</span>', Type::TEXT, $this->value( 'core_posts', 'title' ) ),
							),
						),
					),
				),
				'title_core'        => array(
					'name'     => __( 'WordPress Core', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'title', 'core_404', __( '404', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "core_404" ) ) . '</span>', Type::TEXT, $this->value( 'core_404', 'title' ) ),
								EL::i( 'title', 'core_search', __( 'Search', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "core_search" ) ) . '</span>', Type::TEXT, $this->value( 'core_search', 'title' ) ),
								EL::i( 'title', 'core_archives', __( 'Archives', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "core_archives" ) ) . '</span>', Type::TEXT, $this->value( 'core_archives', 'title' ) ),
								EL::i( 'title', 'core_date_archives', __( 'Date Archives', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "core_date_archives" ) ) . '</span>', Type::TEXT, $this->value( 'core_date_archives', 'title' ) ),
								EL::i( 'title', 'core_author_archives', __( 'Author Archives', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "core_author_archives" ) ) . '</span>', Type::TEXT, $this->value( 'core_author_archives', 'title' ) ),
							),
						),
					),
				),
				'title_cpt_single'  => array(
					'name'     => __( 'Post Types Singular', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'title_cpt_archive' => array(
					'name'     => __( 'Post Types Archive', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
				'title_tax'         => array(
					'name'     => __( 'Taxonomies Archives', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(),
						),
					),
				),
			),
			'path'        => array(
				'path_cpt'  => array(
					'name'     => __( 'Post Types', 'breadcrumbspress' ),
					'sections' => array(),
				),
				'path_tax'  => array(
					'name'     => __( 'Taxonomies', 'breadcrumbspress' ),
					'sections' => array(),
				),
				'path_misc' => array(
					'name'     => __( 'Additional', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => __( 'Date Archives', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'rules', 'date_with_posts', __( 'Posts page', 'breadcrumbspress' ), __( 'Path will be expanded with the Posts page.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'date_with_posts', 'rules' ) ),
							),
						),
						array(
							'label'    => __( 'Author Archives', 'breadcrumbspress' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'rules', 'author_with_posts', __( 'Posts page', 'breadcrumbspress' ), __( 'Path will be expanded with the Posts page.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'author_with_posts', 'rules' ) ),
							),
						),
					),
				),
			),
		);

		foreach ( $post_types as $name => $post_type ) {
			if ( $post_type->has_hierarchy() ) {
				$this->settings['controls']['controls_cpt']['sections'][0]['settings'][] = EL::i( 'rules', 'cpt_hierarchy_' . $name, $post_type->get_label(), __( 'Post Type Name', 'breadcrumbspress' ) . ': <strong>' . $name . '</strong>', Type::SELECT, $this->value( 'cpt_hierarchy_' . $name, 'rules' ) )->data( 'array', array(
					'full'   => __( 'Full hierarchy', 'breadcrumbspress' ),
					'parent' => __( 'Direct parent only', 'breadcrumbspress' ),
					'no'     => __( 'No hierarchy', 'breadcrumbspress' ),
				) );
			}

			$this->settings['title']['title_cpt_single']['sections'][0]['settings'][]     = EL::i( 'title', 'cpt_single_' . $name, $post_type->get_label(), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( 'cpt_single_' . $name ) ) . '</span>', Type::TEXT, $this->value( 'cpt_single_' . $name, 'title' ) );
			$this->settings['display']['display_cpt_single']['sections'][0]['settings'][] = EL::i( 'display', 'cpt_single_' . $name, $post_type->get_label(), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'cpt_single_' . $name, 'display' ) );

			$this->settings['visibility']['visibility_cpt']['sections'][0]['settings'][] = EL::i( 'visibility', 'cpt_single_' . $name, $post_type->get_label(), '', Type::BOOLEAN, $this->value( 'cpt_single_' . $name, 'visibility' ) );

			if ( $post_type->has_archive() !== false && $name != 'post' ) {
				$this->settings['title']['title_cpt_archive']['sections'][0]['settings'][]           = EL::i( 'title', 'cpt_archive_' . $name, $post_type->get_label(), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( 'cpt_archive_' . $name ) ) . '</span>', Type::TEXT, $this->value( 'cpt_archive_' . $name, 'title' ) );
				$this->settings['display']['display_cpt_archive']['sections'][0]['settings'][]       = EL::i( 'display', 'cpt_archive_' . $name, $post_type->get_label(), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'cpt_archive_' . $name, 'display' ) );
				$this->settings['visibility']['visibility_cpt_archive']['sections'][0]['settings'][] = EL::i( 'visibility', 'cpt_archive_' . $name, $post_type->get_label(), '', Type::BOOLEAN, $this->value( 'cpt_archive_' . $name, 'visibility' ) );
			}

			$_settings = array(
				EL::i( 'path', 'cpt_' . $name, __( 'Path', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'cpt_' . $name, 'path' ) )->data( 'array', $this->get_list_of_paths_for_post_type( $name, $post_type ) ),
			);

			if ( $post_type->has_terms() ) {
				$_settings[] = EL::i( 'rules', 'cpt_taxonomy_' . $name, __( 'Taxonomy', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'cpt_taxonomy_' . $name, 'rules' ) )->data( 'array', $post_type->get_taxonomies_list() );
			}

			if ( breadcrumbspress()->allowed_for_posts_page( $name ) && $this->posts_page > 0 ) {
				$_settings[] = EL::i( 'rules', 'cpt_with_posts_' . $name, __( 'Posts page', 'breadcrumbspress' ), __( 'Path will be expanded with the Posts page.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'cpt_with_posts_' . $name, 'rules' ) );
			}

			$this->settings['path']['path_cpt']['sections'][] = array(
				'label'    => $post_type->get_label() . ' (' . $post_type->get_name() . ')',
				'name'     => '',
				'class'    => '',
				'settings' => $_settings,
			);
		}

		foreach ( $taxonomies as $name => $taxonomy ) {
			if ( $taxonomy->has_hierarchy() ) {
				$this->settings['controls']['controls_tax']['sections'][0]['settings'][] = EL::i( 'rules', 'tax_hierarchy_' . $name, $taxonomy->get_label(), __( 'Taxonomy Name', 'breadcrumbspress' ) . ': <strong>' . $name . '</strong>', Type::SELECT, $this->value( 'tax_hierarchy_' . $name, 'rules' ) )->data( 'array', array(
					'full'   => __( 'Full hierarchy', 'breadcrumbspress' ),
					'parent' => __( 'Direct parent only', 'breadcrumbspress' ),
					'no'     => __( 'No hierarchy', 'breadcrumbspress' ),
				) );
			}

			$this->settings['title']['title_tax']['sections'][0]['settings'][]     = EL::i( 'title', 'tax_' . $name, $taxonomy->get_label(), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( 'tax_' . $name ) ) . '</span>', Type::TEXT, $this->value( 'tax_' . $name, 'title' ) );
			$this->settings['display']['display_tax']['sections'][0]['settings'][] = EL::i( 'display', 'tax_' . $name, $taxonomy->get_label(), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'tax_' . $name, 'display' ) );

			$this->settings['visibility']['visibility_tax']['sections'][0]['settings'][] = EL::i( 'visibility', 'tax_' . $name, $taxonomy->get_label(), '', Type::BOOLEAN, $this->value( 'tax_' . $name, 'visibility' ) );

			$_settings = array(
				EL::i( 'path', 'tax_' . $name, __( 'Path', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'tax_' . $name, 'path' ) )->data( 'array', $this->get_list_of_paths_for_taxonomy( $name, $taxonomy ) ),
			);

			if ( breadcrumbspress()->allowed_for_posts_page( $taxonomy->get_post_types() ) && $this->posts_page > 0 ) {
				$_settings[] = EL::i( 'rules', 'tax_with_posts_' . $name, __( 'Posts page', 'breadcrumbspress' ), __( 'Path will be expanded with the Posts page.', 'breadcrumbspress' ), Type::BOOLEAN, $this->value( 'tax_with_posts_' . $name, 'rules' ) );
			} else if ( ! empty( $taxonomy->get_post_types() ) ) {
				$_settings[] = EL::i( 'rules', 'tax_with_cpt_archive_' . $name, __( 'Post Type Archive', 'breadcrumbspress' ), __( 'Include post type archive as the first crumb.', 'breadcrumbspress' ), Type::SELECT, $this->value( 'tax_with_cpt_archive_' . $name, 'rules' ) )->data( 'array', $this->get_post_types_from_list( $taxonomy->get_post_types() ) );
			}

			$this->settings['path']['path_tax']['sections'][] = array(
				'label'    => $taxonomy->get_label() . ' (' . $taxonomy->get_name() . ')',
				'name'     => '',
				'class'    => '',
				'settings' => $_settings,
			);
		}

		if ( breadcrumbspress()->has_bbpress() ) {
			$this->settings['controls']['controls_bbpress'] = array(
				'name'     => __( 'bbPress Breadcrumbs', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => __( 'Forums Root', 'breadcrumbspress' ),
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'settings', 'bbpress_root_title', __( 'Title', 'breadcrumbspress' ), __( 'If empty, default value \'Forums\' will be used.', 'breadcrumbspress' ), Type::TEXT, $this->value( 'bbpress_root_title' ) ),
						),
					),
					array(
						'label'    => __( 'Root crumb display', 'breadcrumbspress' ),
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'settings', 'bbpress_root_display', __( 'Root', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'bbpress_root_display' ) )->data( 'array', array(
								'element' => __( 'Title', 'breadcrumbspress' ),
								'icon'    => __( 'Icon', 'breadcrumbspress' ),
								'html'    => __( 'HTML', 'breadcrumbspress' ),
							) )->switch( array(
								'role' => 'control',
								'name' => 'bcprs-switch-bbpress-root-display',
							) ),
							EL::i( 'settings', 'bbpress_root_icon', __( 'Icon', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'bbpress_root_icon' ) )->data( 'array', $this->get_icons() )->switch( array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-bbpress-root-display',
								'value' => 'icon',
								'ref'   => $this->value( 'bbpress_root_display' ),
							) ),
							EL::i( 'settings', 'bbpress_root_html', __( 'Custom HTML', 'breadcrumbspress' ), __( 'Allowed use of HTML.', 'breadcrumbspress' ), Type::TEXT_HTML, $this->value( 'bbpress_root_html' ) )->switch( array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-bbpress-root-display',
								'value' => 'html',
								'ref'   => $this->value( 'bbpress_root_display' ),
							) ),
						),
					),
				),
			);

			$this->settings['visibility']['visibility_bbpress'] = array(
				'name'     => __( 'bbPress', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'visibility', 'bbpress_profile', __( 'Profiles', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_profile', 'visibility' ) ),
							EL::i( 'visibility', 'bbpress_search', __( 'Search', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_search', 'visibility' ) ),
							EL::i( 'visibility', 'bbpress_view', __( 'Topic Views', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_view', 'visibility' ) ),
							EL::i( 'visibility', 'bbpress_topic_tag', __( 'Topic Tags', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_topic_tag', 'visibility' ) ),
						),
					),
				),
			);

			$this->settings['title']['title_bbpress'] = array(
				'name'     => __( 'bbPress', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'title', 'bbpress_search', __( 'Search', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "bbpress_search" ) ) . '</span>', Type::TEXT, $this->value( 'bbpress_search', 'title' ) ),
							EL::i( 'title', 'bbpress_search_results', __( 'Search Results', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "bbpress_search_results" ) ) . '</span>', Type::TEXT, $this->value( 'bbpress_search_results', 'title' ) ),
							EL::i( 'title', 'bbpress_view', __( 'Topic Views', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "bbpress_view" ) ) . '</span>', Type::TEXT, $this->value( 'bbpress_view', 'title' ) ),
							EL::i( 'title', 'bbpress_tag', __( 'Topic Tags', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "bbpress_tag" ) ) . '</span>', Type::TEXT, $this->value( 'bbpress_tag', 'title' ) ),
						),
					),
				),
			);

			$this->settings['display']['display_bbpress'] = array(
				'name'     => __( 'bbPress', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'display', 'bbpress_search', __( 'Search', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'bbpress_search', 'display' ) ),
							EL::i( 'display', 'bbpress_search_results', __( 'Search Results', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'bbpress_search_results', 'display' ) ),
							EL::i( 'display', 'bbpress_view', __( 'Topic Views', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'bbpress_view', 'display' ) ),
							EL::i( 'display', 'bbpress_tag', __( 'Topic Tags', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'bbpress_tag', 'display' ) ),
						),
					),
				),
			);

			if ( breadcrumbspress()->has_gd_topic_prefix() ) {
				$this->settings['visibility']['visibility_bbpress_members'] = array(
					'name'     => __( 'GD Topic Prefix', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'visibility', 'bbpress_topic_prefix', __( 'Topic Prefix', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_topic_prefix', 'visibility' ) ),
							),
						),
					),
				);
			}

			if ( breadcrumbspress()->has_gd_members_directory() ) {
				$this->settings['controls']['controls_bbpress']['sections'][] = array(
					'label'    => __( 'User Profiles', 'breadcrumbspress' ),
					'name'     => '',
					'class'    => '',
					'settings' => array(
						EL::i( 'settings', 'bbpress_directory_crumb', __( 'With Directory Crumb', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_directory_crumb' ) ),
					),
				);

				$this->settings['visibility']['visibility_bbpress_members'] = array(
					'name'     => __( 'GD Members Directory', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'visibility', 'bbpress_directory', __( 'Directory', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'bbpress_directory', 'visibility' ) ),
							),
						),
					),
				);

				$this->settings['title']['title_bbpress_members'] = array(
					'name'     => __( 'GD Members Directory', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'title', 'bbpress_directory', __( 'Directory Page', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "bbpress_directory" ) ) . '</span>', Type::TEXT, $this->value( 'bbpress_directory', 'title' ) ),
								EL::i( 'title', 'bbpress_directory_profile', __( 'Directory Crumb for Profiles', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">' . esc_html( breadcrumbspress_settings()->get_item_default_title( "bbpress_directory_profile" ) ) . '</span>', Type::TEXT, $this->value( 'bbpress_directory_profile', 'title' ) ),
							),
						),
					),
				);

				$this->settings['display']['display_bbpress_members'] = array(
					'name'     => __( 'GD Members Directory', 'breadcrumbspress' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								EL::i( 'display', 'bbpress_directory', __( 'Directory Page', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'bbpress_directory', 'display' ) ),
								EL::i( 'display', 'bbpress_directory_profile', __( 'Directory Crumb for Profiles', 'breadcrumbspress' ), __( 'Default', 'breadcrumbspress' ) . ': <span class="dev4press-info-default-value">%title%</span>', Type::TEXT_HTML, $this->value( 'bbpress_directory_profile', 'display' ) ),
							),
						),
					),
				);
			}
		}

		if ( breadcrumbspress()->has_buddypress() ) {
			$this->settings['visibility']['visibility_buddypress'] = array(
				'name'     => __( 'BuddyPress', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'visibility', 'buddypress_members', __( 'Members', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'buddypress_members', 'visibility' ) ),
							EL::i( 'visibility', 'buddypress_profile', __( 'Profiles', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'buddypress_profile', 'visibility' ) ),
							EL::i( 'visibility', 'buddypress_activity', __( 'Activity', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'buddypress_activity', 'visibility' ) ),
							EL::i( 'visibility', 'buddypress_groups', __( 'Groups', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'buddypress_groups', 'visibility' ) ),
							EL::i( 'visibility', 'buddypress_group', __( 'Group', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'buddypress_group', 'visibility' ) ),
							EL::i( 'visibility', 'buddypress_group_create', __( 'Create Group', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'buddypress_group_create', 'visibility' ) ),
						),
					),
				),
			);
		}

		if ( breadcrumbspress()->has_woocommerce() ) {
			$this->settings['controls']['controls_woocommerce'] = array(
				'name'     => __( 'WooCommerce Breadcrumbs', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => __( 'Store', 'breadcrumbspress' ),
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'settings', 'woocommerce_show_root_crumb', __( 'Show Store breadcrumb', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'woocommerce_show_root_crumb' ) ),
							EL::i( 'settings', 'woocommerce_root_title', __( 'Title', 'breadcrumbspress' ), __( 'If empty, default value \'Store\' will be used.', 'breadcrumbspress' ), Type::TEXT, $this->value( 'woocommerce_root_title' ) ),
						),
					),
					array(
						'label'    => __( 'Root crumb display', 'breadcrumbspress' ),
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'settings', 'woocommerce_root_display', __( 'Root', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'woocommerce_root_display' ) )->data( 'array', array(
								'element' => __( 'Title', 'breadcrumbspress' ),
								'icon'    => __( 'Icon', 'breadcrumbspress' ),
								'html'    => __( 'HTML', 'breadcrumbspress' ),
							) )->switch( array(
								'role' => 'control',
								'name' => 'bcprs-switch-woocommerce-root-display',
							) ),
							EL::i( 'settings', 'woocommerce_root_icon', __( 'Icon', 'breadcrumbspress' ), '', Type::SELECT, $this->value( 'woocommerce_root_icon' ) )->data( 'array', $this->get_icons() )->switch( array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-woocommerce-root-display',
								'value' => 'icon',
								'ref'   => $this->value( 'woocommerce_root_display' ),
							) ),
							EL::i( 'settings', 'woocommerce_root_html', __( 'Custom HTML', 'breadcrumbspress' ), __( 'Allowed use of HTML.', 'breadcrumbspress' ), Type::TEXT_HTML, $this->value( 'woocommerce_root_html' ) )->switch( array(
								'role'  => 'value',
								'name'  => 'bcprs-switch-woocommerce-root-display',
								'value' => 'html',
								'ref'   => $this->value( 'woocommerce_root_display' ),
							) ),
						),
					),
				),
			);

			$this->settings['visibility']['visibility_woocommerce'] = array(
				'name'     => __( 'WooCommerce', 'breadcrumbspress' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							EL::i( 'visibility', 'woocommerce_account', __( 'Accounts', 'breadcrumbspress' ), '', Type::BOOLEAN, $this->value( 'woocommerce_account', 'visibility' ) ),
						),
					),
				),
			);
		}

		if ( empty( $this->settings['controls']['controls_cpt']['sections'][0]['settings'] ) ) {
			unset( $this->settings['controls']['controls_cpt'] );
		}

		if ( empty( $this->settings['controls']['controls_tax']['sections'][0]['settings'] ) ) {
			unset( $this->settings['controls']['controls_tax'] );
		}

		if ( empty( $this->settings['visibility']['visibility_cpt_archive']['sections'][0]['settings'] ) ) {
			unset( $this->settings['visibility']['visibility_cpt_archive'] );
		}

		if ( empty( $this->settings['visibility']['visibility_tax_archive']['sections'][0]['settings'] ) ) {
			unset( $this->settings['visibility']['visibility_tax_archive'] );
		}

		if ( empty( $this->settings['title']['title_cpt_archive']['sections'][0]['settings'] ) ) {
			unset( $this->settings['title']['title_cpt_archive'] );
		}

		if ( empty( $this->settings['title']['title_tax']['sections'][0]['settings'] ) ) {
			unset( $this->settings['title']['title_tax'] );
		}

		if ( empty( $this->settings['display']['display_cpt_archive']['sections'][0]['settings'] ) ) {
			unset( $this->settings['display']['display_cpt_archive'] );
		}

		if ( empty( $this->settings['display']['display_tax']['sections'][0]['settings'] ) ) {
			unset( $this->settings['display']['display_tax'] );
		}
	}

	private function get_post_types_from_list( $post_types ) : array {
		$list = array(
			'' => __( 'Do not include', 'breadcrumbspress' ),
		);

		foreach ( $post_types as $cpt ) {
			$object       = get_post_type_object( $cpt );
			$list[ $cpt ] = $object->label . ' (' . $cpt . ')';
		}

		return $list;
	}

	private function get_integration_themes() : array {
		return breadcrumbspress()->supported_themes();
	}

	private function get_storefront_actions() : array {
		return array(
			'storefront_header'         => __( 'Header Bottom', 'breadcrumbspress' ),
			'storefront_before_content' => __( 'Content Before', 'breadcrumbspress' ),
			'storefront_content_top'    => __( 'Content Top', 'breadcrumbspress' ),
			'storefront_before_footer'  => __( 'Footer Before', 'breadcrumbspress' ),
			'storefront_footer'         => __( 'Footer Top', 'breadcrumbspress' ),
		);
	}

	private function get_genesis_actions() : array {
		return array(
			'genesis_before_content_sidebar_wrap' => __( 'Before Content Sidebar Wrap', 'breadcrumbspress' ),
			'genesis_before_content'              => __( 'Before Content', 'breadcrumbspress' ),
			'genesis_before_loop'                 => __( 'Before Loop', 'breadcrumbspress' ),
			'genesis_after_content_sidebar_wrap'  => __( 'After Content Sidebar Wrap', 'breadcrumbspress' ),
			'genesis_footer'                      => __( 'Before Footer', 'breadcrumbspress' ),
		);
	}

	private function get_astra_actions() : array {
		return array(
			'astra_content_before' => __( 'Before Content', 'breadcrumbspress' ),
			'astra_content_top'    => __( 'Content Top', 'breadcrumbspress' ),
			'astra_content_bottom' => __( 'Content Bottom', 'breadcrumbspress' ),
			'astra_content_after'  => __( 'After Content', 'breadcrumbspress' ),
		);
	}

	private function get_oceanwp_actions() : array {
		return array(
			'ocean_before_main'              => __( 'Before Main', 'breadcrumbspress' ),
			'ocean_before_page_header_inner' => __( 'Page Header Top', 'breadcrumbspress' ),
			'ocean_after_page_header_inner'  => __( 'Page Header Bottom', 'breadcrumbspress' ),
			'ocean_before_content_wrap'      => __( 'Before Content', 'breadcrumbspress' ),
			'ocean_before_primary'           => __( 'Before Primary', 'breadcrumbspress' ),
			'ocean_after_primary'            => __( 'After Primary', 'breadcrumbspress' ),
			'ocean_after_content_wrap'       => __( 'After Content', 'breadcrumbspress' ),
		);
	}

	private function get_generatepress_actions() : array {
		return array(
			'generate_after_header'          => __( 'After the Header', 'breadcrumbspress' ),
			'generate_inside_site_container' => __( 'Top of the Site Container', 'breadcrumbspress' ),
			'generate_before_footer'         => __( 'Before the Footer Container', 'breadcrumbspress' ),
			'generate_footer'                => __( 'After the Footer Widgets', 'breadcrumbspress' ),
		);
	}

	private function get_blocksy_actions() : array {
		return array(
			'blocksy:header:after'   => __( 'After the Header', 'breadcrumbspress' ),
			'blocksy:content:before' => __( 'Before the Content', 'breadcrumbspress' ),
			'blocksy:content:top'    => __( 'Top of the Content', 'breadcrumbspress' ),
			'blocksy:content:bottom' => __( 'Bottom of the Content', 'breadcrumbspress' ),
			'blocksy:content:after'  => __( 'After the Content', 'breadcrumbspress' ),
			'blocksy:footer:before'  => __( 'Before the Footer', 'breadcrumbspress' ),
		);
	}

	private function get_kadence_actions() : array {
		return array(
			'kadence_after_header'        => __( 'After the Header', 'breadcrumbspress' ),
			'kadence_before_content'      => __( 'Top of the Inner Wrap', 'breadcrumbspress' ),
			'kadence_before_main_content' => __( 'Before the Main Content', 'breadcrumbspress' ),
			'kadence_after_content'       => __( 'Bottom of the Inner Wrap', 'breadcrumbspress' ),
			'kadence_before_footer'       => __( 'Before the Header', 'breadcrumbspress' ),
		);
	}

	private function get_integration_methods() : array {
		return array(
			'manual' => __( 'Manual integration only', 'breadcrumbspress' ),
			'action' => __( 'Hook to specified action', 'breadcrumbspress' ),
			'auto'   => __( 'Automatic theme integration', 'breadcrumbspress' ),
			'theme'  => __( 'Integration with supported themes', 'breadcrumbspress' ),
		);
	}

	private function get_separator_types() : array {
		return array(
			'icon'  => __( 'Icon', 'breadcrumbspress' ),
			'ascii' => __( 'ASCII Icon', 'breadcrumbspress' ),
			'char'  => __( 'Character', 'breadcrumbspress' ),
			'none'  => __( 'None', 'breadcrumbspress' ),
			'empty' => __( 'Empty Tag', 'breadcrumbspress' ),
		);
	}

	private function get_separator_icons() : array {
		return array(
			'crumb'          => __( 'Crumb', 'breadcrumbspress' ),
			'crumb-double'   => __( 'Double Crumb', 'breadcrumbspress' ),
			'angle'          => __( 'Angle', 'breadcrumbspress' ),
			'angle-double'   => __( 'Double Angle', 'breadcrumbspress' ),
			'chevron'        => __( 'Chevron', 'breadcrumbspress' ),
			'chevron-double' => __( 'Double Chevron', 'breadcrumbspress' ),
		);
	}

	private function get_separator_ascii() : array {
		return array(
			'angle'        => __( 'Angle', 'breadcrumbspress' ),
			'angle-double' => __( 'Double Angle', 'breadcrumbspress' ),
			'bullet'       => __( 'Bullet', 'breadcrumbspress' ),
		);
	}

	private function get_style_align() : array {
		return array(
			'initial'       => __( 'Initial', 'breadcrumbspress' ),
			'inherit'       => __( 'Inherit', 'breadcrumbspress' ),
			'flex-start'    => __( 'Left', 'breadcrumbspress' ),
			'center'        => __( 'Center', 'breadcrumbspress' ),
			'flex-end'      => __( 'Right', 'breadcrumbspress' ),
			'space-evenly'  => __( 'Space Evenly', 'breadcrumbspress' ),
			'space-between' => __( 'Space Between', 'breadcrumbspress' ),
			'space-around'  => __( 'Space Around', 'breadcrumbspress' ),
		);
	}

	private function get_style_decoration() : array {
		return array(
			'initial'   => __( 'Initial', 'breadcrumbspress' ),
			'inherit'   => __( 'Inherit', 'breadcrumbspress' ),
			'none'      => __( 'None', 'breadcrumbspress' ),
			'underline' => __( 'Underline', 'breadcrumbspress' ),
			'overline'  => __( 'Overline', 'breadcrumbspress' ),
		);
	}

	private function get_crumb_case() : array {
		return array(
			'asis'  => __( 'Do nothing', 'breadcrumbspress' ),
			'first' => __( 'First letter upper case', 'breadcrumbspress' ),
			'words' => __( 'All words first letter upper case', 'breadcrumbspress' ),
			'lower' => __( 'Everything to lower case', 'breadcrumbspress' ),
			'upper' => __( 'Everything to upper case', 'breadcrumbspress' ),
		);
	}

	private function get_list_type() : array {
		return array(
			'ol' => 'OL',
			'ul' => 'UL',
		);
	}

	private function get_icons() : array {
		return array(
			'home'            => __( 'Home', 'breadcrumbspress' ),
			'home-alt'        => __( 'Home Alt', 'breadcrumbspress' ),
			'home-light'      => __( 'Home Light', 'breadcrumbspress' ),
			'home-alt-light'  => __( 'Home Light Alt', 'breadcrumbspress' ),
			'store'           => __( 'Store', 'breadcrumbspress' ),
			'store-alt'       => __( 'Store Alt', 'breadcrumbspress' ),
			'store-light'     => __( 'Store Light', 'breadcrumbspress' ),
			'store-alt-light' => __( 'Store Light Alt', 'breadcrumbspress' ),
			'book'            => __( 'Book', 'breadcrumbspress' ),
			'book-light'      => __( 'Book Light', 'breadcrumbspress' ),
			'forums'          => __( 'Forums', 'breadcrumbspress' ),
		);
	}

	private function get_list_of_paths_for_post_type( string $cpt, PostType $obj ) : array {
		$paths = array(
			'basic' => __( 'Just the Post', 'breadcrumbspress' ),
		);

		if ( $cpt == 'post' ) {
			$paths = array(
				'basic'    => __( 'Just the Post', 'breadcrumbspress' ),
				'author'   => __( 'Author', 'breadcrumbspress' ),
				'taxonomy' => __( 'Taxonomy Term', 'breadcrumbspress' ),
				'date'     => __( 'Full Date', 'breadcrumbspress' ),
				'month'    => __( 'Month and Year', 'breadcrumbspress' ),
				'year'     => __( 'Year', 'breadcrumbspress' ),
			);

			if ( breadcrumbspress()->allowed_for_posts_page( $cpt ) && $this->posts_page > 0 ) {
				$paths['posts'] = __( 'Posts Page', 'breadcrumbspress' );
			}
		} else {
			if ( $obj->has_archive() ) {
				$paths['post_type'] = __( 'Post Type Archive', 'breadcrumbspress' );
			}

			if ( $obj->has_terms() ) {
				$paths['taxonomy'] = __( 'Taxonomy Term', 'breadcrumbspress' );
			}

			if ( $obj->has_terms() ) {
				$paths['post_type_taxonomy'] = __( 'Post Type Archive with Taxonomy Term', 'breadcrumbspress' );
			}

			if ( breadcrumbspress()->allowed_for_post_types_with_parent( $cpt ) ) {
				$paths['parent_post'] = __( 'Parent Post', 'breadcrumbspress' );
			}
		}

		return apply_filters( 'breadcrumbspress_list_rules_post_type_path_' . $cpt, $paths, $obj );
	}

	private function get_list_of_paths_for_taxonomy( string $tax, Taxonomy $obj ) : array {
		$paths = array();

		if ( $obj->has_hierarchy() ) {
			$paths['basic'] = __( 'Just the Term', 'breadcrumbspress' );
		}

		$paths['taxonomy'] = __( 'Taxonomy archive', 'breadcrumbspress' );

		return apply_filters( 'breadcrumbspress_list_rules_taxonomy_path_' . $tax, $paths, $obj );
	}
}
