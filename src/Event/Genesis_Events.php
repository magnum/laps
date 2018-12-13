<?php
declare( strict_types=1 );

namespace Rarst\Laps\Event;

/**
 * Events for Genesis Framework based themes
 *
 * @link http://my.studiopress.com/themes/genesis/
 */
class Genesis_Events implements Hook_Event_Config_Interface {

	/**
	 * @return array
	 */
	public function get_events(): array {

		return \function_exists( 'genesis' ) ? [
			[ 'Header', 'theme', 'genesis_before_header', 'genesis_after_header' ],
			[ 'Sidebar', 'theme', 'genesis_before_sidebar_widget_area', 'genesis_after_sidebar_widget_area' ],
			[
				'Sidebar (alternate)',
				'theme',
				'genesis_before_sidebar_alt_widget_area',
				'genesis_after_sidebar_alt_widget_area',
			],
		] : [];
	}
}
