<div class="d4p-group d4p-dashboard-card d4p-card-double">
    <h3><?php esc_html_e( 'Breadcrumbs Integration', 'breadcrumbspress' ); ?></h3>
    <div class="d4p-group-inner">
		<?php

		$_methods = array(
			'manual' => __( 'Manual integration', 'breadcrumbspress' ),
			'action' => __( 'Hook to specified action', 'breadcrumbspress' ),
			'auto'   => __( 'Automatic theme integration', 'breadcrumbspress' ),
			'theme'  => __( 'Integration with supported themes', 'breadcrumbspress' ),
		);

		$template = get_template();
		$valid    = breadcrumbspress()->supported_themes();
		$theme    = wp_get_theme( $template );

		if ( isset( $valid[ $template ] ) ) {
			$_theme_message = __( 'This theme is supported by the BreadcrumbsPress plugin for automatic integration.', 'breadcrumbspress' );
		} else {
			$_theme_message = __( 'The theme you are using is not supported for automatic integration.', 'breadcrumbspress' );
		}

		?>
        <h4><?php esc_html_e( 'Currently active theme', 'breadcrumbspress' ); ?></h4>
        <p><?php echo sprintf( __( 'You are currently using theme %s.', 'breadcrumbspress' ), '<strong>' . $theme->Name . '</strong>' ) . ' ' . $_theme_message; ?></p>
        <hr/>
        <h4><?php esc_html_e( 'Current integration method', 'breadcrumbspress' ); ?></h4>
        <p><?php echo sprintf( __( 'Plugin is currently set for %s.', 'breadcrumbspress' ), '<strong>' . $_methods[ breadcrumbspress_settings()->get( 'method', 'integration' ) ] . '</strong>' ) . ' ' . $_theme_message; ?></p>
        <a class="button-primary" href="<?php echo admin_url( 'options-general.php?page=breadcrumbspress&panel=settings&subpanel=integration' ); ?>"><?php esc_html_e( 'Change Integration Settings', 'breadcrumbspress' ); ?></a>
        <hr/>
        <h4><?php esc_html_e( 'Manual integration code', 'breadcrumbspress' ); ?></h4>
        <p><?php esc_html_e( 'This is the basic PHP code block that will produce the breadcrumbs on the current front-end page, based on the plugin settings. This code has to be placed inside the theme template.', 'breadcrumbspress' ); ?></p>
        <div class="breadcrumbspress-integration-code">
            &lt;?php if ( function_exists( 'breadcrumbspress_current' ) ) { breadcrumbspress_current(); } ?>
        </div>
        <hr/>
        <h4><?php esc_html_e( 'Shortcode integration code', 'breadcrumbspress' ); ?></h4>
        <p><?php esc_html_e( 'This is the basic shortcode that is equal to the PHP code displayed above.', 'breadcrumbspress' ); ?></p>
        <div class="breadcrumbspress-integration-code">
            [breadcrumbspress_current]
        </div>
    </div>
</div>