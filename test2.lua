pathsep = '\\'
--new_session = freeswitch.Session(argv[1])
--new_session:originate(session, "{origination_caller_id_number=1234567}sofia/128.2.211.183/1006", 30)
new_session = freeswitch.Session("{origination_caller_id_number=1234567}sofia/128.2.211.183/1006", session)

new_session:setAutoHangup(false)
uuida = new_session:get_uuid()

another_session = freeswitch.Session(uuida)
another_session:streamFile("ivr\\ivr-that_was_an_invalid_entry.wav")

stream:write(tostring(uuida))