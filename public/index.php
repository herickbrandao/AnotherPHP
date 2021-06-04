<?php namespace Another\System;

/*
 * AnotherPHP v1.1
 * See more: https://github.com/herickbrandao/AnotherPHP
 */
$min = '8.0.0';
if( version_compare(phpversion(), $min) === -1 ) {
	echo "<strong>Fatal error:</strong> The PHP version must be {$min} or higher!";
	exit;
}
unset($min);

require_once("../system/Starter.php");
Starter::run();
