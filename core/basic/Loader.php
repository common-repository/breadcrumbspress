<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\Plugin\BreadcrumbsPress\Integration\Action;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\Astra;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\Blocksy;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\GeneratePress;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\Genesis;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\Kadence;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\OceanWP;
use Dev4Press\Plugin\BreadcrumbsPress\Integration\Storefront;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Loader {
	private $method = '';
	private $theme = '';
	private $action = '';

	public function __construct() {
	}

	public static function instance() : Loader {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Loader();
			$instance->run();
		}

		return $instance;
	}

	public function get( $name, $default = null ) {
		return breadcrumbspress_settings()->get( $name, 'integration', $default );
	}

	private function run() {
		if ( ! is_admin() ) {
			$this->method = breadcrumbspress_settings()->get( 'method', 'integration' );

			if ( $this->method == 'action' ) {
				Action::instance();
			} else if ( $this->method == 'theme' ) {
				$this->theme  = breadcrumbspress_settings()->get( 'theme', 'integration', '' );
				$this->action = breadcrumbspress_settings()->get( 'theme_' . $this->theme . '_action', 'integration', '' );

				$this->load();
			} else if ( $this->method == 'auto' ) {
				$this->theme  = get_template();
				$this->action = breadcrumbspress_settings()->get( 'theme_auto_detect_action', 'integration', '' );

				$this->load();
			}
		}
	}

	private function load() {
		if ( $this->theme == 'storefront' ) {
			Storefront::instance();
		} else if ( $this->theme == 'genesis' ) {
			Genesis::instance();
		} else if ( $this->theme == 'astra' ) {
			Astra::instance();
		} else if ( $this->theme == 'oceanwp' ) {
			OceanWP::instance();
		} else if ( $this->theme == 'generatepress' ) {
			GeneratePress::instance();
		} else if ( $this->theme == 'kadence' ) {
			Kadence::instance();
		} else if ( $this->theme == 'blocksy' ) {
			Blocksy::instance();
		}
	}

	public function action() : string {
		return $this->action;
	}

	public function theme() : string {
		return $this->theme;
	}

	public function method() : string {
		return $this->method;
	}

	public function render( string $method, string $wrapper_class, array $args = array() ) : string {
		$classes = array(
			'breadcrumbs-auto-wrapper',
			'breadcrumbs-wrapper-' . $method,
			$wrapper_class,
		);

		$args['echo'] = false;

		$classes = array_unique( $classes );
		$classes = array_filter( $classes );

		$render = '<div class="' . join( ' ', $classes ) . '">';
		$render .= breadcrumbspress_current( $args );
		$render .= '</div>';

		return $render;
	}
}