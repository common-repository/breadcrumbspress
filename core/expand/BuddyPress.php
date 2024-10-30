<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Expand;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Helper;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;
use Dev4Press\Plugin\BreadcrumbsPress\Extend\Plugin;

class BuddyPress extends Plugin {
	public static function instance() : BuddyPress {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new BuddyPress();
			$instance->run();
		}

		return $instance;
	}

	/**
	 * @inheritDoc
	 */
	public function build( $breadcrumbs ) : array {
		if ( $this->q()->is_buddypress() ) {
			$breadcrumbs = array();

			if ( $this->q()->is_buddypress_activity_directory() ) {
				$breadcrumbs = $this->get_activity_page();
			} else if ( $this->q()->is_buddypress_groups_directory() ) {
				$breadcrumbs = $this->get_groups_page();
			} else if ( $this->q()->is_buddypress_group() ) {
				$breadcrumbs = $this->get_group_page();
			} else if ( $this->q()->is_buddypress_members_directory() ) {
				$breadcrumbs = $this->get_members_page();
			} else if ( $this->q()->is_buddypress_user() ) {
				$breadcrumbs = $this->get_user_page();
			} else if ( $this->q()->is_buddypress_group_create() ) {
				$breadcrumbs = $this->get_group_create_page();
			}
		}

		return $breadcrumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_root_crumbs() : array {
		return array(
			'home' => $this->b()->home(),
		);
	}

	/**
	 * @return Crumb[]
	 */
	public function get_groups_page() : array {
		$crumbs              = $this->get_root_crumbs();
		$crumbs['bp_groups'] = $this->b()->crumb( array(
			'type'    => 'bp_groups',
			'title'   => $this->b()->title( 'buddypress_groups' ),
			'display' => $this->b()->display( 'buddypress_groups' ),
			'url'     => bp_get_groups_directory_permalink(),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_user_page( $user_id = null, $page = null, $subpage = null ) : array {
		$user_id = is_null( $user_id ) ? $this->q()->get_bp_user() : $user_id;
		$page    = is_null( $page ) ? $this->q()->get_bp_stack_component() : $page;
		$subpage = is_null( $subpage ) ? $this->q()->get_bp_stack_action() : $subpage;

		$crumbs               = $this->get_root_crumbs();
		$crumbs['bp_members'] = $this->b()->crumb( array(
			'type'    => 'bp_members',
			'title'   => $this->b()->title( 'buddypress_members_profile' ),
			'display' => $this->b()->display( 'buddypress_members_profile' ),
			'url'     => bp_get_members_directory_permalink(),
			'current' => false,
		) );

		$crumbs['bp_user'] = $this->b()->crumb( array(
			'type'    => 'bp_user',
			'title'   => Helper::instance()->user_title( $user_id ),
			'url'     => bp_core_get_user_domain( $user_id ),
			'current' => $page == '',
		) );

		if ( $page != '' ) {
			$key = $subkey = $page;
			if ( ! empty( $subpage ) ) {
				$subkey .= '/' . $subpage;
			}

			$item = buddypress()->members->nav->get( $key );

			$crumbs[ 'bp_user_' . $item->slug ] = $this->b()->crumb( array(
				'type'    => 'bp_user_' . $item->slug,
				'title'   => _bp_strip_spans_from_title( $item->name ),
				'url'     => $item->link,
				'current' => $key == $subkey,
			) );

			if ( $key != $subkey ) {
				$subitem = buddypress()->members->nav->get( $subkey );

				if ( $subitem ) {
					$crumbs[ 'bp_user_' . $subitem->slug ] = $this->b()->crumb( array(
						'type'    => 'bp_user_' . $subitem->slug,
						'title'   => _bp_strip_spans_from_title( $subitem->name ),
						'url'     => $subitem->link,
						'current' => true,
					) );
				} else {
					$crumbs['bp_user_action'] = $this->b()->crumb( array(
						'type'    => 'bp_user_action',
						'title'   => $this->q()->get_bp_action(),
						'current' => true,
					) );
				}
			}
		}

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_group_create_page( $step = null ) : array {
		$step = is_null( $step ) ? $this->q()->get_bp_step() : $step;

		$crumbs                    = $this->get_root_crumbs();
		$crumbs['bp_groups']       = $this->b()->crumb( array(
			'type'    => 'bp_groups',
			'title'   => $this->b()->title( 'buddypress_groups_group' ),
			'display' => $this->b()->display( 'buddypress_groups_group' ),
			'url'     => bp_get_groups_directory_permalink(),
			'current' => false,
		) );
		$crumbs['bp_group_create'] = $this->b()->crumb( array(
			'type'    => 'bp_group_create',
			'title'   => $this->b()->title( 'buddypress_group_create' ),
			'display' => $this->b()->display( 'buddypress_group_create' ),
			'url'     => trailingslashit( bp_get_groups_directory_permalink() . 'create' ),
			'current' => false,
		) );

		$crumbs[ 'bp_group_create_' . $step ] = $this->b()->crumb( array(
			'type'    => 'bp_group_create_' . $step,
			'title'   => buddypress()->groups->group_creation_steps[ $step ]['name'],
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_group_page( $group_id = null, $page = null, $subpage = null ) : array {
		$group_id = is_null( $group_id ) ? $this->q()->get_bp_group() : $group_id;
		$page     = is_null( $page ) ? $this->q()->get_bp_action() : $page;
		$subpage  = is_null( $subpage ) ? $this->q()->get_bp_action_var() : $subpage;
		$group    = groups_get_group( $group_id );

		$crumbs              = $this->get_root_crumbs();
		$crumbs['bp_groups'] = $this->b()->crumb( array(
			'type'    => 'bp_groups',
			'title'   => $this->b()->title( 'buddypress_groups_group' ),
			'display' => $this->b()->display( 'buddypress_groups_group' ),
			'url'     => bp_get_groups_directory_permalink(),
			'current' => false,
		) );

		$crumbs['bp_group'] = $this->b()->crumb( array(
			'type'    => 'bp_group',
			'title'   => $this->b()->title( 'buddypress_group', $group->name ),
			'display' => $this->b()->display( 'buddypress_group' ),
			'url'     => bp_get_group_permalink( $group ),
			'current' => $page == 'home',
		) );

		if ( $page !== 'home' ) {
			$key  = $group->slug . '/' . $page;
			$item = buddypress()->groups->nav->get( $key );

			if ( $item ) {
				$crumbs[ 'bp_group_' . $item->slug ] = $this->b()->crumb( array(
					'type'    => 'bp_group_' . $item->slug,
					'title'   => _bp_strip_spans_from_title( $item->name ),
					'url'     => $item->link,
					'current' => $subpage == '',
				) );

				if ( ! empty( $subpage ) ) {
					$_name = '';
					$_link = '';

					foreach ( buddypress()->groups->nav->get() as $_item ) {
						if ( $_item->slug == $subpage ) {
							$_name = $_item->name;
							$_link = $_item->link;
							break;
						}
					}

					if ( ! empty( $_name ) ) {
						$crumbs[ 'bp_group_' . $subpage ] = $this->b()->crumb( array(
							'type'    => 'bp_group_' . $subpage,
							'title'   => _bp_strip_spans_from_title( $_name ),
							'url'     => $_link,
							'current' => true,
						) );
					} else {
						$crumbs[ 'bp_group_' . $item->slug ]->current = true;
					}
				}
			}
		}

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_members_page() : array {
		$crumbs               = $this->get_root_crumbs();
		$crumbs['bp_members'] = $this->b()->crumb( array(
			'type'    => 'bp_members',
			'title'   => $this->b()->title( 'buddypress_members' ),
			'display' => $this->b()->display( 'buddypress_members' ),
			'url'     => bp_get_members_directory_permalink(),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @return Crumb[]
	 */
	public function get_activity_page() : array {
		$crumbs                = $this->get_root_crumbs();
		$crumbs['bp_activity'] = $this->b()->crumb( array(
			'type'    => 'bp_activity',
			'title'   => $this->b()->title( 'buddypress_activity' ),
			'display' => $this->b()->display( 'buddypress_activity' ),
			'url'     => bp_get_activity_directory_permalink(),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @inheritDoc
	 */
	public function complete( $breadcrumbs ) : array {
		return $breadcrumbs;
	}

	public function ready() {

	}
}