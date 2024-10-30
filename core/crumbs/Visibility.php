<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Crumbs;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Visibility {
	public function __construct() {
	}

	public static function instance() : Visibility {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Visibility();
		}

		return $instance;
	}

	public function q() : Query {
		return Query::instance();
	}

	public function get( $name ) : bool {
		return (bool) breadcrumbspress_settings()->get( $name, 'visibility' );
	}

	public function is_visible() : bool {
		$visible = apply_filters( 'breadcrumbspress_visibility', true );

		if ( $visible ) {
			if ( $this->q()->is_bbpress_view() ) {
				$visible = $this->get( 'bbpress_view' );
			} else if ( $this->q()->is_bbpress_profile() ) {
				$visible = $this->get( 'bbpress_profile' );
			} else if ( $this->q()->is_bbpress_search() ) {
				$visible = $this->get( 'bbpress_search' );
			} else if ( $this->q()->is_bbpress_topic_tag() ) {
				$visible = $this->get( 'bbpress_topic_tag' );
			} else if ( $this->q()->is_bbpress_directory() ) {
				$visible = $this->get( 'bbpress_directory' );
			} else if ( $this->q()->is_bbpress_topic_prefix() ) {
				$visible = $this->get( 'bbpress_topic_prefix' );
			} else if ( $this->q()->is_buddypress_members_directory() ) {
				$visible = $this->get( 'buddypress_members' );
			} else if ( $this->q()->is_buddypress_groups_directory() ) {
				$visible = $this->get( 'buddypress_groups' );
			} else if ( $this->q()->is_buddypress_activity_directory() ) {
				$visible = $this->get( 'buddypress_activity' );
			} else if ( $this->q()->is_buddypress_group() ) {
				$visible = $this->get( 'buddypress_group' );
			} else if ( $this->q()->is_buddypress_group_create() ) {
				$visible = $this->get( 'buddypress_group_create' );
			} else if ( $this->q()->is_buddypress_user() ) {
				$visible = $this->get( 'buddypress_profile' );
			} else if ( $this->q()->is_woocommerce_account_page() ) {
				$visible = $this->get( 'woocommerce_account' );
			} else if ( $this->q()->is_home() ) {
				$visible = $this->get( 'core_home' );
			} else if ( $this->q()->is_front() ) {
				$visible = $this->get( 'core_front' );
			} else if ( $this->q()->is_posts() ) {
				$visible = $this->get( 'core_posts' );
			} else if ( $this->q()->is_author() ) {
				$visible = $this->get( 'core_author_archives' );
			} else if ( $this->q()->is_date() ) {
				$visible = $this->get( 'core_date_archives' );
			} else if ( $this->q()->is_post_type_single() ) {
				$visible = $this->get( 'cpt_single_' . $this->q()->get_post_type() );
			} else if ( $this->q()->is_post_type_archive() ) {
				$visible = $this->get( 'cpt_archive_' . $this->q()->get_post_type() );
			} else if ( $this->q()->is_tax_archive() ) {
				$visible = $this->get( 'tax_' . $this->q()->get_taxonomy() );
			} else if ( $this->q()->is_archive() ) {
				$visible = $this->get( 'core_archives' );
			} else if ( $this->q()->is_404() ) {
				$visible = $this->get( 'core_404' );
			}
		}

		return $visible;
	}
}
