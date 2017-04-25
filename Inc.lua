pathsep = '\\'

session:answer()

prompt = "ivr" .. pathsep  .. "ivr-say_name.wav"
session:streamFile(prompt)

uuida = session:get_uuid()
session:setAutoHangup(false)

freeswitch.consoleLog("INFO","UUIDA:  " .. uuida .. "\n")
uuida1 = string.sub(uuida,1)
freeswitch.consoleLog("INFO","UUIDA1:  " .. uuida1 .. "\n")

-- web-call.lua
web_url = "http://127.0.0.1/FS_Connect_Inc.php?uuid=" ..uuida1
--web_url = "http://127.0.0.1/FS_Connect_Inc.php"
--web_url = "http://127.0.0.1/test.php"
-- Get a FreeSWITCH API object
api = freeswitch.API()

freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")

raw_data = api:execute("curl", web_url)

freeswitch.consoleLog("INFO","Raw data:\n" .. raw_data .. "\n\n")


-- Hangup
--new_session:hangup()