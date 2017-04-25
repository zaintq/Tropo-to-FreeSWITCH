uuid = argv[1];
digits = argv[2];

this_sess = freeswitch.Session(uuid)
this_sess:execute("say", "en number iterated "..digits)

stream:write("Played "..digits.." TO uuid "..uuid)