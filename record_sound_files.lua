-- record_sound_files.lua
-- Lets user record one or more sound files
-- Sounds are stored in ${sounds_dir}
-- Input Callback to handle digits dialed during the recording
function onInput (s, type, obj)
    if ( type == 'dtmf' ) then
        return "break"  -- This ends the recording
    end
end
--Answer the call
session:answer()
-- Set the path separator
pathsep = '\\'
-- Set a variable that contains the sound prompt to play
prompt = "ivr" .. pathsep .. "ivr-please_enter_extension_followed_by_pound.wav"
-- Set a variable that contains the invalid message to play
invalid = "ivr" .. pathsep .. "ivr-that_was_an_invalid_entry.wav"
-- Set a flag for continuing or exiting
continue = true
-- Specify what to do when caller dials digits during the recording
session:setInputCallback("onInput", "")
-- Initiate while loop
-- Loop continues until caller hangs up or chooses to exit
while( session:ready() and continue) do
    -- First menu:
    -- 1 = Record
    -- 2 = Exit
    digits = session:playAndGetDigits(1, 1, 3, 7000, "#", "phrase:record_greeting_or_exit", invalid, "\\d{1}")
    if (digits == "2") then
        continue = false
        freeswitch.consoleLog("INFO","Preparing to exit...\n")
    else
        -- Collect message number from caller
        -- Variable 'digits' will contain the digits collected
        -- Valid input is 3 digits min, 5 digits max
        -- Caller presses # (pound or hash) to finish
        msgnum = session:playAndGetDigits(3, 5, 3, 7000, "#", "phrase:enter_message_number", invalid, "\\d+")
        -- Read back the message number
        session:execute("say", "en number iterated " .. msgnum)
        session:sleep(1000)
        -- New loop: accepted or not
        accepted = false
        while ( not accepted ) do
            -- Record record file
            session:streamFile("phrase:voicemail_record_message")
            -- Play a "bong" tone prior to recording
			session:streamFile("tone_stream://v=-7;%(100,0,941.0,1477.0);v=-7;>=2;+=.1;%(1000, 0, 640)")
            filename = session:getVariable('sounds_dir') .. pathsep .. msgnum .. ".wav"
            session:recordFile(filename,300,100,10)
            -- New loop: Ask caller to listen, accept, or re-record
            listen = true
            while ( listen ) do
                session:streamFile(filename)
                -- Use handy record_file_check macro courtesy of the voicemail module
                local digits = session:playAndGetDigits(1, 1, 2, 4000, "#", "phrase:voicemail_record_file_check:1:2:3", invalid, "\\d{1}")
                if ( digits == "1") then
                    listen = true
                    accepted = false
					 session:execute("sleep","500")
                elseif ( digits == "2" ) then
                    listen = false
                    accepted = true
                    -- Let the caller know that the message is saved
                    -- NOTE: you could put these into a Phrase Macro as well
                    session:streamFile("voicemail/vm-message.wav")
                    session:execute("sleep","100")
                    session:execute("say", "en number iterated " .. msgnum)
                    session:execute("sleep","100")
                    session:streamFile("voicemail/vm-saved.wav")
                    session:execute("sleep","1500")
                elseif ( digits == "3" ) then
                    listen = false
                    accepted = false
                    session:execute("sleep","500")
                end -- if ( digits == "1" )
            end -- while ( listen )
        end -- while ( not accepted )
    end -- if ( digits == "2" )
end -- while ( session:ready() )
-- Let's be polite
thankyou = "ivr" .. pathsep .. "ivr-thank_you.wav"
goodbye  = "voicemail" .. pathsep .. "vm-goodbye.wav"
session:streamFile(thankyou)
session:sleep(250)
session:streamFile(goodbye)
-- Hangup
session:hangup()