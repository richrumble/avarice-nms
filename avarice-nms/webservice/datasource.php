<?php

#connect to avarice db
function avariceDBConnect () {
	$dsn = 'mysql:dbname=avarice;host=127.0.0.1';
	$user = 'av-webservice';
	$pass = 'vsmR37Yd8ULPAKQM';

	try {
		$dbh = new PDO($dsn, $user, $pass);
	} catch(PDOException $e) {
		print $e->getMessage();
		exit;
	};
	return $dbh;
};

?>