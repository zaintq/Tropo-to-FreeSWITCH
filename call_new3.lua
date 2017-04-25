callerid = argv[13];
phno = argv[1];
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

api = freeswitch.API()

hcause = "" 
freeswitch.consoleLog("INFO","caller:  " .. phno .. "--"..To_Whom.."\n")
freeswitch.consoleLog("INFO","callerid passing throw dialplan:  " .. callerid .. "\n")

freeswitch.consoleLog("info", " Call.lua started")

if string.find(To_Whom,"*") then
	web_url = "http://127.0.0.1/FS/APollyLHRClean3.php?error=".."NO_ROUTE_DESTINATION".."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app	 	
    raw_data = api:execute("curl", web_url) 
else
	this_sess = freeswitch.Session("{ignore_early_media=true,setAutoHangup=false,originate_timeout=25,origination_caller_id_number="..callerid.."}sofia/gateway/proxy-301/"..phno)
    if (this_sess:ready()) then

		uuida = this_sess:get_uuid()
		this_sess:setAutoHangup(false)
		uuida1 = string.sub(uuida,1)
		web_url = "bgapi curl http://127.0.0.1/FS/APollyLHRClean3.php?uuid=" ..uuida1.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app.."&From="..From

		freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")
	 	
		api:executeString(web_url)

	
	else
		obCause = this_sess:hangupCause()
	    freeswitch.consoleLog("info", To_Whom.." hangupCause(),"..ouserid..", = " .. obCause )
		web_url = "http://127.0.0.1/FS/APollyLHRClean3.php?error="..obCause.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app

		raw_data = api:execute("curl", web_url)    
	
	end
end	