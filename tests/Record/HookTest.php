<?php
declare( strict_types=1 );

namespace Rarst\Laps\Tests\Record;

use Rarst\Laps\Event\Core_Events;
use Rarst\Laps\Record\Hook_Record_Collector;
use Rarst\Laps\Tests\LapsTestCase;
use Symfony\Component\Stopwatch\Stopwatch;

class HookTest extends LapsTestCase {

	public function testCollector() {

		$stopwatch = new Stopwatch();
		$collector = new Hook_Record_Collector( $stopwatch, [ 'core' => new Core_Events() ] );

		$this->assertTrue( $stopwatch->isStarted( 'Plugins Load' ) );
		$this->assertTrue( has_action( 'after_setup_theme', [ $collector, 'after_setup_theme' ] ) );

		$this->assertTrue( has_action( 'plugins_loaded', 'function ($input)' ) );

		$collector->after_setup_theme();
		$stopwatch->start( 'Toolbar' );
		$collector->get_records();

		$this->assertFalse( $stopwatch->isStarted( 'Toolbar' ) );
	}
}
