pathsep = '\\'

--session:preAnswer()

--prompt = "ivr" .. pathsep  .. "ivr-say_name.wav"
--session:streamFile(prompt)

--prompt = "c:\\xampp\\htdocs\\a\\prompts\\Urd_prompts\\Salaam.wav"
--session:streamFile(prompt)

uuida = session:get_uuid()
session:setAutoHangup(false)

--freeswitch.consoleLog("INFO","UUIDA:  " .. uuida .. "\n")
uuida1 = string.sub(uuida,1)
freeswitch.consoleLog("INFO","UUIDA1:  " .. uuida1 .. "\n")
session:preAnswer()
session:execute("playback", "silence_stream://3000");
--session:execute("playback", "D:/xampp/htdocs/wa/prompts/serviceOff.wav");
--session:execute("hangup","USER_BUSY");

web_url = "http://127.0.0.1/FS/APollyLHRClean.php?uuid=" ..uuida1
-- Get a FreeSWITCH API object
api = freeswitch.API()

freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")

raw_data = api:execute("curl",web_url)
freeswitch.consoleLog("INFO","Url :\n" .. web_url .. "\n\n")

freeswitch.consoleLog("INFO","Raw data:\n" .. raw_data .. "\n\n")