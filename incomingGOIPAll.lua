pathsep = '\\'

phno = session:getVariable("ani")
extension = argv[1]

freeswitch.consoleLog("INFO","Incoming call with Phone Number:" .. phno .. " on extension " .. extension .. "\n")

lengthOfPh = string.len(phno)
if lengthOfPh > 9 then
	phno = phno
else
	phno = "224" .. phno
 end

--session:execute("curl", "http://128.2.208.191/wa/DBScripts/createMissedCall.php?ph=" .. phno .. "&ch=GuinPiglet&syslang=GuinFrench&msglang=GuinFrench&iccid=" .. extension)
session:execute("curl", "http://128.2.208.191/wa/DBScripts/createMissedCall.php?ph=" .. phno .. "&ch=GuinPiglet&iccid=" .. extension)
freeswitch.consoleLog("INFO","Missed Call Registered.\n")
session:hangup();
freeswitch.consoleLog("INFO","HANGUP EXECUTED...\n")