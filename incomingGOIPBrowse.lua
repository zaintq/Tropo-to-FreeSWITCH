pathsep = '\\'


--session:sleep(5000)
--freeswitch.consoleLog("INFO","Sleep1.\n")
--session:sleep(20000)
--freeswitch.consoleLog("INFO","Sleep2.\n")
--session:sleep(20000)
--freeswitch.consoleLog("INFO","Sleep3.\n")
--session:sleep(20000)
--freeswitch.consoleLog("INFO","Sleep4.\n")
--session:sleep(20000)
--freeswitch.consoleLog("INFO","Sleep5.\n")


--session:answer()

--prompt = "ivr" .. pathsep  .. "ivr-say_name.wav"
--session:streamFile(prompt)

--prompt = "c:\\xampp\\htdocs\\a\\prompts\\Urd_prompts\\Salaam.wav"
--session:streamFile(prompt)
phno = session:getVariable("ani")
extension = session:getVariable("1")

--uuida = session:get_uuid()
--session:setAutoHangup(false)

--freeswitch.consoleLog("INFO","UUIDA:  " .. uuida .. "\n")
--uuida1 = string.sub(uuida,1)
--freeswitch.consoleLog("INFO","UUIDA1:  " .. uuida1 .. "\n")

--session:streamFile("c:/xampp/htdocs/earlymedia.wav")
--freeswitch.consoleLog("INFO","Just Played audio file\n")


freeswitch.consoleLog("INFO","Phone Number:" .. phno .. "\n")

lengthOfPh = string.len(phno)
if lengthOfPh > 9 then
	phno = phno
else
	phno = "224" .. phno
 end

session:execute("curl", "http://128.2.208.191/wa/DBScripts/createMissedCall.php?ph=" .. phno .. "&ch=GuinPiglet&syslang=GuinFrench&msglang=GuinFrench&iccid=892246501056013445FF")
freeswitch.consoleLog("INFO","Missed Call Registered.\n")
session:hangup();
freeswitch.consoleLog("INFO","HANGUP EXECUTED...\n")

-- web-call.lua
 -- web_url = "http://127.0.0.1/FS/APolly2CLUS-TFS.php?uuid=" ..uuida1
-- web_url = "http://127.0.0.1/FS/APolly2QxDkCL-TFS.php?uuid=" ..uuida1
-- web_url = "http://127.0.0.1/FS/TFS.php?uuid=" ..uuida1
-- Get a FreeSWITCH API object
-- api = freeswitch.API()

--freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")

--raw_data = api:execute("curl", web_url)
--raw_data = session:execute("curl", web_url) --also works fine. No need for api
-- digits = session:read(5, 7, "c:\\xampp\\htdocs\\a\\prompts\\Urd_prompts\\Salaam.wav", 30000, "#");

--freeswitch.consoleLog("INFO","Raw data:\n" .. raw_data .. "\n\n")