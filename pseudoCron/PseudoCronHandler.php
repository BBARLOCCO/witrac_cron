<?php
namespace Witrac;

use Witrac\PseudoCron;
use Witrac\LogsHandler;
use Witrac\Config;
class PseudoCronHandler{
	private $pseudoCrons = Array();
	private $pid;
	private $path;
	private $cronFileLastUpdate;
	function parseCronFile($path){
		if(file_exists($path)){
			$this->pseudoCrons =[];
			$this->path = $path;
			$file = fopen($path, "r");
			if ($file) {
				while (($line = fgets($file)) !== false) {
					$segments = explode(" ",$line);
					if(sizeof($segments) > 5 || isset(Config::$shortcuts[$segments[0]])){
						if( isset(Config::$shortcuts[$segments[0]])){
							$periodicity = Config::$shortcuts[$segments[0]];
							$i=1;
						}else{
							$periodicity = Array($segments[0],$segments[1],$segments[2],$segments[3],$segments[4]);
							$i=5;
						}				
						$commands = Array();
						for($i;$i<sizeof($segments);$i++){
							$commands[] = $segments[$i];
						}
						$commands = implode(" ",$commands);
						$this->pseudoCrons[] = new PseudoCron($periodicity,$commands);
					}else{
						LogsHandler::error("Wrong format on cron line.");
					}
				}				
				fclose($file);
				$this->cronFileLastUpdate = filemtime($this->path);
			} else {
				LogsHandler::error("Something wen't wrong opening cron file.");
			} 
		}else{
			$error = "Cron file doesn't exists.";
			LogsHandler::error($error);
		}
	}
	function stop(){
		file_put_contents("pseudoCron/handler.pid",-1);
	}
	function start(){
		$this->pid = getmypid();
		echo "Scheduller instance $this->pid started \n";
		$initialModifiedTime = filemtime($this->path);
		file_put_contents("pseudoCron/handler.pid",$this->pid);
		$startTime = time();
		$second = date("s",$startTime);
		$sleep = 60;
		if($second >= 55){
			$sleep=65;
			$startTime = time();
			$second = date("s",$startTime);
		}
		$currentPid = $this->pid;
		while($this->pid==$currentPid){
			sleep($sleep);
			$currentPid = file_get_contents("pseudoCron/handler.pid");
			if($this->pid!=$currentPid){
				echo "Scheduller instance $this->pid stopped \n";
				die();
			}
			$time = time();
			$currentSecond = date("s",$time);
			$sleep=60-($currentSecond-$second);
			$this->checkCronFileUpdates();
			foreach($this->pseudoCrons as $pseudoCron){
				if($pseudoCron->shouldRun($time)){
					$pseudoCron->runInBackground($this->pid);
				}
			}
			
		}
	}
	function checkCronFileUpdates(){
		clearstatcache();
		if(!isset($this->cronFileLastUpdate)){
			$this->cronFileLastUpdate = filemtime($this->path);
			LogsHandler::log(" setting last update $this->cronFileLastUpdate ");
		}else{
			$currentLastUpdate = filemtime($this->path);
			LogsHandler::log(" checking last update $currentLastUpdate ");
			if($this->cronFileLastUpdate != $currentLastUpdate){
				echo "Updating cron file content \n";
				$this->parseCronFile($this->path);
				$this->cronFileLastUpdate = $currentLastUpdate;
			}
		}
	}
}

?>