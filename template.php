<?php
// /////////////////////////
// example of how to get parameters from the URL (AKA query string) in PHP
// and then use these to select the right data from a JSON file, making this a PHP Template
// Gil Barros <gil.barros@formato.com.br>, Feb/2024
// /////////////////////////

$json = file_get_contents('data.json'); // get the external file
$array = json_decode($json, true); // transform JSON format into an Array in PHP

$id = $_GET["id"]; // getting the ID from the URL
if ($id == false) { $id = 0; } // default value for id parameter

$next = $id >= 2 ? 0 : $id+1; // for navigation to next and previous
$prev = $id <= 0 ? 2 : $id-1; // for navigation to next and previous
############################
?>
<!DOCTYPE html>
<html>
<head><title>PHP Template - Project 02, INTD 210, 2025</title></head>
<body style="font-family:sans-serif">
	<h3>The city of <?= $array["cities"][$id]["name"] ?>.</h3>
	<p><?= $array["cities"][$id]["name"] ?>,
	a city in <?= $array["cities"][$id]["country"] ?>,
	has a population of <?= $array["cities"][$id]["population"] ?> people.</p>
	<p><a href="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $prev ?>">Previous page</a> --
	<a href="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $next ?>">Next page</a></p>
<div><pre><?php // for debugging /////////////////////////
// var_dump($_GET);	// uncomment this if needed
// var_dump($id);		// uncomment this if needed
// var_dump($json);  // uncomment this if needed for debugging
// var_dump($array); // uncomment this if needed for debugging
// you can also look at the terminal on the server with the commands:
// tail -F /var/log/apache2/error.log
// tail -F /var/log/apache2/access.log
?></pre></div>
</body>
</html>