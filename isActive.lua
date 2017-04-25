uuid = argv[1];

this_sess = freeswitch.Session(uuid)

--file = io.open("logtest123.txt", "a+")

if this_sess:ready() then
	--file:write("Call Status: Connected.\n")
	stream:write(" true")
else
	--file:write("Call Status: Disconnected.\n")
	stream:write(" false")
end

--file:close()