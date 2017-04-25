pathsep = '\\'

--[[ test1.lua
-- Answer call, play a prompt, hangup

-- Set the path separator
pathsep = '\\'

--Answer the call
session:answer()

-- Set a variable that contains the sound prompt to play 
prompt = "ivr" .. pathsep .. "ivr-please_enter_extension_followed_by_pound.wav"

-- Set a variable that contains the invalid message to play
invalid = "ivr" .. pathsep .. "ivr-that_was_an_invalid_entry.wav"

-- Play file and collect digits
-- Variable 'digits' will contain the digits collected
-- Valid input is 3 digits min, 5 digits max
-- Caller presses # (pound or hash) to finish

digits = session:playAndGetDigits(3, 5, 3, 7000, "#", prompt, invalid, "\\d+")

-- Read back digits iterated, then pause
-- "one two three four five"
session:execute("say", "en number iterated " .. digits)
session:sleep(1000)




-- Politely hang up
thankyou = "ivr" .. pathsep .. "ivr-thank_you.wav"
goodbye  = "voicemail" .. pathsep .. "vm-goodbye.wav"
session:streamFile(thankyou)
session:sleep(250)
session:streamFile(goodbye)
session:sleep(250)]]
-- Initiate an outbound call

new_session = freeswitch.Session("sofia/128.237.196.70/1006", session)

-- new_session:get_uuid
-- Check to see if the call was answered

--[[if new_session:ready() then
    
digits = "123";
new_session:execute("say", "en number iterated " .. digits)

else    -- This means the call was not answered ... Check for the reason

    local obCause = new_session:hangupCause()

    freeswitch.consoleLog("info", "new_session:hangupCause() = " .. obCause )

    if ( obCause == "USER_BUSY" ) then              -- SIP 486
       -- For BUSY you may reschedule the call for later
    elseif ( obCause == "NO_ANSWER" ) then
       -- Call them back in an hour
    elseif ( obCause == "ORIGINATOR_CANCEL" ) then   -- SIP 487
       -- May need to check for network congestion or problems
    else
       -- Log these issues
    end
end]]
if new_session:ready() then

-- Set a variable that contains the sound prompt to play 
prompt = "ivr" .. pathsep .. "ivr-please_enter_extension_followed_by_pound.wav"

-- Set a variable that contains the invalid message to play
invalid = "ivr" .. pathsep .. "ivr-that_was_an_invalid_entry.wav"

-- Play file and collect digits
-- Variable 'digits' will contain the digits collected
-- Valid input is 3 digits min, 5 digits max
-- Caller presses # (pound or hash) to finish

digits = new_session:playAndGetDigits(3, 5, 3, 7000, "#", prompt, invalid, "\\d+")

-- Read back digits iterated, then pause
-- "one two three four five"
new_session:execute("say", "en number iterated " .. digits)
new_session:sleep(1000)



--new_session:answer()
digits = "123";
new_session:execute("say", "en number iterated " .. digits)
--session:execute("say", "en number iterated " .. digits)

prompt = "ivr" .. pathsep  .. "ivr-say_name.wav"
new_session:streamFile(prompt)
--session:streamFile(prompt)
end

-- Hangup
new_session:hangup()

--[[Create a string with path and filename of a sound file
prompt = "ivr" .. pathsep  .. "ivr-say_name.wav"
beep = "ivr" .. pathsep .. "ivr-voxeo-beep.wav"

-- Print a log message
freeswitch.consoleLog("INFO","Prompt file is '" .. prompt .. "'\n")

--Play the prompt
session:streamFile(prompt)
session:streamFile(beep)
]]
