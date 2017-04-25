--os.execute("timeout " .. tonumber(2))
--callerid = "0428333116";
callerid = argv[13];
phno = argv[1];
--callerid = argv[2];
deployment=argv[2]
calltype=argv[3]
To_Whom=argv[4]
oreqid=argv[5]
recIDtoPlay=argv[6]
Effect_Chosen=argv[7]
ocallid=argv[8]
ouserid=argv[9]
testcall=argv[10]
ch=argv[11]
app=argv[12]
From=argv[14]
--	freeswitch.consoleLog("INFO","deployment:  " .. deployment .. "\n")
--	freeswitch.consoleLog("INFO","calltype:  " .. calltype .. "\n")
--	freeswitch.consoleLog("INFO","To_Whom:  " .. To_Whom .. "\n")
--	freeswitch.consoleLog("INFO","oreqid:  " .. oreqid .. "\n")
--	freeswitch.consoleLog("INFO","recIDtoPlay:  " .. recIDtoPlay .. "\n")
--	freeswitch.consoleLog("INFO","Effect_Chosen:  " .. Effect_Chosen .. "\n")
--	freeswitch.consoleLog("INFO","ocallid:  " .. ocallid .. "\n")
--	freeswitch.consoleLog("INFO","ouserid:  " .. ouserid .. "\n")
--	freeswitch.consoleLog("INFO","testcall:  " .. testcall .. "\n")
--	freeswitch.consoleLog("INFO","ch:  " .. ch .. "\n")
--	freeswitch.consoleLog("INFO","app:  " .. app .. "\n")
api = freeswitch.API()
--cmd="C:\\Program\ Files\\FreeSWITCH\\fs_cli.exe -x 'show channels'" 	
--hello=api:executeString(web_url)
--freeswitch.consoleLog("info", "HEllo log sss -- "..hello )
hcause = "" 
freeswitch.consoleLog("INFO","caller:  " .. phno .. "--"..To_Whom.."\n")
freeswitch.consoleLog("INFO","callerid passing throw dialplan:  " .. callerid .. "\n")

freeswitch.consoleLog("info", " Call.lua started")


	this_sess = freeswitch.Session("{ignore_early_media=true,setAutoHangup=false,originate_timeout=25,origination_caller_id_number="..callerid.."}sofia/gateway/proxy-301/"..phno)
	--this_sess.setAutoHangup(false)
--03227711771--03214241241



if (this_sess:ready()) then
	--digits = this_sess:read(5, 10, "C:/Program Files/FreeSWITCH/sounds/en/us/callie/misc/8000/misc-freeswitch_dot_org_more.wav", 3000, "#"); 
	--freeswitch.consoleLog("INFO","digitss:  " .. digits .. "\n");
    ---- Do something good here
	uuida = this_sess:get_uuid()
	this_sess:setAutoHangup(false)
--	freeswitch.consoleLog("INFO","UUIDA:  " .. uuida .. "\n")
	uuida1 = string.sub(uuida,1)
	web_url = "bgapi curl http://127.0.0.1/FS/APollyLHR.php?uuid=" ..uuida1.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app.."&From="..From

--	freeswitch.consoleLog("INFO","UUIDA1:  " .. uuida1 .. "\n")
--	if (callerid=="0428333116" or (callerid=="0428333115" and (To_Whom=="5990" or To_Whom=="5972"))) then
--	if (callerid=="0428333116" or callerid=="0428333115" or callerid=="0428333112" or To_Whom=="5990" or To_Whom=="5972") then
--		web_url = "bgapi curl http://127.0.0.1/FS/APollyLHRpolly.php?uuid=" ..uuida1.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app.."&From="..From
--	else
--		web_url = "bgapi curl http://127.0.0.1/FS/APollyLHR.php?uuid=" ..uuida1.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app.."&From="..From
	-- Get a FreeSWITCH API object
--	end
	freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")

	--web_url = "bgapi curl http://127.0.0.1/FS/APollyLHR.php?uuid=" ..uuida1.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app
 	
	api:executeString(web_url)

	--stream:write(uuida1)
	
else    -- This means the call was not answered ... Check for the reason
	obCause = this_sess:hangupCause()
    freeswitch.consoleLog("info", To_Whom.." hangupCause(),"..ouserid..", = " .. obCause )
	web_url = "http://127.0.0.1/FS/APollyLHR.php?error="..obCause.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app

 --   if ( obCause == "USER_BUSY"  or obCause == "CALL_REJECTED") then 
		--SIP 486
--		freeswitch.consoleLog("info", "this_sess:hangupCause() = 1")
       -- stream:write("busy")
--    elseif ( obCause == "ALLOTTED_TIMEOUT" or obCause == "NO_ANSWER" or hcause == 'RECOVERY_ON_TIMER_EXPIRE' ) then   
		-- SIP 487
--		freeswitch.consoleLog("info", "this_sess:hangupCause() = 2")
      -- stream:write("timeout")
--    elseif ( obCause == "NO_ROUTE_DESTINATION" or obCause == "INCOMPATIBLE_DESTINATION" or obCause == "UNALLOCATED_NUMBER" or obCause == "NORMAL_TEMPORARY_FAILURE"  ) then
--		freeswitch.consoleLog("info", "this_sess:hangupCause() = 3")
    	--stream:write("fail")
 --   else
--		freeswitch.consoleLog("info", "this_sess:hangupCause() = " ..obCause)
    	--stream:write("error")
 --   end
	raw_data = api:execute("curl", web_url)    
end
