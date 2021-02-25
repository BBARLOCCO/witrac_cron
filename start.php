<?php
	require("imports.php");
	use Witrac\PseudoCronHandler;
	use Witrac\Config;
	$handler = new PseudoCronHandler(); 
	$handler->parseCronFile(Config::$cronFile);
	$handler->start();
?>