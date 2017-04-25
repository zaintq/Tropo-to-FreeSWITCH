-- answer the call
--session:answer();
uuid = argv[1];

this_sess = freeswitch.Session(uuid)

-- sleep a second


-- play a file
this_sess:execute("playback", "silence_stream://3000");
this_sess:execute("playback", "C:/Program Files/FreeSWITCH/sounds/en/us/callie/misc/8000/misc-freeswitch_dot_org_more.wav");



-- hangup
this_sess:hangup();