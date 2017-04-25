function countSubstring (s1, s2)
    local count = 0
    for eachMatch in s1:gmatch(s2) do count = count + 1 end
    return count
end

i = 0
while i < 1 do

	api = freeswitch.API();
	calls = api:executeString("show calls")
	num_calls = countSubstring(calls,"0428333116")
	call_count_log = "Time : " .. os.date("!%c") .. "  Concurrent Calls : " .. num_calls .. "\n";
	date = os.date("%x")
	date = date:gsub("/", "_")
	--freeswitch.consoleLog("info", "date is: " .. date .. "\n")
	local file = io.open("D:/xampp/htdocs/wa/CLogs/" .. date .. "_polly_2_logs.txt", "a")
	file:write(call_count_log)	
    file:flush()
	file:close()
	freeswitch.msleep(1000);
end

