<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Expand;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Helper;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Post;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Taxonomy;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Term;
use Dev4Press\Plugin\BreadcrumbsPress\Extend\Plugin;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bbPress extends Plugin {
	private $post_types = array();

	public static function instance() : bbPress {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new bbPress();
			$instance->run();
		}

		return $instance;
	}

	public function get_post_types() : array {
		if ( empty( $this->post_types ) ) {
			$this->post_types = array(
				bbp_get_forum_post_type(),
				bbp_get_topic_post_type(),
				bbp_get_reply_post_type(),
			);
		}

		return $this->post_types;
	}

	public function ready() {
		if ( breadcrumbspress_settings()->get( 'bbpress_disable_breadcrumbs' ) ) {
			add_filter( 'bbp_no_breadcrumb', '__return_true' );
		}
	}

	public function root() : Crumb {
		$title   = breadcrumbspress_settings()->get_item_title( 'bbpress_root' );
		$display = breadcrumbspress_settings()->get_item_display( 'bbpress_root' );

		if ( empty( $display ) ) {
			switch ( breadcrumbspress_settings()->get( 'bbpress_root_display' ) ) {
				case 'icon':
					$display = Helper::instance()->icon( breadcrumbspress_settings()->get( 'bbpress_root_icon' ) );
					break;
				case 'html':
					$display = breadcrumbspress_settings()->get( 'bbpress_root_html' );
					break;
			}
		}

		if ( empty( $title ) ) {
			$title = $this->b()->get_title_forums();
		}

		if ( empty( $display ) ) {
			$display = '%title%';
		}

		return $this->b()->crumb( array(
			'type'    => 'bbp_root',
			'title'   => $title,
			'display' => $display,
			'url'     => $this->b()->get_url_forums(),
			'current' => false,
		) );
	}

	public function directory( bool $current = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'    => 'bbp_directory',
			'title'   => $current ? $this->b()->title( 'bbpress_directory' ) : $this->b()->title( 'bbpress_directory_profile' ),
			'display' => $current ? $this->b()->display( 'bbpress_directory' ) : $this->b()->display( 'bbpress_directory_profile' ),
			'url'     => gdmed_get_members_directory_url(),
			'current' => $current,
		) );
	}

	/** @return Crumb[] */
	public function single( WP_Post $post ) : array {
		$crumbs = array();

		switch ( $post->post_type ) {
			case bbp_get_forum_post_type():
				$crumbs = $this->get_forum_page( $post->ID );
				break;
			case bbp_get_topic_post_type():
				$crumbs = $this->get_topic_page( $post->ID );
				break;
			case bbp_get_reply_post_type():
				$crumbs = $this->get_reply_page( $post->ID );
				break;
		}

		return $crumbs;
	}

	/**
	 * @inheritDoc
	 */
	public function build( $breadcrumbs ) : array {
		if ( $this->q()->is_bbpress() ) {
			$breadcrumbs = array();

			if ( bbp_is_forum_archive() ) {
				$breadcrumbs                      = $this->get_root_crumbs();
				$breadcrumbs['bbp_root']->current = true;
			} else if ( $this->q()->is_bbpress_view() ) {
				$breadcrumbs = $this->get_topic_view_page();
			} else if ( $this->q()->is_bbpress_search() ) {
				if ( $this->q()->is_bbpress_search_results() ) {
					$breadcrumbs = $this->get_search_results_page();
				} else {
					$breadcrumbs = $this->get_search_page();
				}
			} else if ( $this->q()->is_bbpress_directory() ) {
				$breadcrumbs = $this->get_user_directory_page();
			} else if ( $this->q()->is_bbpress_user() ) {
				$breadcrumbs = $this->get_user_profile_page();
			} else if ( $this->q()->is_bbpress_topic_tag() ) {
				$breadcrumbs = $this->get_topic_tag_page();
			} else if ( $this->q()->is_bbpress_topic_prefix() ) {
				$breadcrumbs = $this->get_topic_prefix_page();
			}
		}

		return $breadcrumbs;
	}

	/** @return Crumb[] */
	public function get_root_crumbs() : array {
		return array(
			'home'     => $this->b()->home(),
			'bbp_root' => $this->root(),
		);
	}

	/**
	 * @param null|int $forum
	 * @param bool     $check_edit
	 *
	 * @return Crumb[]
	 */
	public function get_forum_page( $forum = null, $check_edit = true ) : array {
		$forum = is_null( $forum ) ? $this->q()->get_post_id() : $forum;
		$_post = Post::instance( $forum );

		$crumbs = $this->get_root_crumbs() +
		          $this->c()->get_ancestors_crumbs( $_post, 'full' );

		$crumbs['post'] = $this->c()->crumbs_post( $_post );

		if ( $check_edit && $this->q()->is_bbpress_edit() ) {
			$crumbs['post']->current = false;

			$crumbs['edit'] = $this->b()->crumb( array(
				'type'    => 'bbp_edit',
				'title'   => __( 'Edit', 'breadcrumbspress' ),
				'current' => true,
			) );
		}

		return $crumbs;
	}

	/**
	 * @param null|int $topic
	 * @param bool     $check_edit
	 *
	 * @return Crumb[]
	 */
	public function get_topic_page( $topic = null, $check_edit = true, $force_edit = false ) : array {
		$topic = is_null( $topic ) ? $this->q()->get_post_id() : $topic;
		$_post = Post::instance( $topic );

		$crumbs                                      = $this->get_forum_page( $_post->get_parent_id(), false );
		$crumbs['post']->current                     = false;
		$crumbs[ 'post_' . $_post->get_parent_id() ] = $crumbs['post'];
		unset( $crumbs['post'] );

		$crumbs['post'] = $this->c()->crumbs_post( $_post );

		if ( $force_edit || ( $check_edit && $this->q()->is_bbpress_edit() ) ) {
			$crumbs['post']->current = false;

			$crumbs['edit'] = $this->b()->crumb( array(
				'type'    => 'bbp_edit',
				'title'   => __( 'Edit', 'breadcrumbspress' ),
				'current' => true,
			) );
		}

		return $crumbs;
	}

	/**
	 * @param null|int $reply
	 * @param bool     $check_edit
	 *
	 * @return Crumb[]
	 */
	public function get_reply_page( $reply = null, $check_edit = true, $force_edit = false ) : array {
		$reply = is_null( $reply ) ? $this->q()->get_post_id() : $reply;
		$_post = Post::instance( $reply );

		$crumbs                                      = $this->get_topic_page( $_post->get_parent_id(), false );
		$crumbs['post']->current                     = false;
		$crumbs[ 'post_' . $_post->get_parent_id() ] = $crumbs['post'];
		unset( $crumbs['post'] );

		$crumbs['post'] = $this->c()->crumbs_post( $_post );

		if ( $force_edit || ( $check_edit && $this->q()->is_bbpress_edit() ) ) {
			$crumbs['post']->current = false;

			$crumbs['edit'] = $this->b()->crumb( array(
				'type'    => 'bbp_edit',
				'title'   => __( 'Edit', 'breadcrumbspress' ),
				'current' => true,
			) );
		}

		return $crumbs;
	}

	/**
	 * @param null|string $view
	 *
	 * @return Crumb[]
	 */
	public function get_topic_view_page( $view = null ) : array {
		$view       = is_null( $view ) ? $this->q()->get_bbp_view() : $view;
		$view_title = bbp_get_view_title( $view );

		$crumbs             = $this->get_root_crumbs();
		$crumbs['bbp_view'] = $this->b()->crumb( array(
			'type'    => 'bbp_view',
			'title'   => $this->b()->title( 'bbpress_view', $view_title ),
			'display' => $this->b()->display( 'bbpress_view' ),
			'url'     => bbp_get_view_url( $view ),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @param bool $current
	 *
	 * @return Crumb[]
	 */
	public function get_search_page( $current = true ) : array {
		$crumbs               = $this->get_root_crumbs();
		$crumbs['bbp_search'] = $this->b()->crumb( array(
			'type'    => 'bbp_search',
			'title'   => $this->b()->title( 'bbpress_search' ),
			'display' => $this->b()->display( 'bbpress_search' ),
			'url'     => bbp_get_search_url(),
			'current' => $current,
		) );

		return $crumbs;
	}

	/**
	 * @param null|string $query
	 *
	 * @return Crumb[]
	 */
	public function get_search_results_page( $query = null ) : array {
		$query = is_null( $query ) ? $this->q()->get_bbp_search() : $query;
		$value = esc_html( $query );

		$crumbs                       = $this->get_search_page( false );
		$crumbs['bbp_search_results'] = $this->b()->crumb( array(
			'type'    => 'bbp_search_results',
			'title'   => $this->b()->title( 'bbpress_search_results', $value ),
			'display' => $this->b()->display( 'bbpress_search_results' ),
			'url'     => bbp_get_search_results_url(),
			'current' => true,
		) );

		return $crumbs;
	}

	/**
	 * @param null|int    $user
	 * @param null|string $page
	 *
	 * @return Crumb[]
	 */
	public function get_user_profile_page( $user = null, $page = null ) : array {
		$user = is_null( $user ) ? $this->q()->get_bbp_user() : $user;
		$page = is_null( $page ) ? $this->q()->get_bbp_profile() : $page;
		$page = is_null( $page ) || empty( $page ) ? 'profile' : $page;

		$crumbs = $this->get_root_crumbs();

		if ( breadcrumbspress()->has_gd_members_directory() && breadcrumbspress_settings()->get( 'bbpress_directory_crumb' ) ) {
			$crumbs['bbp_directory'] = $this->directory();
		}

		$crumbs['bbp_profile'] = $this->b()->crumb( array(
			'type'    => 'bbp_profile',
			'title'   => Helper::instance()->user_title( $user ),
			'url'     => bbp_get_user_profile_url( $user ),
			'current' => $page == 'profile',
		) );

		if ( $page !== 'profile' ) {
			switch ( $page ) {
				case 'topics':
					$crumbs['bbp_profile_topics'] = $this->b()->crumb( array(
						'type'    => 'bbp_profile_topics',
						'title'   => __( 'Topics Started', 'breadcrumbspress' ),
						'url'     => bbp_get_user_topics_created_url( $user ),
						'current' => true,
					) );
					break;
				case 'replies':
					$crumbs['bbp_profile_replies'] = $this->b()->crumb( array(
						'type'    => 'bbp_profile_replies',
						'title'   => __( 'Replies Created', 'breadcrumbspress' ),
						'url'     => bbp_get_user_replies_created_url( $user ),
						'current' => true,
					) );
					break;
				case 'edit':
					$crumbs['bbp_profile_edit'] = $this->b()->crumb( array(
						'type'    => 'bbp_profile_edit',
						'title'   => __( 'Edit', 'breadcrumbspress' ),
						'url'     => bbp_get_user_profile_edit_url( $user ),
						'current' => true,
					) );
					break;
				case 'engagements':
					$crumbs['bbp_profile_engagements'] = $this->b()->crumb( array(
						'type'    => 'bbp_profile_engagements',
						'title'   => __( 'Engagements', 'breadcrumbspress' ),
						'url'     => bbp_get_user_engagements_url( $user ),
						'current' => true,
					) );
					break;
				case 'subscriptions':
					$crumbs['bbp_profile_subscriptions'] = $this->b()->crumb( array(
						'type'    => 'bbp_profile_subscriptions',
						'title'   => __( 'Subscriptions', 'breadcrumbspress' ),
						'url'     => bbp_get_subscriptions_permalink( $user ),
						'current' => true,
					) );
					break;
				case 'favorites':
					$crumbs['bbp_profile_favorites'] = $this->b()->crumb( array(
						'type'    => 'bbp_profile_favorites',
						'title'   => __( 'Favorites', 'breadcrumbspress' ),
						'url'     => bbp_get_favorites_permalink( $user ),
						'current' => true,
					) );
					break;
			}
		}

		return $crumbs;
	}

	/** @return Crumb[] */
	public function get_user_directory_page() : array {
		$crumbs                  = $this->get_root_crumbs();
		$crumbs['bbp_directory'] = $this->directory( true );

		return $crumbs;
	}

	/**
	 * @param null $tag
	 *
	 * @return Crumb[]
	 */
	public function get_topic_tag_page( $tag = null ) : array {
		$tag  = is_null( $tag ) ? $this->q()->get_term_id() : $tag;
		$edit = $this->q()->is_bbpress_edit();

		$_term = Term::instance( $tag );
		$_type = Taxonomy::instance( $_term->get_taxonomy() );

		$crumbs = $this->get_root_crumbs();

		$crumbs['bbp_tag'] = $this->b()->crumb( array(
			'type'    => 'bbp_tag',
			'title'   => ! $edit
				?
				$this->b()->title( 'tax_' . $_term->get_taxonomy(), $_term->title(), '%value%', array( '%tax%' => $_type->get_label_singular() ) )
				:
				$this->b()->title( 'bbpress_tag', $_term->title(), '%value%', array( '%tax%' => $_type->get_label_singular() ) ),
			'display' => ! $edit
				?
				$this->b()->display( 'tax_' . $_term->get_taxonomy() )
				:
				$this->b()->display( 'bbpress_tag' ),
			'url'     => bbp_get_topic_tag_link( $_term->get_slug() ),
			'current' => ! $edit,
		) );

		if ( $edit ) {
			$crumbs['edit'] = $this->b()->crumb( array(
				'type'    => 'bbp_edit',
				'title'   => __( 'Edit', 'breadcrumbspress' ),
				'url'     => bbp_get_topic_tag_edit_link( $_term->get_slug() ),
				'current' => true,
			) );
		}

		return $crumbs;
	}

	/**
	 * @param null $prefix
	 *
	 * @return Crumb[]
	 */
	public function get_topic_prefix_page( $prefix = null ) : array {
		$prefix = is_null( $prefix ) ? $this->q()->get_term_id() : $prefix;

		$_term = Term::instance( $prefix );
		$_type = Taxonomy::instance( $_term->get_taxonomy() );

		if ( $this->q()->is_single( bbp_get_forum_post_type() ) ) {
			$crumbs                  = $this->get_forum_page( null, false );
			$url                     = add_query_arg( 'topic-prefix', $_term->get_slug(), $crumbs['post']->url );
			$crumbs['post']->current = false;
		} else {
			$url    = gdtox_get_topic_prefix_link( $_term->get_slug() );
			$crumbs = $this->get_root_crumbs();
		}

		$crumbs['bbp_prefix'] = $this->b()->crumb( array(
			'type'    => 'bbp_prefix',
			'title'   => $this->b()->title( 'tax_' . $_term->get_taxonomy(), $_term->title(), '%value%', array( '%tax%' => $_type->get_label_singular() ) ),
			'display' => $this->b()->display( 'tax_' . $_term->get_taxonomy() ),
			'url'     => $url,
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
}