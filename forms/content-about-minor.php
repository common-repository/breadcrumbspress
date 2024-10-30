<?php

use function Dev4Press\v49\Functions\panel;

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'breadcrumbspress' ); ?></h3>
    <p>
        <strong><?php esc_html_e( 'Version', 'breadcrumbspress' ); ?> <span>2.2 / 2.3</span></strong> &minus;
        New options and many other improvements. Shared library updated.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'breadcrumbspress' ); ?> <span>2.0.1 / 2.1</span></strong> &minus;
        Few updates and a fix.
    </p>
    <p>
		<?php printf( __( 'For more information, see <a href=\'%s\'>the changelog</a>.', 'breadcrumbspress' ), panel()->a()->panel_url( 'about', 'changelog' ) ); ?>
    </p>
</div>
