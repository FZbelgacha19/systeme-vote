<?php

$db = 0;
	try {
	$db = new PDO('mysql:host=localhost;dbname=dbsystemvote', 'root', '');
	
	}
	catch(PDOException $e) {
	echo "Impossible de se connecter! \n".$e;
	}


?>