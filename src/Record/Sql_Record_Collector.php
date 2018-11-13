<?php

namespace Rarst\Laps\Record;

use Rarst\Laps\Formatter\Backtrace_Formatter;

/**
 * Processes SQL events from data logged by wpdb.
 */
class Sql_Record_Collector implements Record_Collector_Interface {

	/** @var array $query_starts Log of query start times. */
	protected $query_starts = [];

	/** @var Backtrace_Formatter $formatter */
	protected $formatter;

	/**
	 * Sets up the query start time log.
	 */
	public function __construct() {

		$this->formatter = new Backtrace_Formatter();

		if ( $this->is_savequeries() ) {
			add_filter( 'query', [ $this, 'query' ], 20 ); // TODO refactor when core provides time start data in 5.0.
		}
	}

	/**
	 * Capture SQL queries start times
	 *
	 * @param string $query SQL query.
	 *
	 * @return string
	 */
	public function query( $query ) {

		global $wpdb;

		if ( empty( $this->query_starts ) && ! empty( $wpdb->queries ) ) {
			$this->query_starts[ count( $wpdb->queries ) ] = microtime( true );
		} else {
			$this->query_starts[] = microtime( true );
		}

		return $query;
	}

	/**
	 * @return Record[]
	 */
	public function get_records() {

		if ( ! $this->is_savequeries() ) {
			return [];
		}

		global $wpdb;

		return array_map( [ $this, 'transform' ], array_keys( $wpdb->queries ), $wpdb->queries );
	}

	/**
	 * @return bool
	 */
	protected function is_savequeries() {

		return defined( 'SAVEQUERIES' ) && SAVEQUERIES;
	}

	/**
	 * Transform query data, captured by core, into a Record.
	 *
	 * @param int   $key        Query key in captured data.
	 * @param array $query_data Array of captured query data.
	 *
	 * @return Record
	 */
	protected function transform( $key, $query_data ) {

		static $last_query_end = 0;

		list( $sql, $duration, $caller ) = $query_data;

		$query_start = isset( $this->query_starts[ $key ] ) ? $this->query_starts[ $key ] : $last_query_end;
		$sql         = trim( $sql );
		$category    = 'sql-read';
		if ( 0 === stripos( $sql, 'INSERT' ) || 0 === stripos( $sql, 'UPDATE' ) ) {
			$category = 'sql-write';
		}
		$last_query_end = $query_start + $duration;

		$desc_duration = round( $duration * 1000 );
		$backtrace     = $this->formatter->format( $caller );
		$description   = $sql . ' – ' . $desc_duration . 'ms<hr />' . implode( ' → ', $backtrace );

		return new Record( $sql, $query_start, $duration, $description, $category );
	}
}
