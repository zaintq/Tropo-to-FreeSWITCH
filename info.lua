
api = freeswitch.API();
sofia = api:executeString("show channels count ");

--freeswitch.consoleLog("info", "info return"..sofia)
stream:write(sofia)


