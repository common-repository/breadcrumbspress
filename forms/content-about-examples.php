<div class="breadcrumbs-examples">
	<?php

	use Dev4Press\Plugin\BreadcrumbsPress\Basic\Plugin;

	Plugin::instance()->enqueue_files();

	$_posts = new WP_Query( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( ! empty( $_posts->posts ) ) {
		echo '<h4>' . esc_html__( 'Latest Blog Post', 'breadcrumbspress' ) . '</h4>';

		$post = $_posts->posts[0];

		breadcrumbspress_build()->post( $post );
	}

	$_pages = new WP_Query( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'rand',
	) );

	if ( ! empty( $_pages->posts ) ) {
		echo '<h4>' . esc_html__( 'Random Page', 'breadcrumbspress' ) . '</h4>';

		$page = $_pages->posts[0];

		breadcrumbspress_build()->post( $page );
	}

	$_terms = new WP_Term_Query( array(
		'taxonomy'   => 'category',
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => true,
		'number'     => 5,
	) );

	if ( ! empty( $_terms->terms ) ) {
		echo '<h4>' . esc_html__( 'Random Category', 'breadcrumbspress' ) . '</h4>';

		$key  = array_rand( $_terms->terms );
		$term = $_terms->terms[ $key ];

		breadcrumbspress_build()->term( $term );
	}

	?>
</div>