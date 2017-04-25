uuid = argv[1];
audio_file = argv[2];
audio_file = audio_file:gsub("/", "\\")

this_sess = freeswitch.Session(uuid)
this_sess:streamFile(audio_file)
stream:write("STREAMED "..audio_file.." TO uuid "..uuid)