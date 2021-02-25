<?php 
	namespace Witrac;
	use Witrac\Config;
	class LogsHandler{
		static function error($error,$sendEmail=false){
			$date = date("d-m-Y h:i:s",time());
			$fp = fopen('./files/logs/errors.log', 'a');
			fwrite($fp, "\r$date \r $error\r");
			fclose($fp);
			if($sendEmail){
				mail(Config::$adminEmail,"Scheduller automatic ERROR message",$error);
			}
		}
		static function log($log,$sendEmail=false){
			$date = date("d-m-Y h:i:s",time());
			$fp = fopen('./files/logs/logs.log', 'a');
			fwrite($fp, "\r$date \r $log\r");
			fclose($fp);
			if($sendEmail){
				mail(Config::$adminEmail,"Scheduller automatic log message",$log);
			}
		}
	}
?>