<?php

$days = breadcrumbspress_settings()->get_last_tracked_days();
$days = $days === false ? 15 : (int) $days;

$_class   = $days < 4 ? 'd4p-badge-ok' : ( $days > 14 ? 'd4p-badge-error' : 'd4p-badge-purple' );
$_icon    = $days < 4 ? 'd4p-ui-check-square' : ( $days > 14 ? 'd4p-ui-clear' : 'd4p-ui-warning' );
$_badge   = $days < 4 ? __( 'OK', 'breadcrumbspress' ) : ( $days > 14 ? __( 'Error', 'breadcrumbspress' ) : __( 'Problem', 'breadcrumbspress' ) );
$_message = $days < 4 ? __( 'Everything appears to be in order.', 'breadcrumbspress' ) : ( $days > 14 ? __( 'Breadcrumbs are not displaying on your website.', 'breadcrumbspress' ) : __( 'Breadcrumbs have not been displayed for days.', 'breadcrumbspress' ) );

?>

<div class="d4p-group d4p-dashboard-card d4p-card-double d4p-dashboard-status">
    <h3><?php esc_html_e( 'Plugin Status', 'breadcrumbspress' ); ?></h3>
    <div class="d4p-group-inner">
        <div>
            <span class="d4p-card-badge d4p-badge-right <?php echo $_class; ?>"><i class="d4p-icon <?php echo $_icon; ?>"></i><?php echo $_badge; ?></span>
            <div class="d4p-status-message"><?php echo $_message; ?></div>
        </div>
    </div>
</div>
