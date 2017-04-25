


function countSubstring (s1, s2)
    local count = 0
    for eachMatch in s1:gmatch(s2) do count = count + 1 end
    return count
end
 

api = freeswitch.API();
calls = api:executeString("show calls")
num_calls = countSubstring(calls,"0428333112")
--freeswitch.consoleLog("info", "num calls is: " .. num_calls .. "\n")


stream:write(" " .. num_calls .. "\n");
--api = freeswitch.API();
--num_channels = api:executeString("show calls count like 0428333112");
--digits = api:execute("regex", num_channels  .. "|/([0-9]*)/|$1");
--freeswitch.consoleLog("info", "num calls is: " .. digits .. "\n")


--stream:write(" " .. digits .. "\n");

