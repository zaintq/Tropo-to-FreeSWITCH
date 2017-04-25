function make_Terminators_Valid(terminators)
    valid_input = "\\d+"; 
	l=string.len(terminators);
	i=1;
	while i <= l do
		valid_input=valid_input.."|\\"..string.sub(terminators, i, i);
		i=i+1;
	end
	return valid_input
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
		stream:write("+"..terminator_part)
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

prompt = prompt:gsub("\\", "/")
invalid = invalid:gsub("/", "\\")


this_sess = freeswitch.Session(uuid);
--this_sess:consoleLog("info", "invalid: " .. invalid .. "\n");
--this_sess:consoleLog("info", "min_digits: " .. min_digits .. "\n");
--this_sess:consoleLog("info", "max_digits: " .. max_digits .. "\n");
--this_sess:consoleLog("info", "max_attempts: " .. max_attempts .. "\n");

if invalid == "nothing" then
	invalid = "";
end
--to handle case of different terminators and as freeswitch method of set and get temrinator as a variable doesnt work for all min_digits and max_digits
--treating terminator as a valid digit and then after taking input differentiating it from [1-9] and treating it as a terminator and returning it as a terminator

if tonumber(max_digits) < 2 then
	valid_input=make_Terminators_Valid(terminators);
	digits = this_sess:playAndGetDigits(min_digits,max_digits,max_attempts,timeout,"", prompt, invalid,valid_input)
	write_To_Steam(terminators,digits);
	
else
	this_sess:setVariable("read_terminator_used", "-");
	digits = this_sess:playAndGetDigits(min_digits, max_digits, max_attempts, timeout,terminators, prompt, invalid, valid_input);
	terminator = this_sess:getVariable("read_terminator_used");
	stream:write("_"..digits.." "..terminator)
end

--if on stream, you read frojm script, you get space ' ' on first character it means there is only digit  that you expect with no info of terminator part
--if on stream, you read frojm script, you get space '-' on first character it means there is only terminator that you expect with no info of digit part
--if on stream, you read frojm script, you get space '_' on first character it means there is a combination of digits followed by terminator