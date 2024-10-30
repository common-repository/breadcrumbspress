<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Crumbs;

use Dev4Press\Plugin\BreadcrumbsPress\Basic\Query;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Crumb;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Taxonomy;
use Dev4Press\Plugin\BreadcrumbsPress\Data\Term;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Archives {
	public function __construct() {
	}

	public static function instance() : Archives {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Archives();
		}

		return $instance;
	}

	public function q() : Query {
		return Query::instance();
	}

	public function b() : Builder {
		return Builder::instance();
	}

	public function chain_terms( int $term_id, string $taxonomy, array $chain = array() ) : array {
		$term = get_term( $term_id, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			$chain[] = $term;

			$parent = $term->parent;

			if ( $parent != 0 ) {
				$chain = $this->chain_terms( $parent, $taxonomy, $chain );
			}
		}

		return $chain;
	}

	public function crumbs_term( Term $term, Taxonomy $type, bool $current = false ) : Crumb {
		return $this->b()->crumb( array(
			'type'    => 'term',
			'title'   => $current ? $this->b()->title( 'tax_' . $term->get_taxonomy(), $term->title(), '%value%', array( '%tax%' => $type->get_label_singular() ) ) : $term->title(),
			'display' => $current ? $this->b()->display( 'tax_' . $term->get_taxonomy() ) : '%title%',
			'url'     => $term->url(),
			'current' => $current,
			'id'      => $term->get_id(),
		) );
	}

	public function crumbs_posts_page() : Crumb {
		return $this->b()->crumb( array(
			'type'  => 'posts_page',
			'title' => get_the_title( $this->q()->get_posts_page_id() ),
			'url'   => get_permalink( $this->q()->get_posts_page_id() ),
		) );
	}

	public function crumbs_cpt_archive( $cpt ) : Crumb {
		$object = get_post_type_object( $cpt );

		return $this->b()->crumb( array(
			'type'  => 'cpt_archive',
			'title' => $object->label,
			'url'   => get_post_type_archive_link( $cpt ),
		) );
	}

	/** @return Crumb[] */
	public function current( WP_Term $term ) : array {
		return $this->single( $term );
	}

	/** @return Crumb[] */
	public function single( WP_Term $term, $path = null ) : array {
		$_type = Taxonomy::instance( $term->taxonomy );
		$_term = Term::instance( $term );

		$_crumb_home = $this->b()->home();
		$_crumb_term = $this->crumbs_term( $_term, $_type, true );

		$path      = is_null( $path ) ? $_type->get_path() : $path;
		$hierarchy = $_type->get_hierarchy();

		$crumbs = array();

		if ( $path == 'taxonomy' && $hierarchy != 'no' ) {
			if ( $hierarchy == 'full' ) {
				$crumbs = $this->get_path_full( $_type, $_term, $_crumb_home, $_crumb_term );
			} else if ( $hierarchy == 'parent' && $_term->has_parent() ) {
				$crumbs = $this->get_path_parent( $_type, $_term, $_crumb_home, $_crumb_term );
			}
		}

		if ( empty( $crumbs ) ) {
			$crumbs = $this->get_path_basic( $_type, $_term, $_crumb_home, $_crumb_term );
		}

		if ( $this->q()->has_posts_page() && $_type->get_with_posts() && $path != 'posts' ) {
			$crumbs = array_slice( $crumbs, 0, 1, true ) +
			          array( 'posts' => $this->crumbs_posts_page() ) +
			          array_slice( $crumbs, 1, null, true );
		} else {
			$cpt = $_type->get_with_cpt_archive();

			if ( ! empty( $cpt ) ) {
				$crumbs = array_slice( $crumbs, 0, 1, true ) +
				          array( 'posts' => $this->crumbs_cpt_archive( $cpt ) ) +
				          array_slice( $crumbs, 1, null, true );
			}
		}

		return $crumbs;
	}

	/** @return Crumb[] */
	public function get_path_basic( Taxonomy $_type, Term $_term, Crumb $_crumb_home, Crumb $_crumb_term ) : array {
		return array(
			'home' => $_crumb_home,
			'term' => $_crumb_term,
		);
	}

	/** @return Crumb[] */
	public function get_path_parent( Taxonomy $_type, Term $_term, Crumb $_crumb_home, Crumb $_crumb_term ) : array {
		$parent_term = get_term_by( 'id', $_term->get_parent_id(), $_type->get_name() );
		$_parent     = Term::instance( $parent_term );

		return array(
			'home'   => $_crumb_home,
			'parent' => $this->crumbs_term( $_parent, $_type ),
			'term'   => $_crumb_term,
		);
	}

	/** @return Crumb[] */
	public function get_path_full( Taxonomy $_type, Term $_term, Crumb $_crumb_home, Crumb $_crumb_term ) : array {
		$terms = array_reverse( $this->chain_terms( $_term->get_id(), $_term->get_taxonomy() ) );

		$crumbs = array(
			'home' => $_crumb_home,
		);

		if ( count( $terms ) > 1 ) {
			for ( $i = 0; $i < count( $terms ) - 1; $i ++ ) {
				$_parent = Term::instance( $terms[ $i ] );

				$crumbs[ 'term_' . $_parent->get_id() ] = $this->crumbs_term( $_parent, $_type );
			}
		}

		$crumbs['term'] = $_crumb_term;

		return $crumbs;
	}
}
