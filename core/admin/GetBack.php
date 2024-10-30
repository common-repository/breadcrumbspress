<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Admin;

use Dev4Press\v49\Core\Admin\GetBack as BaseGetBack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GetBack extends BaseGetBack {
	protected function process() {
		parent::process();

		do_action( 'breadcrumbspress_admin_getback_handler', $this->a()->panel );
	}
}
