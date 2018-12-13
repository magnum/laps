<?php
declare( strict_types=1 );

namespace Rarst\Laps\Event;

/**
 * Events for Hybrid Core based themes
 *
 * @link http://themehybrid.com/hybrid-core
 */
class Hybrid_Events implements Hook_Event_Config_Interface {

	/**
	 * @return array
	 */
	public function get_events(): array {

		$events = [];

		if ( ! \function_exists( 'hybrid_get_prefix' ) ) {
			return $events;
		}

		/** @var string $prefix */
		$prefix = hybrid_get_prefix();

		$events = [
			[ 'Header', 'theme', "{$prefix}_before_header", "{$prefix}_after_header" ],
		];

		/** @var array $sidebars */
		$sidebars = get_theme_support( 'hybrid-core-sidebars' );

		if ( ! empty( $sidebars[0] ) ) {
			/** @var string $sidebar */
			foreach ( $sidebars[0] as $sidebar ) {
				$events[] = [
					"Sidebar ({$sidebar})",
					'theme',
					"{$prefix}_before_{$sidebar}",
					"{$prefix}_after_{$sidebar}",
				];
			}
		}

		return $events;
	}
}
