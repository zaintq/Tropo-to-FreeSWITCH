uuid = argv[1];
another_session = freeswitch.Session(uuid)
callerid = another_session:getVariable("destination_number");

--file = io.open("logtest1.txt", "a+")
--file:write("\n:"..callerid..":\n")
--file:close()
another_session:consoleLog("info", "callerid: " .. callerid .. "\n");
stream:write(callerid);