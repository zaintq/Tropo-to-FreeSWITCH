
-- web-call.lua
web_url = argv[1];
-- Get a FreeSWITCH API object
api = freeswitch.API();

freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")

raw_data = api:execute("curl", web_url)

freeswitch.consoleLog("INFO","Raw data:\n" .. raw_data .. "\n\n")

stream:write(raw_data)
