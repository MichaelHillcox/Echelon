<?php

/**
 * Created by: Michael Hillcox.
 * Date:       10/07/2016 - 17:17
 * For:        Legacy-Echelon
 */
require_once __DIR__."/../../inc/config.php";

class Database
{
	public $database;
	protected $dbError = false;
	protected $dbType = "mysql";

	function __construct( $dbType = NULL )
	{
		if( !is_null($dbType) )
			$this->dbType = $dbType;

		try {
			$this->connect();
		} catch ( PDOException $e ) {
			$this->dbError = $e;
		}
	}

	private function connect() {
		$this->database = new PDO(
			$this->dbType.":host=".DBL_HOSTNAME.";dbname=".DBL_DB,
			DBL_USERNAME,
			DBL_PASSWORD
		);
	}

	public function hasErrors() {
		if( $this->dbError != false )
			return true;
		return false;
	}

	public function getError() {
		return $this->dbError;
	}
}