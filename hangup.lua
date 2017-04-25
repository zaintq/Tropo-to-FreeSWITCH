uuid = argv[1];

session = freeswitch.Session(uuid)
session:preAnswer()
--session:execute("playback", "silence_stream://3000");
--session:execute("playback", "D:/xampp/htdocs/wa/prompts/serviceOff.wav");
session:execute("hangup","USER_BUSY");

--file = io.open("logtest123.txt", "a+")
--file:write(uuid.."\n")
--file:close()
stream:write("HUNGUP uuid "..uuid)