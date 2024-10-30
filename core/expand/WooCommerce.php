<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Expand;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Helper;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;
use Dev4Press\Plugin\BreadcrumbsPress\Extend\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerce extends Plugin {
	public static function instance() : WooCommerce {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new WooCommerce();
			$instance->run();
			$instance->override();
		}

		return $instance;
	}

	public function ready() {
		if ( breadcrumbspress_settings()->get( 'woocommerce_disable_breadcrumbs' ) ) {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
			remove_action( 'storefront_before_content', 'woocommerce_breadcrumb' );
		}
	}

	public function root() : Crumb {
		$title   = breadcrumbspress_settings()->get_item_title( 'woocommerce_root' );
		$display = breadcrumbspress_settings()->get_item_display( 'woocommerce_root' );

		if ( empty( $display ) ) {
			switch ( breadcrumbspress_settings()->get( 'woocommerce_root_display' ) ) {
				case 'icon':
					$display = Helper::instance()->icon( breadcrumbspress_settings()->get( 'woocommerce_root_icon' ) );
					break;
				case 'html':
					$display = breadcrumbspress_settings()->get( 'woocommerce_root_html' );
					break;
			}
		}

		if ( empty( $title ) ) {
			$title = $this->b()->get_title_store();
		}

		if ( empty( $display ) ) {
			$display = '%title%';
		}

		return $this->b()->crumb( array(
			'type'    => 'woo_root',
			'title'   => $title,
			'display' => $display,
			'url'     => $this->b()->get_url_store(),
			'current' => false,
		) );
	}

	protected function override() {
		add_action( 'breadcrumbspress_builder_for_post_type_archive_product', array( $this, 'post_type_archive' ), 10 );
		add_action( 'breadcrumbspress_builder_for_post_type_single_product', array( $this, 'post_type_single' ), 10 );
		add_action( 'breadcrumbspress_builder_for_taxonomy_term_product_cat', array( $this, 'taxonomy_archive' ), 10 );
		add_action( 'breadcrumbspress_builder_for_taxonomy_term_product_tag', array( $this, 'taxonomy_archive' ), 10 );
	}

	/**
	 * @param Crumb[] $breadcrumbs
	 *
	 * @return Crumb[]
	 */
	public function post_type_archive( $breadcrumbs ) : array {
		$breadcrumbs = array_slice( $breadcrumbs, 0, 1 ) +
		               array( 'woo_root' => $this->root() );

		return $breadcrumbs;
	}

	/**
	 * @param Crumb[] $breadcrumbs
	 *
	 * @return Crumb[]
	 */
	public function post_type_single( $breadcrumbs ) : array {
		$breadcrumbs = array_slice( $breadcrumbs, 0, 1 ) +
		               array( 'woo_root' => $this->root() ) +
		               array_slice( $breadcrumbs, 2 );

		return $breadcrumbs;
	}

	/**
	 * @param Crumb[] $breadcrumbs
	 *
	 * @return Crumb[]
	 */
	public function taxonomy_archive( $breadcrumbs ) : array {
		$breadcrumbs = array_slice( $breadcrumbs, 0, 1 ) +
		               array( 'woo_root' => $this->root() ) +
		               array_slice( $breadcrumbs, 1 );

		return $breadcrumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_root_crumbs() : array {
		return array(
			'home'     => $this->b()->home(),
			'woo_root' => $this->root(),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function build( $breadcrumbs ) : array {
		if ( $this->q()->is_woocommerce_cart() ) {
			$breadcrumbs = $this->get_cart_page();
		} else if ( $this->q()->is_woocommerce_checkout() ) {
			$breadcrumbs = $this->get_checkout_page();
		} else if ( $this->q()->is_woocommerce_account_page() ) {
			$breadcrumbs = $this->get_account_page();
		}

		return $breadcrumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_cart_page() : array {
		$crumbs             = $this->get_root_crumbs();
		$crumbs['woo_cart'] = $this->b()->crumb( array(
			'type'    => 'woo_cart',
			'title'   => $this->b()->title( 'woocommerce_cart', $this->q()->get_title() ),
			'display' => $this->b()->display( 'woocommerce_cart' ),
			'url'     => wc_get_cart_url(),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_checkout_page() : array {
		$crumbs                 = $this->get_root_crumbs();
		$crumbs['woo_checkout'] = $this->b()->crumb( array(
			'type'    => 'woo_checkout',
			'title'   => $this->b()->title( 'woocommerce_checkout', $this->q()->get_title() ),
			'display' => $this->b()->display( 'woocommerce_checkout' ),
			'url'     => wc_get_checkout_url(),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_account_page( $endpoint = null ) : array {
		$endpoint = is_null( $endpoint ) ? $this->q()->get_woo_endpoint() : $endpoint;

		$crumbs = $this->get_root_crumbs();

		$crumbs['woo_account'] = $this->b()->crumb( array(
			'type'    => 'woo_account',
			'title'   => $this->q()->get_title(),
			'url'     => wc_get_account_endpoint_url( 'dashboard' ),
			'current' => empty( $endpoint ),
		) );

		if ( ! empty( $endpoint ) ) {
			$crumbs['woo_account_page'] = $this->b()->crumb( array(
				'type'    => 'woo_account_page',
				'title'   => wc()->query->get_endpoint_title( $endpoint ),
				'url'     => wc_get_account_endpoint_url( $endpoint ),
				'current' => true,
			) );
		}

		return $crumbs;
	}

	/**
	 * @inheritDoc
	 */
	public function complete( $breadcrumbs ) : array {
		return $breadcrumbs;
	}
}