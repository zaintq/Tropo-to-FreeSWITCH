

function onInputCBF(s, _type, obj, arg)
    local k, v = nil, nil
    local _debug = true
    if _debug then
        for k, v in pairs(obj) do
            print(string.format('obj k-> %s v->%s\n', tostring(k), tostring(v)))
        end
        if _type == 'table' then
            for k, v in pairs(_type) do
                print(string.format('_type k-> %s v->%s\n', tostring(k), tostring(v)))
            end
        end
        print(string.format('\n(%s == dtmf) and (obj.digit [%s])\n', _type, obj.digit))
    end
    if (_type == "dtmf") then
        if obj.digit == "#" then
            return ''
        else
            return ''
        end
    end
end

-- answer the call
session:answer();

-- sleep a second
session:sleep(1000);

print("getting ready to record the audio prompt");

-- play a file


print("played the audio prompt");

-- hangup

 

 
while session:ready() do

    session:setInputCallback('onInputCBF', '');
    session:recordFile("E:/Freeswitch/freeswitch-1.4.20/Win32/Debug/recordings/new.wav", 15, 15, 5);
    session:consoleLog("info", "prompt: nope" .. "\n");
end



