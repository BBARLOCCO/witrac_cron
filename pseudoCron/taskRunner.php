<?php
	require("./imports.php");
	use Witrac\PseudoCron;
	use Witrac\LogsHandler;
	try{
		$commands = $_SERVER['argv'][2];
		$currentPid = file_get_contents("pseudoCron/handler.pid");
		$pid = $_SERVER['argv'][1];
		if($pid!=$currentPid){
			LogsHandler::log("Cancel $commands execution, another scheduler instance started.");
			die();
		}
		$instance = new PseudoCron(null,$commands);
		$instance->run();
	}catch(\Throwable $e){
		LogsHandler::error("Error running $commands. \r".$e->getMessage());
	}
	
?>