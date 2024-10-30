<?php

use function Dev4Press\v49\Functions\panel;

?>
<div class="d4p-content">
    <div class="d4p-setup-wrapper">
        <div class="d4p-update-info">
			<?php

			breadcrumbspress_settings()->set( 'install', false, 'info' );
			breadcrumbspress_settings()->set( 'update', false, 'info', true );

			?>

            <div class="d4p-install-block">
                <h4>
					<?php esc_html_e( 'All Done', 'breadcrumbspress' ); ?>
                </h4>
                <div>
					<?php esc_html_e( 'Update completed.', 'breadcrumbspress' ); ?>
                </div>
            </div>

            <div class="d4p-install-confirm">
                <a class="button-primary" href="<?php echo panel()->a()->panel_url( 'about' ) ?>&update"><?php esc_html_e( 'Click here to continue', 'breadcrumbspress' ); ?></a>
            </div>
        </div>
    </div>
</div>