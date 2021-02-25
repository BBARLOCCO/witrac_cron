<?php
	namespace Witrac;
	use Witrac\LogsHandler;
	use Witrac\Config;
	class PseudoCron{
		private $periodicity;
		private $commands;
		function __construct($periodicity,$commands){
			$this->periodicity = $periodicity;
			$this->commands = $commands;
		}
		function shouldRun($when){
			
			if($this->periodicity!=null && sizeof($this->periodicity)>4){
				$cronStyleDate = explode("/",date("i/h/j/m/w",$when));
				$shouldRun = true;
				for($i=0;$i<5;$i++){
					if($this->periodicity[$i]!="*"){
						if(str_contains($this->periodicity[$i],"/")){
							$divisor = explode("/",$this->periodicity[$i])[1];
							if($cronStyleDate[$i]%$divisor != 0){
								$shouldRun = false;
							}
						}else if(str_contains($this->periodicity[$i],"-")){
							$fromTo = explode("-",$this->periodicity[$i]);
							$timeAt = [];
							for($z=$fromTo[0];$z < $fromTo[1];$z++){
								$timeAt[] = $z;
							}
							if(!in_array($cronStyleDate[$i],$timeAt)){
								$shouldRun = false;
							}
						}else if(str_contains($this->periodicity[$i],",")){
							$timeAt = explode("-",$this->periodicity[$i]);
							if(!in_array($cronStyleDate[$i],$timeAt)){
								$shouldRun = false;
							}						
						}else if($this->periodicity[$i] != $cronStyleDate[$i]){
							$shouldRun = false;
						}
					}
				}
				LogsHandler::log("Testing periodicity ".json_encode($this->periodicity). " on date:".json_encode($cronStyleDate)." result: $shouldRun");
				return $shouldRun;
			}else{
				return false;
			}
			return true;
		}
		function runInBackground($pid){
			$cmd = Config::$phpPath." ./pseudoCron/taskRunner.php $pid \"$this->commands\" ";
			if (substr(php_uname(), 0, 7) == "Windows"){
				pclose(popen("start /B ". $cmd, "r")); 
			}
			else {
				exec($cmd . " > /dev/null &");  
			}
			LogsHandler::log("Command $this->commands launched on background.");
		}
		function run(){
			$logs = [];
			$errors = [];
			try{
				$logs[] = "Schedulled command: $this->commands";
				$op = shell_exec($this->commands." 2>&1");
				$logs[] = "Result: $op";
			}catch(\Throwable $e){
				$errors[]="Error Running command: $this->commands";
				$errors[]=$e->getMessage();
			}finally{
				if(sizeof($logs)>0){
					LogsHandler::log(implode("\r",$logs),Config::$shouldSendEmails);
				}
				if(sizeof($errors)>0){
					LogsHandler::error(implode("\r",$errors),Config::$shouldSendEmails);
				}
			}
		}
	}
?>