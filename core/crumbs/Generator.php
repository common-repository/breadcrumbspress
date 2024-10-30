<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Crumbs;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Generator {
	private $status = false;
	private $crumbs = array();

	public function __construct() {
	}

	public static function instance() : Generator {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Generator();
		}

		return $instance;
	}

	public function q() : Query {
		return Query::instance();
	}

	public function b() : Builder {
		return Builder::instance();
	}

	public function crumb( $crumb = array() ) : Crumb {
		return new Crumb( $crumb );
	}

	/** @return Crumb[] */
	public function breadcrumbs() : array {
		return $this->crumbs;
	}

	/** @return Crumb[] */
	public function build() : array {
		if ( empty( $this->crumbs ) ) {
			$this->status = true;

			if ( $this->q()->is_home() || $this->q()->is_front() ) {
				$this->crumbs = $this->b()->get_home_page();
			} else if ( $this->q()->is_posts() ) {
				$this->crumbs = $this->b()->get_posts_page();
			} else {
				$this->crumbs = apply_filters( 'breadcrumbspress_generator_build', $this->crumbs );

				if ( empty( $this->crumbs ) ) {
					if ( $this->q()->is_search() ) {
						$this->crumbs = $this->b()->get_search_results_page();
					} else if ( $this->q()->is_post_type_single() ) {
						$this->crumbs = $this->b()->get_post_type_single_page();
					} else if ( $this->q()->is_post_type_archive() ) {
						$this->crumbs = $this->b()->get_post_type_archive_page();
					} else if ( $this->q()->is_tax_archive() ) {
						$this->crumbs = $this->b()->get_taxonomy_term_archive_page();
					} else if ( $this->q()->is_author() ) {
						$this->crumbs = $this->b()->get_author_archive_page();
					} else if ( $this->q()->is_date() ) {
						if ( $this->q()->is_year() ) {
							$this->crumbs = $this->b()->get_date_year_page();
						} else if ( $this->q()->is_month() ) {
							$this->crumbs = $this->b()->get_date_month_page();
						} else if ( $this->q()->is_day() ) {
							$this->crumbs = $this->b()->get_date_day_page();
						}
					} else if ( $this->q()->is_archive() ) {
						$this->crumbs = $this->b()->get_archive_page();
					} else if ( $this->q()->is_404() ) {
						$this->crumbs = $this->b()->get_error_page();
					}
				}
			}

			$this->crumbs = apply_filters( 'breadcrumbspress_generator_complete', $this->crumbs );
			$this->status = false;
		}

		return $this->crumbs;
	}

	public function is() : bool {
		return $this->status;
	}
}
