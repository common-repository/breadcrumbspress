<?php

use function Dev4Press\v49\Functions\panel;

?>
<div class="d4p-content">
    <div class="d4p-group d4p-group-information">
        <h3><?php esc_html_e( 'Important Information', 'breadcrumbspress' ); ?></h3>
        <div class="d4p-group-inner">
			<?php esc_html_e( 'This tool can remove plugin settings saved in the WordPress options table added by the plugin.', 'breadcrumbspress' ); ?>
			<?php esc_html_e( 'All the settings added by this plugin are limited to WordPress options table.', 'breadcrumbspress' ); ?>
            <br/><br/>
			<?php esc_html_e( 'Deletion operations are not reversible, and it is highly recommended to create database backup before proceeding with this tool.', 'breadcrumbspress' ); ?>
			<?php esc_html_e( 'If you choose to remove plugin settings, once that is done, all settings will be reinitialized to default values if you choose to leave plugin active.', 'breadcrumbspress' ); ?>
        </div>
    </div>

    <div class="d4p-group d4p-group-tools">
        <h3><?php esc_html_e( 'Remove plugin settings', 'breadcrumbspress' ); ?></h3>
        <div class="d4p-group-inner">
            <label>
                <input type="checkbox" class="widefat" name="breadcrumbspresstools[remove][settings]" value="on"/> <?php esc_html_e( 'All Plugin Settings', 'breadcrumbspress' ); ?>
            </label>
        </div>
    </div>

    <div class="d4p-group d4p-group-tools">
        <h3><?php esc_html_e( 'Disable Plugin', 'breadcrumbspress' ); ?></h3>
        <div class="d4p-group-inner">
            <label>
                <input type="checkbox" class="widefat" name="breadcrumbspresstools[remove][disable]" value="on"/> <?php esc_html_e( 'Disable plugin', 'breadcrumbspress' ); ?>
            </label>
        </div>
    </div>

	<?php panel()->include_accessibility_control(); ?>
</div>
