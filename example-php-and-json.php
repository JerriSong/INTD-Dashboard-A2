<?php
// /////////////////////////
// example of how to use content from an external JSON file in PHP
// Gil Barros <gil.barros@formato.com.br>, Feb/2025
// /////////////////////////

$json = file_get_contents('data.json'); // get the external file
$array = json_decode($json, true); // transform JSON format into an Array in PHP

############################
?>
<!DOCTYPE html>
<html>
<head><title>Example of PHP code using data from external file in JSON</title></head>
<body style="font-family:sans-serif">
	<h3>Cities of America.</h3>
	<h4>Name: <?= $array["cities"][0]["name"] ?></h4>
	<p>This city is in <?= $array["cities"][0]["country"] ?> and it's population is <?= $array["cities"][0]["population"] ?>.</p>

	<h4>Name: <?= $array["cities"][1]["name"] ?></h4>
	<p>This city is in <?= $array["cities"][1]["country"] ?> and it's population is <?= $array["cities"][1]["population"] ?>.</p>

	<h4>Name: <?= $array["cities"][2]["name"] ?></h4>
	<p>This city is in <?= $array["cities"][2]["country"] ?> and it's population is <?= $array["cities"][2]["population"] ?>.</p>

<div><pre><?php // for debugging /////////////////////////
// var_dump($json);  // uncomment this if needed for debugging
// var_dump($array); // uncomment this if needed for debugging
// you can also look at the terminal on the server with the commands:
// tail -F /var/log/apache2/error.log
// tail -F /var/log/apache2/access.log
?></pre></div>
</body>
</html>
