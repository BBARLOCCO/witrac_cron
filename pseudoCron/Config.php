<?php 
	namespace Witrac;
	class Config{
		static $phpPath = "C:/xampp/php/php.exe";
		static $cronFile = "./files/cron/cronFile";
		static $shorcuts = [
							"@yearly"=>["0","0" ,"1", "1","*"],
							"@monthly"=>["0","0" ,"1", "*","*"],
							"@weekly"=>["0","0" ,"*", "*","0"],
							"@daily"=>["0","0" ,"*", "*","*"],
							"@hourly"=>["0","*" ,"*", "*","*"],
							"@everyCicle"=>["*","*" ,"*", "*","*"],
						];
		static $adminEmail = "admin@email.com";
		static $shouldSendEmails = false;
	}
?>