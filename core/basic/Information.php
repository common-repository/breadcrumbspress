<?php

namespace Dev4Press\Plugin\BreadcrumbsPress\Basic;

use Dev4Press\v49\Core\Plugins\Information as BaseInformation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Information extends BaseInformation {
	public $code = 'breadcrumbspress';

	public $version = '2.3';
	public $build = 40;
	public $edition = 'lite';
	public $status = 'stable';
	public $updated = '2024.06.28';
	public $released = '2021.04.14';

	public $cms = array(
		'wordpress'    => '6.1',
		'classicpress' => '2.0',
	);
}
