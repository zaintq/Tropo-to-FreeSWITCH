uuid = argv[1];

this_sess = freeswitch.Session(uuid)
this_sess:answer()

--file = io.open("logtest123.txt", "a+")
--file:write(uuid.."\n")
--file:close()
stream:write("Answered uuid "..uuid)