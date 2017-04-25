uuid = argv[1];
audio_file = argv[2];

another_session = freeswitch.Session(uuid)
another_session:streamFile("ivr\\ivr-that_was_an_invalid_entry.wav")
another_session:hangup()
stream:write(uuid)