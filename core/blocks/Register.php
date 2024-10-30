<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Blocks;

use Dev4Press\v49\Core\Blocks\Register as BaseRegister;
use Dev4Press\v49\WordPress;
use WP_Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Register extends BaseRegister {
	public static function instance() : Register {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Register();
		}

		return $instance;
	}

	public function blocks() {
		$this->_register_script();
		$this->_register_style();

		$this->_register_single_block();
	}

	protected function _default_block_classes( $attributes, $block ) : array {
		$classes = array( 'breadcrumbspress-block-wrapper', 'breadcrumbspress-block-' . $block );

		if ( ! empty( $attributes['class'] ) ) {
			$classes[] = esc_attr( $attributes['class'] );
		}

		return $classes;
	}

	protected function _register_script() {
		$asset_file = include( BREADCRUMBSPRESS_PATH . 'build/index.asset.php' );

		wp_register_script( 'breadcrumbspress-editor', BREADCRUMBSPRESS_URL . 'build/index.js', $asset_file['dependencies'], $asset_file['version'] );
		wp_set_script_translations( 'breadcrumbspress-editor', 'breadcrumbspress', BREADCRUMBSPRESS_PATH . 'languages' );
	}

	protected function _register_style() {
		$asset_file = include( BREADCRUMBSPRESS_PATH . 'build/index.asset.php' );

		wp_register_style( 'breadcrumbspress-editor', BREADCRUMBSPRESS_URL . 'css/blocks.css', array( 'breadcrumbspress' ), $asset_file['version'] );
	}

	private function _register_single_block() {
		register_block_type( BREADCRUMBSPRESS_BLOCKS_PATH . 'single', array(
			'render_callback' => array( $this, 'callback_single' ),
		) );
	}

	public function callback_single( array $attributes, string $content, WP_Block $block ) : string {
		$classes = $this->_default_block_classes( $attributes, 'single' );

		if ( $this->is_editor() ) {
			$post_id = $block->context['postId'] ?? 0;
		} else {
			$post_id = 0;
		}

		$rendered = Render::instance()->single( $post_id, $attributes );

		if ( empty( $rendered ) && $this->is_editor() ) {
			$rendered = __( 'Breadcrumbs can\'t be rendered in this context.', 'breadcrumbspress' );
		}

		return '<div class="' . join( ' ', $classes ) . '">' . $rendered . '</div>';
	}
}
