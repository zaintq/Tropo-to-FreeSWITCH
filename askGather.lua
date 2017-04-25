function make_Valid_Input(valid_input)
	if valid_input == "d" then
		valid_input = "\\d+"; 
    end
    return valid_input
end

function make_Terminators_Valid(valid_input,terminators)
	if terminators ~= "@" then
		l=string.len(terminators);
		i=1;
		valids = valid_input;
		while i <= l do
			valids=valids.."|\\"..string.sub(terminators, i, i);
			i=i+1;
		end
		return valids
	else
		return valid_input
	end
end

function check_If_Digit_Terminator(terminators,Digit)
	l=string.len(terminators);
	i=1;
	while i <= l do
		if string.sub(terminators, i, i) == Digit then
			return true
		end
		i=i+1;
	end
	return false
end

function get_Digits(terminators,Digits)
	l=string.len(Digits);
	i=1;
	digits_part="";
	if check_If_Digit_Terminator(terminators,string.sub(Digits, 1, 1)) == false then
		digits_part=digits_part..string.sub(Digits, 1, 1);
	end
	return digits_part
end

function get_terminator(terminators,Digits)
	l=string.len(Digits);
	i=1;
	terminator_part="";
	if check_If_Digit_Terminator(terminators,string.sub(Digits, 1, 1)) == true then
		terminator_part=digits_part..string.sub(Digits, 1, 1);
	end
	return terminator_part
end


function write_To_Steam(terminators,Digits)
	digits_part=get_Digits(terminators,Digits)
	terminator_part=get_terminator(terminators,Digits)
	if terminator_part == "" then
		stream:write(" "..digits_part)
	else
		stream:write("-"..terminator_part)
	end
end

function isvalid(digits,valid_input_mx_1)
	if string.find(valid_input_mx_1, "\\d+") then
		if string.find("0123456789",digits) then
		 	return true
		else
			if string.find(valid_input_mx_1,digits) then
		 		return true
			end
		end	
	else 
		 if string.find(valid_input_mx_1,digits) then
		 		return true
			end
	end
end
--*****************************************************************************************************************************************************************
uuid = argv[1];
prompt = argv[2];
invalid = argv[3];
min_digits = argv[4];
max_digits = argv[5];
max_attempts = argv[6];
timeout = argv[7];
terminators = argv[8];
valid_input= argv[9];
timeout_prompt = argv[10];
digit_timeout = argv[11];

prompt = prompt:gsub("\\", "/")
invalid = invalid:gsub("/", "\\")
timeout_prompt = timeout_prompt:gsub("/", "\\")


this_sess = freeswitch.Session(uuid);
--this_sess:consoleLog("info", "*******Start*******: ".. "\n");
--this_sess:consoleLog("info", "prompt: " .. prompt .. "\n");
--this_sess:consoleLog("info", "invalid: " .. invalid .. "\n");
--this_sess:consoleLog("info", "min_digits: " .. min_digits .. "\n");
--this_sess:consoleLog("info", "max_digits: " .. max_digits .. "\n");
--this_sess:consoleLog("info", "max_attempts: " .. max_attempts .. "\n");
--this_sess:consoleLog("info", "terminators: " .. terminators .. "\n");
--this_sess:consoleLog("info", "valid_input: " .. valid_input .. "\n");
--this_sess:consoleLog("info", "timeout_prompt: " .. timeout_prompt .. "\n");
--this_sess:consoleLog("info", "*******end*******: ".. "\n");
valid_input=make_Valid_Input(valid_input);
valid_input_mx_1 = make_Terminators_Valid(valid_input,terminators);

--this_sess:consoleLog("info", "valid_input: " .. valid_input .. "\n");
--this_sess:consoleLog("info", "valid_input_mx_1: " .. valid_input_mx_1 .. "\n");
--this_sess:consoleLog("info", "2: " .. "\n");

--if this_sess:ready() then
--	this_sess:consoleLog("info", "ready 1: " .. "\n");
--else
--	this_sess:consoleLog("info", "ready 2: " .. "\n");
--end

value = this_sess:getState();
--	this_sess:consoleLog("info", "state value: " .. value );

if timeout_prompt ~= "-" then
	i = 0;
	digits = "";
--	this_sess:consoleLog("info", "max_attempts1: " .. max_attempts .. "\n");
	while i < tonumber(max_attempts) do
		if tonumber(max_digits) < 2 then
--			this_sess:consoleLog("info", "max_digits1: " .. max_digits .. "\n");
			if i>0 then
				this_sess:streamFile(timeout_prompt);
			end 
			digits = this_sess:playAndGetDigits(min_digits, max_digits, 1, timeout,"", prompt, "", valid_input_mx_1);
			if digits ~= "" then
				if isvalid(digits,valid_input_mx_1) == true then
					write_To_Steam(terminators,digits);
					break
				else
					this_sess:streamFile(timeout_prompt);
				end		
			end
		else
--			this_sess:consoleLog("info", "max_digits2: " .. max_digits .. "\n");
			if i>0 then
				this_sess:streamFile(timeout_prompt);
			end 
--			this_sess:consoleLog("info", "terminators: " .. terminators .. "\n");
--			this_sess:consoleLog("info", "valid_input: " .. valid_input .. "\n");
--			this_sess:consoleLog("info", "min_digits: " .. min_digits .. "\n");
			if terminators ~= "@" then
				digits = this_sess:playAndGetDigits(min_digits, max_digits, 1, timeout,terminators, prompt, "", valid_input);
			else
				digits = this_sess:playAndGetDigits(min_digits, max_digits, 1, timeout,"", prompt, "", valid_input);
			end
			if digits ~= "" then
				stream:write("_"..digits);
				break
			end

		end
		i = i+1;
	end
	
else
--	this_sess:consoleLog("info", "max_attempts2: " .. max_attempts .. "\n");
	if tonumber(max_digits) < 2 then
		digits = this_sess:playAndGetDigits(min_digits, max_digits, max_attempts, timeout,"", prompt, invalid, valid_input_mx_1)
		write_To_Steam(terminators,digits);
	else
		this_sess:setVariable("read_terminator_used", "-");
		digits = this_sess:playAndGetDigits(min_digits, max_digits, max_attempts, timeout,terminators, prompt, invalid, valid_input);
		terminator = this_sess:getVariable("read_terminator_used");
		stream:write("_"..digits);
--		this_sess:consoleLog("info", "number endereD: " .. valid_input .. "\n");
	end
end




--to handle case of different terminators and as freeswitch method of set and get temrinator as a variable doesnt work for all min_digits and max_digits
--treating terminator as a valid digit and then after taking input differentiating it from [1-9] and treating it as a terminator and returning it as a terminator


--if on stream, you read frojm script, you get space ' ' on first character it means there is only digit  that you expect with no info of terminator part
--if on stream, you read frojm script, you get space '-' on first character it means there is only terminator that you expect with no info of digit part
--if on stream, you read frojm script, you get space '_' on first character it means there is a combination of digits followed by terminator