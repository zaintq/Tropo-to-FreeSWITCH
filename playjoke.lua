uuid = argv[1];
audio_file = argv[2];
times = argv[3];

this_sess = freeswitch.Session(uuid)

this_sess:consoleLog("info", "uuid: " .. uuid .. "\n");
this_sess:consoleLog("info", "audio_file: " .. audio_file .. "\n");
this_sess:consoleLog("info", "times: " .. times .. "\n");


audio_file = audio_file:gsub("/", "\\")

if times == "1" then
	this_sess:preAnswer()
end

if times ~= "4" then

	this_sess:execute("playback", "silence_stream://1000");
	--this_sess:execute("playback", "C:/Program Files/FreeSWITCH/sounds/en/us/callie/misc/8000/misc-freeswitch_dot_org_more.wav");
	this_sess:execute("playback", audio_file);
	--this_sess:execute("playback", "D:/xampp/htdocs/wa/prompts/serviceOff.wav");
	stream:write("Userplayed : "..audio_file)
else
	this_sess:execute("hangup","USER_BUSY");
	--this_sess:destroy()
	stream:write("HUNGUP uuid "..uuid)
end

