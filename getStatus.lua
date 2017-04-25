pathsep = '\\'
session:setAutoHangup(false)

--ph = argv[1]; 
--freeswitch.bridge("{sip_auth_username=1000, sip_auth_password=Conakry2014}sofia/gateway/goipamerbaby/" .. ph);

var1 = session:getVariable("last_bridge_hangup_cause")
freeswitch.consoleLog("INFO","Bridge Hangup Cause:  " .. var1 .. "\n")

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