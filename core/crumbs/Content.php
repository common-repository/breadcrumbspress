<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Crumbs;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Helper;
use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Post;
use Dev4Press\Plugin\BreadcrumbsPress\Data\PostType;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Taxonomy;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\bbPress;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\GDContentTools;
use Dev4Press\Plugin\BreadcrumbsPress\Expand\GDKnowledgeBase;
use WP_Post;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Content {
	public function __construct() {
	}

	public static function instance() : Content {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Content();
		}

		return $instance;
	}

	public function q() : Query {
		return Query::instance();
	}

	public function b() : Builder {
		return Builder::instance();
	}

	public function crumbs_post( Post $post, bool $current = true ) : Crumb {
		return $this->b()->crumb( array(
			'type'    => 'post',
			'title'   => $this->b()->title( 'cpt_single_' . $post->get_post_type(), $post->title() ),
			'display' => $this->b()->display( 'cpt_single_' . $post->get_post_type() ),
			'url'     => $post->url(),
			'id'      => $post->get_id(),
			'current' => $current,
		) );
	}

	public function crumbs_term( WP_Term $term, Post $post, bool $cpt = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'term',
			'title' => $term->name,
			'url'   => get_term_link( $term ),
			'id'    => $term->term_id,
		) );
	}

	public function crumbs_post_author( Post $post, bool $cpt = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'post',
			'title' => $post->author_display(),
			'url'   => $post->author_url(),
		) );
	}

	public function crumbs_post_type_archive( PostType $post_type ) : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'post_type',
			'title' => $post_type->get_label(),
			'url'   => get_post_type_archive_link( $post_type->get_name() ),
		) );
	}

	public function crumbs_year( Post $post, bool $cpt = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'year',
			'title' => $post->get_year_formatted(),
			'url'   => get_year_link( $post->get_year() ),
		) );
	}

	public function crumbs_month( Post $post, bool $cpt = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'month',
			'title' => $post->get_month_formatted(),
			'url'   => get_month_link( $post->get_year(), $post->get_month() ),
		) );
	}

	public function crumbs_day( Post $post, bool $cpt = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'day',
			'title' => $post->get_day_formatted(),
			'url'   => get_day_link( $post->get_year(), $post->get_month(), $post->get_day() ),
		) );
	}

	public function crumbs_posts_page() : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'posts_page',
			'title' => get_the_title( $this->q()->get_posts_page_id() ),
			'url'   => get_permalink( $this->q()->get_posts_page_id() ),
		) );
	}

	/** @return Crumb[] */
	public function current( WP_Post $post ) : array {
		return $this->single( $post );
	}

	/**
	 * @param WP_Post     $post
	 * @param string|null $path
	 * @param string|null $taxonomy
	 *
	 * @return Crumb[]
	 */
	public function single( WP_Post $post, $path = null, $taxonomy = null ) : array {
		$_type = PostType::instance( $post->post_type );
		$_post = Post::instance( $post );

		if ( $_type->is_bbpress() ) {
			return bbPress::instance()->single( $post );
		}

		$_crumb_home = $this->b()->home();
		$_crumb_post = $this->crumbs_post( $_post );

		$path      = is_null( $path ) ? $_type->get_path() : $path;
		$taxonomy  = is_null( $taxonomy ) ? $_type->get_taxonomy() : $taxonomy;
		$hierarchy = $_type->get_hierarchy();

		if ( $_post->get_parent_id() == 0 ) {
			$hierarchy = 'no';
		}

		$crumbs = array();

		if ( $path == 'taxonomy' || $path == 'post_type_taxonomy' ) {
			if ( ! $_type->has_terms() || ! $_type->is_taxonomy_valid( $taxonomy ) ) {
				if ( $_type->has_archive() ) {
					$path = 'post_type';
				}
			}
		}

		switch ( $path ) {
			case 'post_type':
				if ( $_type->has_archive() ) {
					$crumbs = $this->get_path_post_type_archive( $_type, $_post, $_crumb_home, $_crumb_post, $hierarchy );
				}
				break;
			case 'author':
				if ( $_type->has_author() ) {
					$crumbs = $this->get_path_author( $_type, $_post, $_crumb_home, $_crumb_post );
				}
				break;
			case 'date':
			case 'day':
				if ( $_type->has_day() ) {
					$crumbs = $this->get_path_day( $_type, $_post, $_crumb_home, $_crumb_post );
				}
				break;
			case 'month':
				if ( $_type->has_month() ) {
					$crumbs = $this->get_path_month( $_type, $_post, $_crumb_home, $_crumb_post );
				}
				break;
			case 'year':
				if ( $_type->has_year() ) {
					$crumbs = $this->get_path_year( $_type, $_post, $_crumb_home, $_crumb_post );
				}
				break;
			case 'posts':
				if ( $_type->has_posts() && Query::instance()->has_posts_page() ) {
					$crumbs = $this->get_path_posts( $_type, $_post, $_crumb_home, $_crumb_post );
				}
				break;
			case 'parent_post':
				if ( $_type->has_parent_post() && $_post->get_parent_id() > 0 ) {
					$parent = get_post( $_post->get_parent_id() );

					if ( $parent instanceof WP_Post ) {
						$crumbs                  = $this->single( $parent );
						$crumbs['post']->current = false;
						$crumbs['child_post']    = $_crumb_post;
					}
				}
				break;
			case 'post_type_taxonomy':
			case 'taxonomy':
				if ( $_type->has_terms() && $_type->is_taxonomy_valid( $taxonomy ) ) {
					$term = $_post->get_first_term( $taxonomy );

					if ( $term !== false ) {
						$chain = Helper::instance()->chain_terms( $term->term_id, $taxonomy );
						$chain = array_reverse( $chain );

						$crumbs = array(
							'home' => $_crumb_home,
						);

						if ( $this->q()->has_posts_page() && Taxonomy::instance( $taxonomy )->get_with_posts() ) {
							$crumbs['posts'] = $this->crumbs_posts_page();
						}

						if ( $path == 'post_type_taxonomy' ) {
							$crumbs['post_type_archive'] = $this->crumbs_post_type_archive( $_type );
						}

						for ( $i = 0; $i < count( $chain ); $i ++ ) {
							$crumbs[ 'term_' . $chain[ $i ]->term_id ] = $this->crumbs_term( $chain[ $i ], $_post );
						}

						$crumbs['post'] = $_crumb_post;
					}
				}
				break;
		}

		if ( empty( $crumbs ) ) {
			$crumbs = $this->get_path_basic( $_type, $_post, $_crumb_home, $_crumb_post, $hierarchy );
		}

		if ( Query::instance()->has_posts_page() && $_type->get_with_posts() && $path != 'posts' ) {
			$crumbs = array_slice( $crumbs, 0, 1, true ) +
			          array( 'posts' => $this->crumbs_posts_page() ) +
			          array_slice( $crumbs, 1, null, true );
		}

		return $crumbs;
	}

	/** @return Crumb[] */
	public function get_ancestors_crumbs( Post $_post, string $hierarchy ) : array {
		$ancestors = array();
		$crumbs    = array();

		if ( $hierarchy == 'full' ) {
			$ancestors = array_reverse( (array) get_post_ancestors( $_post->get_id() ) );
		} else if ( $hierarchy == 'parent' ) {
			if ( $_post->get_parent_id() > 0 ) {
				$ancestors[] = $_post->get_parent_id();
			}
		}

		foreach ( $ancestors as $id ) {
			$_ancestor = Post::instance( $id );

			$crumbs[ 'post_' . $id ] = $this->b()->crumb( array(
				'type'    => 'post',
				'title'   => $_ancestor->title(),
				'url'     => $_ancestor->url(),
				'current' => false,
			) );
		}

		return $crumbs;
	}

	/** @return Crumb[] */
	public function get_path_basic( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post, string $hierarchy = '' ) : array {
		$hierarchy = empty( $hierarchy ) ? $_type->get_hierarchy() : $hierarchy;

		$crumbs = array(
			'home' => $_crumb_home,
			'post' => $_crumb_post,
		);

		if ( $hierarchy != 'no' ) {
			$crumbs = array_slice( $crumbs, 0, 1, true ) +
			          $this->get_ancestors_crumbs( $_post, $hierarchy ) +
			          array_slice( $crumbs, 1, null, true );
		}

		return $crumbs;
	}

	/** @return Crumb[] */
	public function get_path_post_type_archive( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post, string $hierarchy ) : array {
		$crumbs = $this->get_path_basic( $_type, $_post, $_crumb_home, $_crumb_post, $hierarchy );

		return array_slice( $crumbs, 0, 1, true ) +
		       array( 'post_type_archive' => $this->crumbs_post_type_archive( $_type ) ) +
		       array_slice( $crumbs, 1, null, true );
	}

	/** @return Crumb[] */
	public function get_path_posts( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post ) : array {
		return array(
			'home'       => $_crumb_home,
			'posts_page' => $this->crumbs_posts_page(),
			'post'       => $_crumb_post,
		);
	}

	/** @return Crumb[] */
	public function get_path_author( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post ) : array {
		$breadcrumbs = array(
			'home'   => $_crumb_home,
			'author' => $this->crumbs_post_author( $_post ),
			'post'   => $_crumb_post,
		);

		return $this->b()->expand_with_posts_page( $breadcrumbs, 'author_with_posts' );
	}

	/** @return Crumb[] */
	public function get_path_day( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post ) : array {
		$breadcrumbs = array(
			'home'  => $_crumb_home,
			'year'  => $this->crumbs_year( $_post ),
			'month' => $this->crumbs_month( $_post ),
			'day'   => $this->crumbs_day( $_post ),
			'post'  => $_crumb_post,
		);

		return $this->b()->expand_with_posts_page( $breadcrumbs, 'date_with_posts' );
	}

	/** @return Crumb[] */
	public function get_path_month( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post ) : array {
		$breadcrumbs = array(
			'home'  => $_crumb_home,
			'year'  => $this->crumbs_year( $_post ),
			'month' => $this->crumbs_month( $_post ),
			'post'  => $_crumb_post,
		);

		return $this->b()->expand_with_posts_page( $breadcrumbs, 'date_with_posts' );
	}

	/** @return Crumb[] */
	public function get_path_year( PostType $_type, Post $_post, Crumb $_crumb_home, Crumb $_crumb_post ) : array {
		$breadcrumbs = array(
			'home' => $_crumb_home,
			'year' => $this->crumbs_year( $_post ),
			'post' => $_crumb_post,
		);

		return $this->b()->expand_with_posts_page( $breadcrumbs, 'date_with_posts' );
	}
}
