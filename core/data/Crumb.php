<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Data;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;
use Dev4Press\v49\Core\Quick\Arr;
use Dev4Press\v49\Core\Quick\Str;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Crumb {
	public $type = '';
	public $display = '';
	public $title = '';
	public $url = '';
	public $class = '';
	public $rel = '';
	public $target = '';
	public $first = false;
	public $current = false;
	public $id = 0;

	private $_display = '';

	public function __construct( $crumb = array() ) {
		$defaults = array(
			'type'    => '',
			'display' => '',
			'title'   => '',
			'url'     => '',
			'class'   => '',
			'rel'     => '',
			'target'  => '',
			'current' => false,
			'id'      => 0,
		);

		$crumb = shortcode_atts( $defaults, $crumb );

		foreach ( $crumb as $key => $value ) {
			$this->$key = $value;
		}

		if ( empty( $this->display ) ) {
			$this->display = '%title%';
		}

		if ( $this->current && empty( $this->url ) ) {
			$this->url = Query::instance()->get_url();
		}
	}

	public function set_current( $current = true ) {
		$this->current = $current;
	}

	public function is_current() : bool {
		return $this->current;
	}

	public function get_title() : string {
		return $this->title;
	}

	public function get_title_formatted() : string {
		return breadcrumbspress()->normalize_title( $this->title );
	}

	public function get_display() : string {
		if ( empty( $this->_display ) ) {
			$display = empty( $this->display ) ? '%title%' : $this->display;
			$display = str_replace( '%title%', $this->get_title_formatted(), $display );

			$this->_display = $display;
		}

		return $this->_display;
	}

	public function get_tag_attributes() : string {
		$attributes = array();

		if ( ! empty( $this->rel ) ) {
			$attributes['rel'] = $this->rel;
		}

		if ( ! empty( $this->rel ) ) {
			$attributes['rel'] = $this->rel;
		}

		if ( $this->get_display() != $this->get_title_formatted() ) {
			$attributes['title'] = $this->get_title_formatted();
		}

		return Arr::to_html_attributes( $attributes );
	}

	public function get_css_classes( $i = false, $extra = '' ) : string {
		$class = array(
			'breadcrumb-item',
			'breadcrumb-item-type-' . $this->type,
		);

		if ( $i ) {
			$class[] = 'breadcrumb-item-order-' . $i;
		}

		if ( $this->id > 0 ) {
			$class[] = 'breadcrumb-item-' . $this->type . '-' . $this->id;
		}

		if ( ! empty( $this->class ) ) {
			$class[] = $this->class;
		}

		if ( ! empty( $extra ) ) {
			$class[] = $extra;
		}

		return join( ' ', $class );
	}
}
