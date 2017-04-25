uuid = argv[1];

this_sess = freeswitch.Session(uuid)

this_sess:preAnswer()
this_sess:execute("playback", "silence_stream://3000");
--this_sess:execute("playback", "C:/Program Files/FreeSWITCH/sounds/en/us/callie/misc/8000/misc-freeswitch_dot_org_more.wav");
this_sess:execute("playback", "D:/xampp/htdocs/wa/prompts/call-kerny-ka-shukriya.wav");
--this_sess:execute("playback", "D:/xampp/htdocs/wa/prompts/serviceOff.wav");
this_sess:execute("hangup","USER_BUSY");
--this_sess:destroy()
stream:write("HUNGUP uuid "..uuid)


