<?php
	define('USERNAME', 'denlogue');
	define('PASS', 't4skmur1');
	define('DSN', 'mysql:host=localhost; dbname=denlogue; charset=utf8');

	function db_connect(){
		$dbh = new PDO(DSN, USERNAME, PASS);
		return $dbh;
	}
?>