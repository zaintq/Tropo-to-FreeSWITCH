os.execute("timeout " .. tonumber(5))
callerid = "0428333115";
phno = argv[1];
hcause = "" 
freeswitch.consoleLog("INFO","caller:  " .. phno .. "\n")
freeswitch.consoleLog("info", "Reject.lua ended,,and Call.lua started")

	this_sess = freeswitch.Session("{ignore_early_media=true,originate_timeout=30,origination_caller_id_number="..callerid.."}sofia/gateway/proxy-301/"..phno)
	
--03227711771--03214241241


if (this_sess:ready()) then
	--digits = this_sess:read(5, 10, "C:/Program Files/FreeSWITCH/sounds/en/us/callie/misc/8000/misc-freeswitch_dot_org_more.wav", 3000, "#"); 
	--freeswitch.consoleLog("INFO","digitss:  " .. digits .. "\n");
    ---- Do something good here
	uuida = this_sess:get_uuid()
	this_sess:setAutoHangup(false)
	freeswitch.consoleLog("INFO","UUIDA:  " .. uuida .. "\n")
	uuida1 = string.sub(uuida,1)
	freeswitch.consoleLog("INFO","UUIDA1:  " .. uuida1 .. "\n")
	stream:write(uuida1)
	web_url = "http://127.0.0.1/FS/APollyLHR.php?uuid=" ..uuida1.."&userid="..phno
	--Get a FreeSWITCH API object
    api = freeswitch.API()
	freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")
	raw_data = api:execute("curl", web_url)

	
	
else    -- This means the call was not answered ... Check for the reason
	obCause = this_sess:hangupCause()
    freeswitch.consoleLog("info", "this_sess:hangupCause() = " .. obCause )

    if ( obCause == "USER_BUSY"  or obCause == "CALL_REJECTED") then 
		--SIP 486
		freeswitch.consoleLog("info", "this_sess:hangupCause() = 1")
       --stream:write("busy")
    elseif ( obCause == "ALLOTTED_TIMEOUT" or obCause == "NO_ANSWER" or hcause == 'RECOVERY_ON_TIMER_EXPIRE' ) then   
		-- SIP 487
		freeswitch.consoleLog("info", "this_sess:hangupCause() = 2")
       --stream:write("timeout")
    elseif ( obCause == "NO_ROUTE_DESTINATION" or obCause == "INCOMPATIBLE_DESTINATION" or obCause == "UNALLOCATED_NUMBER" or obCause == "NORMAL_TEMPORARY_FAILURE"  ) then
		freeswitch.consoleLog("info", "this_sess:hangupCause() = 3")
    		--stream:write("fail")
    else
		freeswitch.consoleLog("info", "this_sess:hangupCause() = " ..obCause)
    	--stream:write("fail")
       
    end
end
