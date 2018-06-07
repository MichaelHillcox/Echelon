<?php

/**
 * Created by: Michael Hillcox.
 * Date:       10/07/2016 - 16:58
 * For:        Legacy-Echelon
 */

 // TODO: Finish implementation

class Pagination
{

	protected $query;
	protected $total;
	protected $database;

	/**
	 * Pagination constructor.
	 * @param $database
	 * @param $query
	 * @param $total
	 */
	function __construct($database, $query, $total ) {
		$this->query = $query;
		$this->total = $total;

		$this->generatePagination();
	}

	/**
	 *	This will handle all of the important stuff.
	 */
	private function generatePagination() {

	}

	/**
	 *
	 */
	public function getPageContent() {

	}

	/**
	 *
	 */
	public function getPages() {

	}

	/**
	 *
	 */
	public function getLazyPages() {

	}
}
