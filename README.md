# witrack_cron
Cron replacement in pure php

## Instructions
Check Config.php for config options. You can set automatic emails on/off, custom "cron shortcuts", set up php executable file and cronFile location. 

### Start
In order to start the scheduler run php start.php on your terminal. 
### Stop
In order to stop the scheduler run php stop.php on your terminal. 

### Single instance
The process will automatically detect if its already running (the last started one will remain working while the old one will auto-terminate).

### Automatic cron file changes detection
The process will automatically detect if the cron file changes.

## NOTES
This is a simple scheduler, doesn't keep track of every execution in a DB or file neither it saves "last execution" for each cron file line. 