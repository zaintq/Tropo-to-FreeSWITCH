 

--*****************************************************************************************************************************************************************
uuid = argv[1];
prompt = argv[2];
file_name = argv[3];
max_len_secs = argv[4];
silence_secs = argv[5];
silence_threshold = 100;

this_sess = freeswitch.Session(uuid); 


active = 0
while this_sess:ready() do
	this_sess:consoleLog("info", "uuid: " .. uuid .. "\n");
	prompt = prompt:gsub("\\", "/")
	file_name = file_name:gsub("_", "/")
	this_sess:consoleLog("info", "prompt: " .. prompt .. "\n");
	this_sess:consoleLog("info", "file_name: " .. file_name .. "\n");
	this_sess:consoleLog("info", "max_len_secs: " .. max_len_secs .. "\n");
	this_sess:consoleLog("info", "silence_threshold: " .. silence_threshold .. "\n");
	this_sess:consoleLog("info", "silence_secs: " .. silence_secs .. "\n");
	-- Play the prompt
	this_sess:streamFile(prompt);
	-- Play a "bong" tone prior to recording
	this_sess:streamFile("tone_stream://v=-7;%(100,0,941.0,1477.0);v=-7;>=2;+=.1;%(1000, 0, 640)");
	--setting terminator # pound key
	this_sess:execute("set","playback_terminators=#");
	this_sess:execute("record",file_name.." "..max_len_secs.." "..
	"".." ".."");
	stream:write(file_name);
	value = this_sess:getState()
	this_sess:consoleLog("info", "value: " .. value );
	active = 1
	break

end



 
