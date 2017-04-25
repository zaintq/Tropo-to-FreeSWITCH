<?php
_log("%%$$~Call Start~$$&&");
set_time_limit(1200);
// Explicitly setting time zone
$time_zone="Asia/Calcutta";
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);
// SIP Address Components
$IPaddress = "10.64.1.39";
$udpport = "5060";
// Base directories
$base_dir = "http://128.2.208.191/a/";
$scripts_dir = $base_dir."Scripts/";
$praat_dir = "http://128.2.208.191/a/Praat/";
$DB_dir = "http://128.2.208.191/a/DBScripts/";
$logFilePath = "e:\\xampp\\htdocs\\a\\LogFiles\\";
$prompts_dir = "";
$AlreadygivenFeedback = "FALSE";
$AlreadyHeardJobs = "FALSE";
$thiscallStatus = "Answered";// Temporary assignment
$Country="IN";
$fh = "";	// temprary variable to act as a place holder for file handle
$BabaJobs = "true";
//////////////////////////////////////////////////////////////////////////////////////
$calltype = 'Unsubsidized';
$currentStatus = 'InProgress';	
$oreqid = "0";
// Getting the User's Phone Number from the sip header
$useridUnclean = $currentCall->getHeader("from");
$Useless = array("<", ">", "@", ";", "_");
$Clean = str_replace($Useless, "&", $useridUnclean);
$colon = array(":");
$equals = str_replace($colon, "=", $Clean);
parse_str($equals);
$userid = $sip;  // Phone number acquired
//&&$$** ph encoding
$useridUnEnc = $userid;	// added
$userid = PhToKeyAndStore($useridUnEnc, 0);	// added
//&&$$** Now $userid contains the encoded user id

$callid = makeNewCall($calltype, $oreqid, $userid, $currentStatus);	// Create a row in the call table
writeToLog($callid, $fh, "start", "Call Type: ".$calltype.", Phone Number: ".$userid.", Originating Request ID: ".$oreqid.", Call ID: ".$callid.", Country: ".$Country);

//----- Global Voice Prompts---------
$Another_Friend = "Anotherfriend.wav";
$Ask_for_forwarding2 = "CorrAskforforwarding2.wav";
$BadChoiceKeys = "Wrongbutton.wav";
$Bye = "Bye.wav";
$ContactDetails = "ContactDetails.wav";
$delAlertMsg = "delAlertMsg.wav";
$Forward_confirmation = "Forward_confirmation.wav";
$Forward_confirmation2 = "Forward_confirmation2.wav";
$Friend_name = "Friendname.wav";
$Friend_no_prompt = "Friendnoprompt.wav";
$Friend_no_repeat = "Friendnorepeat.wav";
$Greetings = "Greetings.wav";
$Greetings2 = "Greetings2.wav";
$GreetingsUS = "msgNameIs.wav";
$Here_it_is = "Hereitis.wav";
$InformedConsent = "InformedConsent.wav";
$J_CallLater = "J_CallLater.wav";
$J_ConfirmSend = "J_ConfirmSend.wav";
$J_Greetings = "J_Greetings.wav";
$J_Greetings2 = "J_Greetings2.wav";
$J_Here_it_is = "J_Hereitis.wav";
$J_Menu = "J_Menu.wav";
$J_Next = "J_Next.wav";
$J_NoMoreAds = "J_NoMoreAds.wav";
$J_PlayingFromBeg = "J_PlayingFromBeg.wav";
$J_Prev = "J_Prev.wav";
$J_Skip = "J_Skip.wav";
$J_SolicitFeedback = "J_SolicitFeedback.wav";
$J_SPrompt = "J_SPrompt.wav";
$J_YouCan = "J_YouCan.wav";
$msg1 = "msg1.wav";
$msg2 = "msg2.wav";
$msg3 = "msg3.wav";
$msg4 = "msg4.wav";
$msg5 = "msg5.wav";
$msg5p = "msg5p.wav";
$msgFirst = "msgFirst.wav";
$msgNameIs = "msgNameIs.wav";
$msgNext = "msgNext.wav";
$msgNoMore = "msgNoMore.wav";
$msgNowOrLater = "msgNowOrLater.wav";
$Number_confirm = "Numberconfirm.wav";
$Processing_plz_wait = "Processingplzwait.wav";
$Prompt_for_speaking = "Promptforspeaking.wav";
$Quota = "Quota2.wav";
$Ready_for_effects = "Readyforeffects.wav";
$Record_your_feedback = "Recordyourfeedback.wav";
$Record_your_name = "Recordyourname.wav";
$Salaam = "Salaam.wav";
$SeedGreetings = "SeedGreetings.wav";
$SenderPh = "senderPh.wav";
$Thanks_for_Feedback = "ThanksforFeedback.wav";
$TimeOutKeys = "Nobutton.wav";
$Try_again = "Tryagain.wav";
$What_to_do2 = "Whattodo3.wav";
$What_to_do3 = "Whattodo3.wav";
$YouCan = "YouCan.wav";

// Generic Code, shared by all three types of requests
$CallTableCutoff = getCallTableCutoff(5);
$ReqTableCutoff = getReqTableCutoff(5);

writeToLog($callid, $fh, "stats", "Call Table cutoff found at:".$CallTableCutoff);
writeToLog($callid, $fh, "stats", "Req Table cutoff found at:".$ReqTableCutoff);


/*if(!file_exists($logFilePath."\\".date('Y'))){ 
	mkdir($logFilePath."\\".date('Y'));
} 
if(!file_exists($logFilePath."\\".date('Y')."\\".date('m'))){ 
	mkdir($logFilePath."\\".date('Y')."\\".date('m'));
} 
if(!file_exists($logFilePath."\\".date('Y')."\\".date('m')."\\".date('d'))){ 
	mkdir($logFilePath."\\".date('Y')."\\".date('m')."\\".date('d'));
}*/

$time_stamp = date('Y-m-d_H-i-s');
$logFile = $logFilePath."\\".date('Y')."\\".date('m')."\\".date('d')."\\log_".$time_stamp."_".$callid.".txt";	// Give a caller ID based name
//$fh = fopen($logFile, 'a');

if($Country == "PK"){
	// Urdu
	$prompts_dir = "http://hosting.tropo.com/37743/www/audio/prompts/Urd_prompts/";
}
else if($Country == "US"){ //English prompts
	$prompts_dir = "http://hosting.tropo.com/37743/www/audio/prompts/Eng_prompts/";
}
else{ //Kannada prompts
	$prompts_dir = "http://hosting.tropo.com/37743/www/audio/prompts/Kan_prompts/";
}

// Processing to find if message(s) are waiting for this user
// Message related globals
$anyMessages = "false";
$phno = "";
$oreqid = "";
$ocallid = "";
$effectno = "";
$recIDtoPlay = "";
$ouserid = "";
$messages = my_messages($userid);	// how many and which messages are waiting
$pmsgs = explode("|", $messages); 	// parsed messages
$countMsgs = count($pmsgs);
$msgIndex = 0;
$Age = searchPh($userid);			// How many times has he called us before? Unsub or CMB, that was answered
$lastMarked = 0;
	
writeToLog($callid, $fh, "", "Age of the user: ".$Age."Number of messages: ".$countMsgs.", Raw string: ".$messages);

if($countMsgs > 1 && $Age <= 2){		// There are messages waiting. For inexperienced users, we make the choice.
	$anyMessages = "TRUE";
	answer();
	$Salaam = $prompts_dir."Salaam.wav";
	say($Salaam);
	deliverMsg();
}
else{
	// Initiating main body
	$anyMessages = "false";
	answer();
	$Salaam = $prompts_dir."Salaam.wav";
	say($Salaam);
	answerCall($callid, 'FALSE', 'TRUE'); //$reply = "FALSE", $Isthis the firstmenu = "TRUE"
}
// Finalize and close
Prehangup();
// ************************** Call Error Handlers *****************************
function callFailureFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	$thiscallStatus = "Failed";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($callid, $fh, "CallFailure", "Call Failed.");
	
	Prehangup();
}
function errorFCN($callid, $status){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	$thiscallStatus = "Error";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($callid, $fh, "CallError", "Call encountered an error.");
	
	Prehangup();
}
function timeOutFCN($callid, $status){
	global $fh;
	global $oreqid;
	global $callid;
	global $thiscallStatus;
	$thiscallStatus = "TimedOut";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($callid, $fh, "CallTimeOut", "Call Timed Out.");
	
	Prehangup();
}
function keystimeOutFCN($event){
	global $prompts_dir;
	global $TimeOutKeys;
	say($prompts_dir.$TimeOutKeys);
}
function keysbadChoiceFCN($event){
	global $prompts_dir;
	global $BadChoiceKeys;
	say($prompts_dir.$BadChoiceKeys);
}
// End -- Call Error Handlers
///////////////////////////////////////////////////////
function deliverMsg() {
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;
	
	global $msg1;
	global $msg2;
	global $msg3;
	global $msg4;
	global $msg5;
	global $msg5p;
	global $msgFirst;
	global $msgNext;
	global $msgNameIs;
	global $msgNoMore;
	global $GreetingsUS;
	global $Processing_plz_wait;
	global $YouCan;
	global $ContactDetails;
	global $What_to_do2;			
	global $SenderPh;
	global $Forward_confirmation2;
	global $Here_it_is;
	global $BadChoiceKeys;
	global $Bye;
	global $TimeOutKeys;

	global $logFilePath;
	global $fh;
	global $calltype;
	global $callid;
	global $oreqid;
	global $userid;
	global $ouserid;
	global $recIDtoPlay;
	global $effectno;
	global $ocallid;
	global $anyMessages;
	global $countMsgs;
	global $pmsgs; 	// parsed messages
	global $msgIndex;
	global $Age;
	global $lastMarked;
	global $BabaJobs;
	
	$currmsg = explode(" ", $pmsgs[$msgIndex]);
	$oreqid = $currmsg[0]; 
	$recIDtoPlay = $currmsg[2]; 
	$effectno = $currmsg[3]; 
	$ocallid = $currmsg[4]; 
	$ouserid = $currmsg[5];
	//&&$$** Decode the Sender's phone number
	$ouserid = KeyToPh($ouserid);	// added
	//&&$$**
		
	if($countMsgs == 2){	// one
		say($prompts_dir.$msg1);//"Polly has been given one message for you.");
	}
	else if($countMsgs == 3){	// two
		say($prompts_dir.$msg2);//"Polly has been given two messages for you. First Message:");
		say($prompts_dir.$msgFirst);
	}
	else if($countMsgs == 4){	// 3
		say($prompts_dir.$msg3);//"Polly has been given three messages for you. First Message:");
		say($prompts_dir.$msgFirst);
	}
	else if($countMsgs == 5){	// 4
		say($prompts_dir.$msg4);//"Polly has been given four messages for you. First Message:");
		say($prompts_dir.$msgFirst);
	}
	else if($countMsgs == 6){	// 5
		say($prompts_dir.$msg5);//"Polly has been given five messages for you. First Message:");
		say($prompts_dir.$msgFirst);
	}
	else if($countMsgs > 6){	// >5
		say($prompts_dir.$msg5p);//"Polly has been given more than five messages for you. First Message:");
		say($prompts_dir.$msgFirst);
	}
	
	if(($Age < 2 || $calltype == 'SystemMessage') AND $calltype <> "Unsubsidized"){
		writeToLog($callid, $fh, "", "Sending SMS to ".$userid." because the age of the user is ".$Age);
		sendSMS($userid, 'Del');// Send a SMS to the receiver
	}
	
	/*writeToLog($callid, $fh, "", "Starting Call Recording");
	startCallRecording($scripts_dir."process_callRec.php?callid=$callid");//process_callRec*/
	/*$status = "InProgress";
	updateCallStatus($callid, $status);
	$status = "fulfilled";
	updateRequestStatus($oreqid, $status);*/
	
	writeToLog($callid, $fh, "CallAnswered", "Call Answered.");
	writeToLog($callid, $fh, "say", "Playing greetings for message delivery");
	$result = ask($prompts_dir.$GreetingsUS,//"The sender of this message has told Polly of his name as:",
		array(
						"choices" => "UR(1,UR), EN(2, EN), KA(3, KA)", 
						"mode" => 'dtmf',
						"repeat" => 0,
						"bargein" => true,
						"timeout" => 0,
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);
	if($result->value=='UR'){
		say("Switching to Urdu.");
		$Country = 'PK';
		$prompts_dir = $base_dir."prompts/Urd_prompts/";

		writeToLog($callid, $fh, "", "User pressed 1 (cheat key)");
		writeToLog($callid, $fh, "", "Country is set to: ".$Country);
	}
	else if($result->value=='EN'){
		say("Switching to English.");
		$Country = 'US';
		$prompts_dir = $base_dir."prompts/Eng_prompts/";
	
		writeToLog($callid, $fh, "", "User pressed 2 (cheat key)");
		writeToLog($callid, $fh, "", "Country is set to: ".$Country);
	}
	else if($result->value=='KA'){
		say("Switching to Kannada.");
		$Country = 'IN';
		$prompts_dir = $base_dir."prompts/Kan_prompts/";

		writeToLog($callid, $fh, "", "User pressed 3 (cheat key)");
		writeToLog($callid, $fh, "", "Country is set to: ".$Country);
	}
	else if($result->name == 'choice')
	{
		writeToLog($callid, $fh, "p-barge-in", "User pressed a button to skip ".$prompts_dir.$GreetingsUS.".");
	}
	
	$Name = $praat_dir."UserNames/UserName-".$ocallid.".wav";
	say($Name);
	say($prompts_dir.$Here_it_is);//"And here is the message..."
	// ********** Say out the message: 
	$repeat = "TRUE";
	$iter = 0;
	while($repeat == "TRUE"){
		$Path = $praat_dir."/ModifiedRecordings/".$effectno."-s-".$recIDtoPlay.".wav";
		if(!file_exists($Path)){
			say($prompts_dir.$Processing_plz_wait);//"Processing. Please wait!";
			writeToLog($callid, $fh, "playerr", "File cannot be found. Played the prompt to wait.");
			$reps = 0;
			while(!file_exists($Path) && $reps<10){
				writeToLog($callid, $fh, "say", "Playing the clock as the user waits.");
				say($prompts_dir.$Processing_plz_wait);//"Processing. Please wait!";
				say($praat_dir."clock_fast.wav");
				$reps = $reps+1;
			}
		}
		else{
			say($praat_dir."clock_fast.wav");
		}

		/* Commented this out when we started discouraging toll-free calls
		if($iter == 1 || $iter==3){
			$presult = ask($prompts_dir.$YouCan,// Play the message file. Interrupt with any key
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($presult->name == 'choice')
			{
				writeToLog($callid, $fh, "barge-in", "User pressed ".$presult->value." to skip ".$prompts_dir.$YouCan.".");
			}
			$presult = ask($prompts_dir.$ContactDetails,// Play the message file. Interrupt with any key
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($presult->name == 'choice')
			{
				writeToLog($callid, $fh, "barge-in", "User pressed ".$presult->value." to skip ".$prompts_dir.$ContactDetails.".");
			}
		}
		$iter = $iter+1;*/
		
		$presult = ask($Path,// Play the message file. Interrupt with any key
		array(
				"choices" => "[1 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => true,
				"repeat" => 0,
				"timeout"=> 0.1,
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);
		if ($presult->name == 'choice')
		{
			writeToLog($callid, $fh, "r-barge-in", "User pressed ".$presult->value." to skip ".$Path.".");
		}
		
		if($lastMarked != $oreqid){
			updateRequestStatus($oreqid, "CallinFulfilled");
			MsgsPlayedinCallID($oreqid, $callid);
			$lastMarked = $oreqid;
		}
		
		writeToLog($callid, $fh, "not", "Played the message with request id: ".$oreqid." in call id: ".$callid.".");
		writeToLog($callid, $fh, "say", "Now playing message related options.");
		$repeat = "FALSE";	// If not action taken then loop will break
		
		if($countMsgs > 2){
			$What_to_do2 = "Whattodo4Msg.wav";	//"to listen to the next message, press 6. to listen to the sender's phone number, press 0. to repeat, press 1. to forward to friends, press 2. To reply, press 3. or to record a new message, press 4";
		}

		$result = ask($prompts_dir.$What_to_do2,
		array(
				"choices" => "[1 DIGITS]",	//Using the [1 DI1GITS] to allow tracking wrong keys"rpt(1,rpt), fwd(2, fwd), cont(3,cont), feedback(8, feedback), quit(9, quit)", 
				"mode" => 'dtmf',
				"bargein" => true,
				"repeat" => 2,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "keystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);
		// ***** These first two if's seem like a good idea. If they work as expected, add them to the rest of the places too
		if ($result->name == 'timeout' || $result->name == 'hangup') //User did not respond or call received by machine
		{
			// ***** do something here
		}
		else if ($result->name == 'badChoice')
		{
			// ***** do something here
		}
		else if ($result->name == 'choice')
		{
			if ($result->value == 0)//'sender's phone number'
			{
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Sender's Phone number).");
				say($prompts_dir.$SenderPh);
				$num12 = str_split($ouserid);
				for($index1 = 0; $index1 < count($num12); $index1+=1)
				{
					$fileName = $num12[$index1].'.wav';
					$numpath = $prompts_dir.$fileName;
					say($numpath);
				}
				$repeat = "TRUE";
			}
			else if ($result->value == 1)//'rpt'
			{
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Repeat).");
				$repeat = "TRUE";
			}
			else if ($result->value==2)//'fwd'
			{
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Forward).");
				$repeat = "TRUE";
				sendToFriends($callid, $recIDtoPlay, $userid, $effectno, $Path);
				say($prompts_dir.$Forward_confirmation2);
			}
			else if($result->value==3)//'reply'
			{
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Reply).");
				$repeat = "TRUE";
				$reply = "TRUE";				
				answerCall($callid, $reply, 'FALSE');
			}
			else if($result->value==4)//'new'
			{
				$countMsgs--;		// The current message should be marked read and count of messages decremented now
				$msgIndex++;
				//updateRequestStatus($oreqid, "CallinFulfilled");
				if($countMsgs <= 2){
					$What_to_do2 = "Whattodo3.wav";	//"to listen to the next message, press 6. to listen to the sender's phone number, press 0. to repeat, press 1. to forward to friends, press 2. To reply, press 3. or to record a new message, press 4";
				}
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (New Recording).");
				$repeat = "TRUE";
				$reply = "FALSE";				
				answerCall($callid, $reply, 'FALSE');
			}
			else if($result->value==5)//'Jobs')
			{
				$countMsgs--;		// The current message should be marked read and count of messages decremented now
				$msgIndex++;
				//updateRequestStatus($oreqid, "CallinFulfilled");
				if($countMsgs <= 2){
					$What_to_do2 = "Whattodo3.wav";	//"to listen to the next message, press 6. to listen to the sender's phone number, press 0. to repeat, press 1. to forward to friends, press 2. To reply, press 3. or to record a new message, press 4";
				}
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Jobs).");
				if($BabaJobs == 'true'){	
					say($prompts_dir.$Processing_plz_wait);
					if($Country == 'US'){
						transfer(array("sip:9990051311@sbc-external.orl.voxeo.net"), array(
						   "playvalue" => "http://www.phono.com/audio/holdmusic.mp3",
						   "terminator" => "*",
						   "onTimeout" => "timeoutFCNTr",
						   "onSuccess" => "successFCNTr",
							'headers' => array('pollycallid' => $callid)
						   )
						);
					}
					else if($Country == 'IN'){
						transfer(array("sip:9990067118@sbc-external.orl.voxeo.net"), array(
						   "playvalue" => "http://www.phono.com/audio/holdmusic.mp3",
						   "terminator" => "*",
						   "onTimeout" => "timeoutFCNTr",
						   "onSuccess" => "successFCNTr",
							'headers' => array('pollycallid' => $callid)
						   )
						);
					}
					writeToLog($callid, $fh, "", "User returned from BabaJobs IVR.");
				}
				else{
					answerCall_J($callid, 'FALSE', 'FALSE');
				}
				$repeat = "TRUE";
				if($count == 4){
					$count = -1;
				}
				continue;
			}
			else if ($result->value == 6 && $countMsgs > 2)//'Next Message'
			{
				$countMsgs--;		// The current message should be marked read and count of messages decremented now
				$msgIndex++;
				//updateRequestStatus($oreqid, "CallinFulfilled");
				if($countMsgs <= 2){
					$What_to_do2 = "Whattodo3.wav";	//"to listen to the next message, press 6. to listen to the sender's phone number, press 0. to repeat, press 1. to forward to friends, press 2. To reply, press 3. or to record a new message, press 4";
				}
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Next Message).");

				$currmsg = explode(" ", $pmsgs[$msgIndex]);
				//$msgIndex++;
				$oreqid = $currmsg[0]; 
				$recIDtoPlay = $currmsg[2]; 
				$effectno = $currmsg[3]; 
				$ocallid = $currmsg[4]; 
				$ouserid = $currmsg[5];
				//&&$$** Decode the Sender's phone number
				$ouserid = KeyToPh($ouserid);	// added
				//&&$$**
	
				$GreetingsUS = "Greetings2.wav";
				say($prompts_dir.$msgNext);//"Next Message:");
				$result = ask($prompts_dir.$msgNameIs,//"The sender of this message has told Polly of his name as:",
				array(
								"choices" => "UR(1,UR), EN(2, EN), KA(3, KA)", 
								"mode" => 'dtmf',
								"repeat" => 0,
								"bargein" => true,
								"timeout" => 0,
								"onHangup" => create_function("$event", "Prehangup()")
							)
						);
				if($result->value=='UR'){
					say("Switching to Urdu.");
					$Country = 'PK';
					$prompts_dir = $base_dir."prompts/Urd_prompts/";

					writeToLog($callid, $fh, "", "User pressed 1 (cheat key)");
					writeToLog($callid, $fh, "", "Country is set to: ".$Country);
				}
				else if($result->value=='EN'){
					say("Switching to English.");
					$Country = 'US';
					$prompts_dir = $base_dir."prompts/Eng_prompts/";
				
					writeToLog($callid, $fh, "", "User pressed 2 (cheat key)");
					writeToLog($callid, $fh, "", "Country is set to: ".$Country);
				}
				else if($result->value=='KA'){
					say("Switching to Kannada.");
					$Country = 'IN';
					$prompts_dir = $base_dir."prompts/Kan_prompts/";

					writeToLog($callid, $fh, "", "User pressed 3 (cheat key)");
					writeToLog($callid, $fh, "", "Country is set to: ".$Country);
				}
				else if($result->name == 'choice')
				{
					writeToLog($callid, $fh, "p-barge-in", "User pressed a button to skip ".$prompts_dir.$msgNameIs.".");
				}
				
				$Name = $praat_dir."UserNames/UserName-".$ocallid.".wav";
				say($Name);
				say($prompts_dir.$Here_it_is);//"And here is the message..."
				$repeat = "TRUE";
			}
			else if ($result->value == 6 && $countMsgs <= 2)//'Next Message'
			{
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Next Message) but there are no more messages.");
				$repeat = "TRUE";
				say($prompts_dir.$msgNoMore);//"You have no more messages.");	// **&&
				
			}
			else if ($result->value==0)//'quit')
			{
				$countMsgs = 1;		// The current message should be marked read and count of messages decremented now
				//updateRequestStatus($oreqid, "CallinFulfilled");
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (Quit).");
				$repeat = "FALSE";
			}
			else{
				writeToLog($callid, $fh, "", "User pressed ".$result->value." (wrong key).");
				$repeat = "TRUE";
				say($prompts_dir.$BadChoiceKeys);
			}
		}
		else{
			writeToLog($callid, $fh, "", "User did not press any key.");
			$repeat = "FALSE";
		}
	}// end of main while loop while($repeat == "TRUE")
	say($prompts_dir.$Bye);//"Thanks for calling. Good Bye."
	Prehangup();
	writeToLog($callid, $fh, "", "Hanging up.");
}
///////////////////////////////////////////////////////
function answerCall($callid, $reply, $isthisthefirstMenu)//answerCall()
{
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;

	global $msg1;
	global $msg2;
	global $msg3;
	global $msg4;
	global $msg5;
	global $msg5p;
	global $msgFirst;
	global $msgNowOrLater;
	global $SeedGreetings;
	global $Salaam;
	global $ContactDetails;
	global $Greetings;
	global $Ask_for_forwarding2;
	global $InformedConsent;
	global $Prompt_for_speaking;
	global $Processing_plz_wait;
	global $Ready_for_effects;
	global $Forward_confirmation;
	global $Forward_confirmation2;
	global $msgNoMore;
	global $Record_your_feedback;
	global $Thanks_for_Feedback;
	global $BadChoiceKeys;
	global $TimeOutKeys;
	global $Bye;

	global $logFilePath;
	global $fh;
	global $calltype;
	global $callid;
	global $oreqid;
	global $userid;
	global $TreatmentGroup;
	global $Age;
	global $ouserid;
	global $recIDtoPlay;
	global $effectno;
	global $ocallid;
	global $anyMessages;
	global $countMsgs;
	global $pmsgs; 	// parsed messages
	global $msgIndex;
	global $BabaJobs;
	global $recID;
		
	writeToLog($callid, $fh, "stats", "Inside answerCall(). Calltype: ".$calltype);
	
	if($isthisthefirstMenu == 'TRUE' && $countMsgs > 1){
		if($countMsgs == 2){	// one
			say($prompts_dir.$msg1);//"Polly has been given one message for you.");
		}
		else if($countMsgs == 3){	// two
			say($prompts_dir.$msg2);//"Polly has been given two messages for you. First Message:");
		}
		else if($countMsgs == 4){	// 3
			say($prompts_dir.$msg3);//"Polly has been given three messages for you. First Message:");
		}
		else if($countMsgs == 5){	// 4
			say($prompts_dir.$msg4);//"Polly has been given four messages for you. First Message:");
		}
		else if($countMsgs == 6){	// 5
			say($prompts_dir.$msg5);//"Polly has been given five messages for you. First Message:");
		}
		else if($countMsgs > 6){	// >5
			say($prompts_dir.$msg5p);//"Polly has been given more than five messages for you. First Message:");
		}
		$result = ask($prompts_dir.$msgNowOrLater,//"To listen to the messages, press 1. Otherwise press 2.", 
				array(
						"choices" => "msg(1,msg)", 
						"mode" => 'dtmf',
						"repeat" => 0,
						"bargein" => true,
						"timeout" => 5,
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);
		if($result->value=='msg'){
			writeToLog($callid, $fh, "", "User pressed 1 to indicate that he wants to listen to messages.");
			deliverMsg();
		}
		else if ($result->name == 'choice'){
			writeToLog($callid, $fh, "m-other-choice", "User pressed some button other than 1 at ".$prompts_dir.$msgNowOrLater."."); // may or may not be a bargein
		}
	}
	
	if($calltype == 'Call-me-back' || $calltype == 'SystemMessage'){
		$status = "InProgress";
		updateCallStatus($callid, $status);
		$status = "fulfilled";
		updateRequestStatus($oreqid, $status);
		writeToLog($callid, $fh, "CallAnswered", "Call Answered.");
	}
		
	if($calltype != 'Delivery'){
		/*writeToLog($callid, $fh, "", "Starting Call Recording");
		startCallRecording($scripts_dir."process_callRec.php?callid=$callid");//process_callRec*/
		
		writeToLog($callid, $fh, "", "Playing Greetings");
		
		if($calltype == 'SystemMessage'){
			say($prompts_dir.$SeedGreetings);
			say($prompts_dir.$ContactDetails);
			sendSMS($userid, 'Del');// Send a SMS to the receiver
		}

		//say($prompts_dir.$Greetings);
		$result = ask($prompts_dir.$Greetings, //say("Hi! Now you can do all sorts of cool things with your voice... Check it out!");
				array(
						"choices" => "UR(1,UR), EN(2, EN), KA(3, KA)", 
						"mode" => 'dtmf',
						"repeat" => 0,
						"bargein" => true,
						"timeout" => 0.1,
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);
		if($result->value=='UR'){
			say("Switching to Urdu.");
			$Country = 'PK';
			$prompts_dir = $base_dir."prompts/Urd_prompts/";

			writeToLog($callid, $fh, "", "User pressed 1 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->value=='EN'){
			say("Switching to English.");
			$Country = 'US';
			$prompts_dir = $base_dir."prompts/Eng_prompts/";
		
			writeToLog($callid, $fh, "", "User pressed 2 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->value=='KA'){
			say("Switching to Kannada.");
			$Country = 'IN';
			$prompts_dir = $base_dir."prompts/Kan_prompts/";

			writeToLog($callid, $fh, "", "User pressed 3 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->name == 'choice')
		{
			writeToLog($callid, $fh, "p-barge-in", "User pressed a button to skip ".$prompts_dir.$Greetings.".");
		}		
	}
	if($reply == "TRUE" && $countMsgs <= 1){
		$Ask_for_forwarding2 = "CorrAskforforwarding2-Reply.wav";
	}
	else if($reply == "TRUE" && $countMsgs > 1){
		$Ask_for_forwarding2 = "CorrAskforforwarding2-ReplyMsg.wav";
	}
	else if($countMsgs > 1){
		$Ask_for_forwarding2 = "CorrAskforforwarding2Msg.wav";
	}
	else{
		$Ask_for_forwarding2 = "CorrAskforforwarding2.wav";
	}
	//$Age = searchPh($userid);// added global
	if($Age > 4){			// Caller's age is more than 5 User Initiated calls...
		if($countMsgs > 1){
			$Ask_for_forwarding2 = "CorrAskforforwardingandFeedbackMsg.wav";
			if($reply == "TRUE"){
				$Ask_for_forwarding2 = "CorrAskforforwardingandFeedback-ReplyMsg.wav";
			}
		}
		else{
			$Ask_for_forwarding2 = "CorrAskforforwardingandFeedback.wav";
			if($reply == "TRUE"){
				$Ask_for_forwarding2 = "CorrAskforforwardingandFeedback-Reply.wav";
			}
		}
	}
	_log("about to play informed consent.");
	writeToLog($callid, $fh, "", "Now playing Informed consent.");
	say($prompts_dir.$InformedConsent);
	
	$recid = makeNewRec($callid);
	writeToLog($callid, $fh, "", "Assigned Recording ID ".$recid);

	$rerecord = "TRUE";// For doing a rerecording
	$rerecordAllowed = 1; // Rerecord allowed in this call
	while($rerecord == "TRUE"){
		$rerecord = "FALSE";
		writeToLog($callid, $fh, "", "Prompting the user to speak");
		$result = record($prompts_dir.$Prompt_for_speaking,//"Just say something after the beep and Press # when done."
					array(
						"beep" => true, "timeout" => 600, "silenceTimeout" => 3, "maxTime" => 15, "bargein" => false, "terminator" => "#",
						"recordURI" => $scripts_dir."process_recording.php?recid=$recid",
						"format" => "audio/wav",
						//"onTimeout" => create_function("$event", 'say("timeout");'),
						//"onRecord" => create_function("$event", 'say("Record");'),
						//"onEvent" => create_function("$event", 'say("Event is $event->name");'),
						"onHangup" => create_function("$event", "Prehangup()")
						)
					);
		$resultstr = implode(" , " , $result);
		writeToLog($callid, $fh, "", "Recording Complete.");
			
		$filePath = $praat_dir;	
		$fileNames[0] = "ModifiedRecordings/0-s";
		$fileNames[1] = "ModifiedRecordings/1-s";
		$fileNames[2] = "ModifiedRecordings/2-s";
		$fileNames[3] = "ModifiedRecordings/3-s";
		$fileNames[4] = "ModifiedRecordings/4-s";
		$fileNames[5] = "ModifiedRecordings/5-s";
		$fileNames[6] = "ModifiedRecordings/6-s";
		$fileNames[7] = "ModifiedRecordings/FLs";
		$fileNames[8] = "ModifiedRecordings/HIs";
		$fileNames[9] = "ModifiedRecordings/LOs";
		$fileNames[10] = "ModifiedRecordings/RVSs";
		$fileNames[11] = "ModifiedRecordings/BKMUZWHs";
		
		$Path = "";
		$repeat = "TRUE";

		writeToLog($callid, $fh, "", "Prompting the user to get ready for effects.");
		say($prompts_dir.$Ready_for_effects);//"Now get ready for the effects... Here it goes!!!"
		
		$NumOfEffects = 6;
		for($count = 0; $count <= $NumOfEffects && $repeat == "TRUE"; $count+=1){
			$Path = $filePath.$fileNames[$count]."-".$recid.".wav";
			
			if(!file_exists($Path)){
				say($prompts_dir.$Processing_plz_wait);//"Processing. Please wait!";
				writeToLog($callid, $fh, "", "File has not been created yet. Played the prompt to wait.");
				$reps = 0;
				while(!file_exists($Path) && $reps<10){
					writeToLog($callid, $fh, "", "Playing the clock as the user waits.");
						
					say($prompts_dir.$Processing_plz_wait);//"Processing. Please wait!";
					say($praat_dir."clock_fast.wav");
					$reps = $reps+1;
				}
			}
			else{
				say($praat_dir."clock_fast.wav");
			}

			$presult = ask($Path,// Play the modified sound file. Interrupt with any key
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($presult->name == 'choice')
			{
				writeToLog($callid, $fh, "r-barge-in", "User pressed ".$presult->value." to skip ".$Path.".");
			}
		
			_log("Prompt finished.");
			writeToLog($callid, $fh, "", "Played effect number: ".$count);
			writeToLog($callid, $fh, "", "Now playing effect related options.");
				
			$repeat = "FALSE";	// If not action taken then loop will break
			$result = ask($prompts_dir.$Ask_for_forwarding2,//"To Repeat, press one. To send to a friend, press two. To try another effect, press three",
			array(
					"choices" => "[1 DIGITS]",	//Using the [1 DIGITS] to allow tracking wrong keys"rpt(1,rpt), fwd(2, fwd), cont(3,cont), feedback(8, feedback), quit(9, quit)", 
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "keystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($result->name == 'choice'){
				if ($result->value == 1)//'rpt')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Repeat).");
					$repeat = "TRUE";
					$count = $count - 1;
				}
				else if ($result->value==2)//'fwd')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Forward).");
					//////////////////////////
					$rerecordAllowed = 0;	// Can't rerecord in this call anymore

					if($reply == "TRUE" && $countMsgs <= 1){
						$Ask_for_forwarding2 = "Askforforwarding2-Reply.wav";
					}
					else if($reply == "TRUE" && $countMsgs > 1){
						$Ask_for_forwarding2 = "Askforforwarding2-ReplyMsg.wav";
					}
					else if($countMsgs > 1){
						$Ask_for_forwarding2 = "Askforforwarding2Msg.wav";
					}
					else{
						$Ask_for_forwarding2 = "Askforforwarding2.wav";
					}
					//$Age = searchPh($userid);// added global
					if($Age > 4){			// Caller's age is more than 5 User Initiated calls...
						if($countMsgs > 1){
							$Ask_for_forwarding2 = "AskforforwardingandFeedbackMsg.wav";
							if($reply == "TRUE"){
								$Ask_for_forwarding2 = "AskforforwardingandFeedback-ReplyMsg.wav";
							}
						}
						else{
							$Ask_for_forwarding2 = "AskforforwardingandFeedback.wav";
							if($reply == "TRUE"){
								$Ask_for_forwarding2 = "AskforforwardingandFeedback-Reply.wav";
							}
						}
					}
					//////////////////////////
					if($reply == "FALSE"){
						$repeat = "TRUE";
						sendToFriends($callid,$recid, $userid, $count, $Path);
						say($prompts_dir.$Forward_confirmation);
					}
					else if($reply == "TRUE"){
						$repeat = "FALSE";
						replyToFriend($callid,$recid, $userid, $count, $Path);
						say($prompts_dir.$Forward_confirmation2);
					}
				}
				else if($result->value==3)//'cont')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Next).");
					$repeat = "TRUE";
					if($count == $NumOfEffects){
						$count = -1;
					}
					continue;
				}
				else if($result->value==5)//'Jobs')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Jobs).");
					if($BabaJobs == 'true'){	
						say($prompts_dir.$Processing_plz_wait);
						if($Country == 'US'){
							transfer(array("sip:9990051311@sbc-external.orl.voxeo.net"), array(
							   "playvalue" => "http://www.phono.com/audio/holdmusic.mp3",
							   "terminator" => "*",
							   "onTimeout" => "timeoutFCNTr",
							   "onSuccess" => "successFCNTr",
								'headers' => array('pollycallid' => $callid)
							   )
							);
						}
						else if($Country == 'IN'){
							transfer(array("sip:9990067118@sbc-external.orl.voxeo.net"), array(
							   "playvalue" => "http://www.phono.com/audio/holdmusic.mp3",
							   "terminator" => "*",
							   "onTimeout" => "timeoutFCNTr",
							   "onSuccess" => "successFCNTr",
       'headers' => array('pollycallid' => $callid)
							   )
							);
						}
						writeToLog($callid, $fh, "", "User returned from BabaJobs IVR.");
					}
					else{
						answerCall_J($callid, 'FALSE', 'FALSE');
					}
					$repeat = "FALSE";
					if($count == $NumOfEffects){
						$count = -1;
					}
					continue;
				}
				else if($result->value==6 && $countMsgs > 1)//'Messages')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Messages).");	
					deliverMsg();
					$repeat = "FALSE";
					if($count == $NumOfEffects){
						$count = -1;
					}
					continue;
				}
				else if($result->value==6 && $countMsgs <= 1)//'Messages')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Messages) but he has no messages.");
					$repeat = "TRUE";
					say($prompts_dir.$msgNoMore);
					$count = $count - 1;// Dealing with bad choices as repeats.
				}
				else if($result->value==8)//'feedback')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Feedback).");
					$fbtype = "UInit";
					$fbid = makeNewFB($fbtype, $callid);
					$repeat = "TRUE";
					$feedBack = record($prompts_dir.$Record_your_feedback,//""
							array(
								"beep" => true, "timeout" => 600.0, "silenceTimeout" => 4.0, "maxTime" => 60, "bargein" => false, "terminator" => "#",
								"recordURI" => $scripts_dir."process_feedback.php?fbid=$fbid&fbtype=$fbtype",
								"format" => "audio/wav",
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);
					say($prompts_dir.$Thanks_for_Feedback);
					$count = $count - 1;
				}
				else if ($result->value==9)//'quit')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Quit).");
					$repeat = "FALSE";
				}
				else if ($result->value==0 && $rerecordAllowed == 1)//'rerecord')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Rerecord).");
					$repeat = "FALSE";
					$rerecord = "TRUE";
				}
				else{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (wrong key).");
					$repeat = "TRUE";
					say($prompts_dir.$BadChoiceKeys);
					$count = $count - 1;// Dealing with bad choices as repeats.
				}
			}
			else{
				writeToLog($callid, $fh, "", "User did not press any key.");
				$repeat = "FALSE";
			}
			if($count == $NumOfEffects){
				$count = -1;
			}
		}//Continue in for loop: play next effect
	}// Only loop this loop if the user want to rerecord
	if($reply != "TRUE"){// If this function was being used to record a reply then don't hangup from here
		say($prompts_dir.$Bye);//"Thanks for calling. Good Bye."
		Prehangup();
		writeToLog($callid, $fh, "", "Hanging up.");
	}
}// end answerCall() function

function answerCall_J($callid, $reply, $Delivery)
{
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;
	
	global $J_Greetings;
	global $J_Greetings2;
	global $J_Here_it_is;
	global $J_Skip;
	global $J_NoMoreAds;
	global $J_CallLater;
	global $J_PlayingFromBeg;
	global $J_YouCan;
	global $ContactDetails;
	global $J_Menu;
	global $J_ConfirmSend;
	global $J_Next;
	global $J_Prev;
	global $J_SolicitFeedback;
	global $Thanks_for_Feedback;
	global $BadChoiceKeys;
	global $TimeOutKeys;
	global $Bye;
	
	global $logFilePath;
	global $fh;
	global $calltype;
	global $userid;
	global $recIDtoPlay;
	global $callid;
	global $oreqid;
	global $ocallid;
	global $Age;
	
	if($calltype == 'JDelivery'){
		//$Age = searchPh($userid);//Added Global					// How many times has he called us before?
		if($Age < 2 || $calltype == 'SystemMessage'){
			writeToLog($callid, $fh, "", "Sending SMS to ".$userid." because the age of the user is ".$Age);
			sendSMS($userid, 'JDel');// Send a SMS to the receiver
		}
	}
	
	if($calltype != 'JDelivery'){
		writeToLog($callid, $fh, "", "Playing Greetings");
		
		$result = ask($prompts_dir.$J_Greetings,
			array(
							"choices" => "UR(1,UR), EN(2, EN), KA(3, KA)", 
							"mode" => 'dtmf',
							"repeat" => 0,
							"bargein" => true,
							"timeout" => 0,
							"onHangup" => create_function("$event", "Prehangup()")
						)
					);
		if($result->value=='UR'){
			say("Switching to Urdu.");
			$Country = 'PK';
			$prompts_dir = $base_dir."prompts/Urd_prompts/";

			writeToLog($callid, $fh, "", "User pressed 1 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->value=='EN'){
			say("Switching to English.");
			$Country = 'US';
			$prompts_dir = $base_dir."prompts/Eng_prompts/";
		
			writeToLog($callid, $fh, "", "User pressed 2 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->value=='KA'){
			say("Switching to Kannada.");
			$Country = 'IN';
			$prompts_dir = $base_dir."prompts/Kan_prompts/";

			writeToLog($callid, $fh, "", "User pressed 3 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->name == 'choice')
		{
			writeToLog($callid, $fh, "p-barge-in", "User pressed a button to skip ".$prompts_dir.$J_Greetings.".");
		}	
	}
	else{
		/*writeToLog($callid, $fh, "", "Starting Call Recording");
		startCallRecording($scripts_dir."process_callRec.php?callid=$callid");//process_callRec*/
		$status = "InProgress";
		updateCallStatus($callid, $status);
		$status = "fulfilled";
		updateRequestStatus($oreqid, $status);
		writeToLog($callid, $fh, "CallAnswered", "Call Answered.");
		writeToLog($callid, $fh, "say", "Playing greetings for ad delivery");
		
		$result = ask($prompts_dir.$J_Greetings2, 
			array(
							"choices" => "UR(1,UR), EN(2, EN), KA(3, KA)", 
							"mode" => 'dtmf',
							"repeat" => 0,
							"bargein" => true,
							"timeout" => 0,
							"onHangup" => create_function("$event", "Prehangup()")
						)
					);
		if($result->value=='UR'){
			say("Switching to Urdu.");
			$Country = 'PK';
			$prompts_dir = $base_dir."prompts/Urd_prompts/";

			writeToLog($callid, $fh, "", "User pressed 1 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->value=='EN'){
			say("Switching to English.");
			$Country = 'US';
			$prompts_dir = $base_dir."prompts/Eng_prompts/";
		
			writeToLog($callid, $fh, "", "User pressed 2 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->value=='KA'){
			say("Switching to Kannada.");
			$Country = 'IN';
			$prompts_dir = $base_dir."prompts/Kan_prompts/";

			writeToLog($callid, $fh, "", "User pressed 3 (cheat key)");
			writeToLog($callid, $fh, "", "Country is set to: ".$Country);
		}
		else if($result->name == 'choice')
		{
			writeToLog($callid, $fh, "p-barge-in", "User pressed a button to skip ".$prompts_dir.$J_Greetings2.".");
		}		
		$Name = $praat_dir."UserNames/UserName-".$ocallid.".wav";
		say($Name);
		say($prompts_dir.$J_Here_it_is);//"And here is the message..."		
	}
	
	//$Age = searchPh($userid); // Added Global
	if($Age > 4){			// Caller's age is more than 5 User Initiated calls...
		$Ask_for_forwarding2 = "CorrAskforforwardingandFeedback.wav";
	}
	$Path = "";
	$repeat = "TRUE";
	$iter = 0;
	$JustContact = "FALSE";
	
	if($calltype != 'JDelivery'){
		$AdID = getMaxAdID();
		say($prompts_dir.$J_Skip);
	}
	else{
		$AdID = $recIDtoPlay;
	}
	
	writeToLog($callid, $fh, "", "AdID: ".$AdID.".");

	while($repeat == "TRUE"){
		$isThisCallActive = $currentCall->isActive;
		writeToLog($callid, $fh, "isActive", "Is the current call active? ".$isThisCallActive." Hanging up if the call is not active.");			
		if($isThisCallActive == "false"){
			Prehangup();
		}
		
		if($AdID == 0){
			writeToLog($callid, $fh, "", "No more Ads.");
				
			say($prompts_dir.$J_NoMoreAds);
			$AdID = getMaxAdID();
			if($AdID == 0){
				say($prompts_dir.$J_CallLater);
				writeToLog($callid, $fh, "", "There are no ads in the system. Hanging up.");
				Prehangup();
			}
			else{
				say($prompts_dir.$J_PlayingFromBeg);
				writeToLog($callid, $fh, "", "Starting from the most recent ad again.");
			}
		}
		$DatePaper = $base_dir."/JobAds/DP-".$AdID.".wav";
		$AdPath = $base_dir."/JobAds/IN-".$AdID.".wav";
		$ContactPath = $base_dir."/JobAds/CT-".$AdID.".wav";
		
		if(($iter == 1 || $iter==3) && $calltype == 'JDelivery'){
			$presult = ask($prompts_dir.$J_YouCan,// Play the message file. Interrupt with any key
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($presult->name == 'choice')
			{
				writeToLog($callid, $fh, "r-barge-in", "User pressed ".$presult->value." to skip ".$prompts_dir.$J_YouCan.".");
			}
			$presult = ask($prompts_dir.$ContactDetails,// Play the message file. Interrupt with any key
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($presult->name == 'choice')
			{
				writeToLog($callid, $fh, "r-barge-in", "User pressed ".$presult->value." to skip ".$prompts_dir.$ContactDetails.".");
			}
		}
		$iter = $iter+1;

		writeToLog($callid, $fh, "", "Now Playing AdID: ".$AdID.".");
		if($JustContact != "TRUE"){
			writeToLog($callid, $fh, "", "Playing Date and Paper.");
			
			$action = ask($DatePaper,// Play the Job ad body
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			writeToLog($callid, $fh, "", "Incrementing 'No of times played' for AdID: ".$AdID.".");
				
			incNoOfTimesPlayed($AdID);
			writeToLog($callid, $fh, "", "Adding a row to new Ads Played by PhNo for AdID: ".$AdID." Ph no: ".$userid.".");
				
			newAdsPlayedbyPhNo($AdID, $userid);
			newAdsPlayedbyCallID($AdID, $callid);
			
			if ($action->value == ''){	// play only if the user did not skip the previous recording
				writeToLog($callid, $fh, "", "Playing Ad body.");
			
				$action = ask($AdPath,// Play the Job ad body
				array(
						"choices" => "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => true,
						"repeat" => 0,
						"timeout"=> 0.1,
						"onHangup" => create_function("$event", "Prehangup()")
					)
				);
			}
		}
		if ($action->value == ''){	// play only if the user did not skip the previous recording
			writeToLog($callid, $fh, "", "Playing Contact Details.");
				
			$action = ask($ContactPath,// Play the Job contact details
			array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 0.1,
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
		}	
		$JustContact = "FALSE";
		if ($action->value == ''){	// play only if the user did not skip the previous recording
			$result = ask($prompts_dir.$J_Menu,
			array(
					"choices" => "[1 DIGITS]",//Using the [1 DIGITS] to allow tracking wrong keys
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 0,
					"timeout"=> 5,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "keystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($result->name == 'choice'){
				if ($result->value == 1)//'rpt the contact')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Repeat the contact details).");
					$JustContact = "TRUE";
					$repeat = "TRUE";
				}
				else if ($result->value==2)//'rpt comp ad')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Repeat the whole ad).");
					$repeat = "TRUE";			
				}
				else if ($result->value==3)//'send to friend')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Send to a friend).");
					sendToFriends_J($callid,$AdID, $userid, 0, $Path);
					say($prompts_dir.$J_ConfirmSend);
					$repeat = "TRUE";
				}
				else if ($result->value==4)//'prev')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Next).");
					$AdID = getMinAdID_GreaterThanID($AdID);
					$repeat = "TRUE";
					if($AdID != 0){
						say($prompts_dir.$J_Prev);
					}
				}
				else if ($result->value==5)//'next')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Next).");
					$AdID = getMaxAdID_LessThanID($AdID);
					$repeat = "TRUE";
					if($AdID != 0){
						say($prompts_dir.$J_Next);
					}
				}
				else if($result->value==8)//'feedback')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Feedback).");
					$fbtype = "UInit";
					$fbid = makeNewFB($fbtype, $callid);
					$repeat = "TRUE";
					$feedBack = record($prompts_dir.$J_SolicitFeedback,//""
							array(
								"beep" => true, "timeout" => 600.0, "silenceTimeout" => 4.0, "maxTime" => 60, "bargein" => false, "terminator" => "#",
								"recordURI" => $scripts_dir."process_feedback.php?fbid=$fbid&fbtype=$fbtype",
								"format" => "audio/wav",
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);
					say($prompts_dir.$Thanks_for_Feedback);
					$count = $count - 1;
				}
				else if ($result->value==9)//'quit')
				{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (Quit).");
					$repeat = "FALSE";
				}
				else{
					writeToLog($callid, $fh, "", "User pressed ".$result->value." (wrong key).");
					$repeat = "TRUE";
					say($prompts_dir.$BadChoiceKeys);
				}
			}
			else{
				writeToLog($callid, $fh, "", "User did not press any key. Going to the next Ad.");
				$AdID = getMaxAdID_LessThanID($AdID);
				if($AdID != 0){
					say($prompts_dir.$J_Next);
				}
				$repeat = "TRUE";
			}
		}
		else{
			writeToLog($callid, $fh, "", "User skipped the current ad by pressing".$action->value.". Going to the next ad.");
			$AdID = getMaxAdID_LessThanID($AdID);
			$repeat = "TRUE";
			if($AdID != 0){
				say($prompts_dir.$J_Next);
			}
		}
		$action->value = '';
	}//While $repeat == "TRUE"
	say($prompts_dir.$Bye);//"Thanks for calling. Good Bye."
	Prehangup();
	writeToLog($callid, $fh, "", "Hanging up.");
}// end answerCall_J() function

function sendToFriends_J($callid, $AdID, $telNumber, $count, $songpath)
{	
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;
	
	global $Friend_no_prompt;
	global $BadChoiceKeys;
	global $TimeOutKeys;
	global $Bye;
	global $Record_your_name;
	global $Friend_name;
	global $Another_Friend;
	global $J_SolicitFeedback;
	global $Record_your_feedback;
	global $Thanks_for_Feedback;
	
	global $AlreadygivenFeedback;
	global $fh;
	global $calltype;
	global $userid;
	global $Age;
	
	//Prompt for friends' numbers
	$FriendsNumber = 'true';
	$numNewRequests = 1;
	while($FriendsNumber != 'false')
	{
		writeToLog($callid, $fh, "", "Now getting friend's phone number for request number".$numNewRequests.".");		
		$NumberList = ask($prompts_dir.$Friend_no_prompt,//"Please enter the phone number of your friend followed by the pound key",
			array(
				"choices"=>"[1-14 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => true,
				"attempts" => 3,
				"timeout"=> 15,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "keystimeOutFCN",
				"terminator" => "#",
				"onHangup" => create_function("$event", "Prehangup()")
				)
			);
		if($NumberList->name == 'choice'){
			if($numNewRequests == 1){// User has entered the first friend number
				writeToLog($callid, $fh, "", "Now recording user's name.");
				// Prompt the user for his/her name
				$friendsName = record($prompts_dir.$Record_your_name,//"Please record your name, so that your friend can send you a message back",
							array(
								"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => "#",
								"recordURI" => $scripts_dir."process_UserNamerecording.php?callid=$callid",
								"format" => "audio/wav",
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);
				writeToLog($callid, $fh, "", "Recording of user's name complete.");
										
			}
			// Create a new Request here
			//&&$$**
			$frndNoUnEnc = $NumberList->value;	// added
			$frndNoEnc = PhToKeyAndStore($frndNoUnEnc, $userid);	// added
			$reqid = makeNewReq($AdID, $count, $callid, "USJDelivery", $frndNoEnc, "Pending");	// changed $NumberList->value to $frndNoEnc
			//&&$$**
			writeToLog($callid, $fh, "", "Assigned request ID: ".$reqid);
			writeToLog($callid, $fh, "", "Now recording user's friend's name");
				
			$friendsName = record($prompts_dir.$Friend_name,//"Please record your friend's name, so that you may be able to reach them easily next time",
				array(
					"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => "#",
					"recordURI" => $scripts_dir."process_FriendNamerecording.php?reqid=$reqid",
					"format" => "audio/wav",
					"onError" => create_function("$event", 'say("Wrong Input");'),
					"onTimeout" => create_function("$event", 'say("No Input");'),
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
			writeToLog($callid, $fh, "", "User's friend's name recording complete.");
			writeToLog($callid, $fh, "", "Asking the user if he wants to record another name.");
				
			$WrongButtonPressed = 'TRUE';
			$failsafe = 0;
			while($WrongButtonPressed == 'TRUE'){
				$WrongButtonPressed = 'FALSE';
				$MoreNumbers = ask($prompts_dir.$Another_Friend,//"To add another number, press one, or if you are done, press two",
					array(
						"choices"=> "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => true,
						"attempts" => 2,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "keystimeOutFCN",
						"timeout"=> 10,
						"onHangup" => create_function("$event", "Prehangup()")
						)
					);
				if($MoreNumbers->name == 'choice' && $MoreNumbers->value != 1 && $MoreNumbers->value != 2){	// Wrong button
					$WrongButtonPressed = 'TRUE';
					say($prompts_dir.$BadChoiceKeys);
					writeToLog($callid, $fh, "", "User pressed a wrong key: ".$MoreNumbers->value);
				}
				if($failsafe >=3){
					Prehangup();
				}
				$failsafe++;
			}

			if($MoreNumbers->name == 'choice'){	
				if($MoreNumbers->value == 2){
					writeToLog($callid, $fh, "", "User presses ".$MoreNumbers->value." to say that he is done");
					$FriendsNumber = 'false';
				}
				else if($MoreNumbers->value == 1){
					writeToLog($callid, $fh, "", "User presses ".$MoreNumbers->value." to say that he wants to record another number.");
					$numNewRequests++;
				}
			}
			else{	// No key was pressed so, assume that he does not want to add another number
				writeToLog($callid, $fh, "", "Timed out. User did not press any key. Now proceeding.");
				$FriendsNumber = 'false';
			}
		}
		else{
				writeToLog($callid, $fh, "", "Timed out. No number entered. Now hanging up.");
				say($prompts_dir.$Bye);//"Thanks for calling. Good Bye."
				$FriendsNumber = 'false';
				Prehangup();
		}
	}//End of while($Friendsnumber != false)
	
	// Requesting feedback
	//$Age = searchPh($telNumber);//Added Global					// How many times has he called us before?
	$previousFeedBack = gaveFeedBack($telNumber);	// Did he ever give feedback before?
	if(((($Age > 5 && $previousFeedBack == 0) || $Age % 20 == 0) && $AlreadygivenFeedback == "FALSE") && $Age != 0){
		$AlreadygivenFeedback = "TRUE";
		writeToLog($callid, $fh, "", "Requesting System prompted feedback.");
		$fbtype = "SPrompt";
		$fbid = makeNewFB($fbtype, $callid);
				
		$feedBack = record($prompts_dir.$J_SolicitFeedback,//
				array(
					"beep" => true, "timeout" => 600.0, "silenceTimeout" => 4.0, "maxTime" => 30, "bargein" => false, "terminator" => "#",
					"recordURI" => $scripts_dir."process_feedback.php?fbid=$fbid&fbtype=$fbtype",
					"format" => "audio/wav",
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
		say($prompts_dir.$Thanks_for_Feedback);
		writeToLog($callid, $fh, "", "System prompted feedback recording complete.");
			
	}
	return $numNewRequests;	
}

function sendToFriends($callid, $recid, $telNumber, $count, $songpath)
{	
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;
	
	global $Friend_no_prompt;
	global $BadChoiceKeys;
	global $TimeOutKeys;
	global $Record_your_feedback;
	global $Thanks_for_Feedback;
	global $Bye;
	global $Friend_name;
	global $Another_Friend;
	global $Record_your_name;
	global $J_SPrompt;
	
	global $AlreadygivenFeedback;
	global $fh;
	global $calltype;
	global $userid;
	global $AlreadyHeardJobs;
	global $Age;
	//&&$$**
	global $useridUnEnc;
	//&&$$**
	
	//Prompt for friends' numbers
	$FriendsNumber = 'true';
	$numNewRequests = 1;
	while($FriendsNumber != 'false')
	{
		writeToLog($callid, $fh, "", "Now getting friend's phone number for request number".$numNewRequests.".");
		$NumberList = ask($prompts_dir.$Friend_no_prompt,//"Please enter the phone number of your friend followed by the pound key",
			array(
				"choices"=>"[1-14 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => true,
				"attempts" => 3,
				"timeout"=> 15,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "keystimeOutFCN",
				"terminator" => "#",
				"onHangup" => create_function("$event", "Prehangup()")
				)
			);
		if($NumberList->name == 'choice'){	
			if($numNewRequests == 1){// User has entered the first friend number
				writeToLog($callid, $fh, "", "Now recording user's name.");
					
				// Prompt the user for his/her name
				$friendsName = record($prompts_dir.$Record_your_name,//"Please record your name, so that your friend can send you a message back",
							array(
								"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => "#",
								"recordURI" => $scripts_dir."process_UserNamerecording.php?callid=$callid",
								"format" => "audio/wav",
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);
				writeToLog($callid, $fh, "", "Recording of user's name complete.");
			}
			// Create a new Request here				
			//&&$$**
			$frndNoUnEnc = $NumberList->value;	// added
			$frndNoEnc = PhToKeyAndStore($frndNoUnEnc, $userid);	// added
			$reqid = makeNewReq($recid, $count, $callid, "USDelivery", $frndNoEnc, "Pending");	// changed $NumberList->value to $frndNoEnc
			//&&$$**
			writeToLog($callid, $fh, "", "Assigned request ID: ".$reqid);
			writeToLog($callid, $fh, "", "Now recording user's friend's name");
			// Add to BJ Logs
			//addToBJLogs($callid, $fh, $useridUnEnc, $frndNoUnEnc, $recid, $count);
					
			$friendsName = record($prompts_dir.$Friend_name,//"Please record your friend's name, so that you may be able to reach them easily next time",
				array(
					"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => "#",
					"recordURI" => $scripts_dir."process_FriendNamerecording.php?reqid=$reqid",
					"format" => "audio/wav",
					"onError" => create_function("$event", 'say("Wrong Input");'),
					"onTimeout" => create_function("$event", 'say("No Input");'),
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
			writeToLog($callid, $fh, "", "User's friend's name recording complete.");
			writeToLog($callid, $fh, "", "Asking the user if he wants to record another name.");
				
			$WrongButtonPressed = 'TRUE';
			$failsafe = 0;
			while($WrongButtonPressed == 'TRUE'){
				$WrongButtonPressed = 'FALSE';
				$MoreNumbers = ask($prompts_dir.$Another_Friend,//"To add another number, press one, or if you are done, press two",
					array(
						"choices"=> "[1 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => true,
						"attempts" => 2,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "keystimeOutFCN",
						"timeout"=> 10,
						"onHangup" => create_function("$event", "Prehangup()")
						)
					);
				if($MoreNumbers->name == 'choice' && $MoreNumbers->value != 1 && $MoreNumbers->value != 2){	// Wrong button
					$WrongButtonPressed = 'TRUE';
					say($prompts_dir.$BadChoiceKeys);
					writeToLog($callid, $fh, "", "User pressed a wrong key: ".$MoreNumbers->value);
				}
				if($failsafe >=3){
					Prehangup();
				}
				$failsafe++;
			}

			if($MoreNumbers->name == 'choice'){	
				if($MoreNumbers->value == 2){
					writeToLog($callid, $fh, "", "User presses ".$MoreNumbers->value." to say that he is done");
					$FriendsNumber = 'false';
				}
				else if($MoreNumbers->value == 1){
					writeToLog($callid, $fh, "", "User presses ".$MoreNumbers->value." to say that he wants to record another number.");
					$numNewRequests++;
				}
			}
			else{	// No key was pressed so, assume that he does not want to add another number
				writeToLog($callid, $fh, "", "Timed out. User did not press any key. Now proceeding.");
				$FriendsNumber = 'false';
			}
		}
		else{
				writeToLog($callid, $fh, "", "Timed out. No number entered. Now hanging up.");
				say($prompts_dir.$Bye);//"Thanks for calling. Good Bye."
				$FriendsNumber = 'false';
				Prehangup();
		}
	}//End of while($Friendsnumber != false)
	
	// Requesting feedback
	//$Age = searchPh($telNumber);//Added Global					// How many times has he called us before?
	$previousFeedBack = gaveFeedBack($telNumber);	// Did he ever give feedback before?
	if(((($Age > 5 && $previousFeedBack == 0) || $Age % 20 == 0) && $AlreadygivenFeedback == "FALSE") && $Age != 0){
		$AlreadygivenFeedback = "TRUE";
		writeToLog($callid, $fh, "", "Requesting System prompted feedback ".$Age." ".$previousFeedBack." ".$telNumber.".");
		$fbtype = "SPrompt";
		$fbid = makeNewFB($fbtype, $callid);
				
		$feedBack = record($prompts_dir.$Record_your_feedback,//
				array(
					"beep" => true, "timeout" => 600.0, "silenceTimeout" => 4.0, "maxTime" => 30, "bargein" => false, "terminator" => "#",
					"recordURI" => $scripts_dir."process_feedback.php?fbid=$fbid&fbtype=$fbtype",
					"format" => "audio/wav",
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
		say($prompts_dir.$Thanks_for_Feedback);
		writeToLog($callid, $fh, "", "System prompted feedback recording complete.");
			
	}
	writeToLog($callid, $fh, "", "Age is: ".$Age." for telephone number: ".$telNumber.". Alreadyheardjobs is: ".$AlreadyHeardJobs);
	
	// Introducing Jobs
	if((($Age % 6 ==0 || $Age % 9 == 0) && $AlreadyHeardJobs == "FALSE") && $Age != 0){
		$AlreadyHeardJobs = "TRUE";
		writeToLog($callid, $fh, "", "Introducing Jobs as the age is: ".$Age." for telephone number: ".$telNumber.".");
			
		$result = ask($prompts_dir.$J_SPrompt,//"Do you know that now you can listen to news paper job ads for free. Just press 1.", 
		array(
				"choices" => "swap(1,swap)", 
				"mode" => 'dtmf',
				"repeat" => 0,
				"bargein" => true,
				"timeout" => 7,
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);
		if($result->value=='swap'){			
			writeToLog($callid, $fh, "kp:1:op:~ User pressed 1 (Go to Jobs)");
			answerCall_J($callid, 'FALSE', 'FALSE');
		}
		else if($result->name == 'choice')
		{
			writeToLog($callid, $fh, "m-other-choice", "User pressed some button other than 1 at ".$prompts_dir.$J_SPrompt."."); // may or may not be a bargein
		}	
		writeToLog($callid, $fh, "", "System prompted Jobs intro complete. User decided to move on by not pressing 1.");	
	}
	return $numNewRequests;	
}

function replyToFriend($callid, $recid, $telNumber, $count, $songpath)
{	
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;
	
	global $Greetings;
	global $Prompt_for_speaking;
	global $Processing_plz_wait;
	global $Ask_for_forwarding;
	global $Ask_for_forwarding2;
	global $Ready_for_effects;
	global $Bye;
	global $Friend_no_prompt;
	global $Number_confirm;
	global $Friend_name;
	global $Another_Friend;
	global $Try_again;
	global $Record_your_name;
	global $Forward_confirmation;
	global $BadChoiceKeys;
	global $TimeOutKeys;
	global $Friend_no_repeat;
	global $Record_your_feedback;
	global $Thanks_for_Feedback;
	
	global $AlreadygivenFeedback;
	global $fh;
	global $calltype;
	global $Age;
	//&&$$**
	global $useridUnEnc;
	//&&$$**
		
	writeToLog($callid, $fh, "", "Now recording user's name.");	
	// Prompt the user for his/her name
	$friendsName = record($prompts_dir.$Record_your_name,//"Please record your name, so that your friend can send you a message back",
				array(
					"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => "#",
					"recordURI" => $scripts_dir."process_UserNamerecording.php?callid=$callid",
					"format" => "audio/wav",
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
	writeToLog($callid, $fh, "", "Recording of user's name complete.");
							
	
	// Create a new Request here				
	$reqid = makeNewReq($recid, $count, $callid, "USDelivery", getPhNo(), "Pending");
	writeToLog($callid, $fh, "", "Assigned request ID: ".$reqid);
	writeToLog($callid, $fh, "", "Now recording user's friend's name");
	// Add to BJ Logs
	//addToBJLogs($callid, $fh, $useridUnEnc, KeyToPh(getPhNo()), $recid, $count);
		
	$friendsName = record($prompts_dir.$Friend_name,//"Please record your friend's name, so that you may be able to reach them easily next time",
		array(
			"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => "#",
			"recordURI" => $scripts_dir."process_FriendNamerecording.php?reqid=$reqid",
			"format" => "audio/wav",
			"onError" => create_function("$event", 'say("Wrong Input");'),
			"onTimeout" => create_function("$event", 'say("No Input");'),
			"onHangup" => create_function("$event", "Prehangup()")
			)
		);
	writeToLog($callid, $fh, "", "User's friend's name recording complete.");
	writeToLog($callid, $fh, "", "Asking the user if he wants to record another name.");
}
//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// DB Access Functions ////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
function makeNewRec($callid)
{
	global $DB_dir;
	$url = $DB_dir."New_Rec.php?callid=$callid";
	$result = doCurl($url);
	return $result;
}

function makeNewCall($calltype, $reqid, $phno, $status)
{
	global $DB_dir;
	$url = $DB_dir."New_Call.php?reqid=$reqid&phno=$phno&calltype=$calltype&status=$status";
	$result = doCurl($url);
	return $result;
}

function makeNewReq($recid, $effect, $callid, $reqtype, $phno, $status)
{
	global $DB_dir;
	$url = $DB_dir."New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&phno=$phno&status=$status";
	$result = doCurl($url);
	return $result;
}

function makeNewFB($fbtype, $callid)
{
	global $DB_dir;
	$url = $DB_dir."New_FB.php?fbtype=$fbtype&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function markCallEndTime($callid){
	global $DB_dir;
	$url = $DB_dir."Update_Call_Endtime.php?callid=$callid";
	$result = doCurl($url);
	return $result;
}

function updateCallStatus($callid, $status){
	global $DB_dir;
	$url = $DB_dir."Update_Call_Status.php?callid=$callid&status=$status";
	$result = doCurl($url);
	return $result;
}

function updateRequestStatus($reqid, $status){
	global $DB_dir;
	$url = $DB_dir."Update_Request_Status.php?reqid=$reqid&status=$status";
	$result = doCurl($url);
	return $result;
}

function Prehangup(){
	global $callid;
	global $fh;
	global $thiscallStatus;
	/////////////////////
	updateCallStatus($callid, $thiscallStatus);
	writeToLog($callid, $fh, "CallEnd", "Call ended for callid: ".$callid);
	
	/////////////////////
	markCallEndTime($callid);
	//fclose($fh);
	//stopCallRecording();
	hangup();
	exit(0);
}

function gaveFeedBack($ph)
{
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."gave_feedback.php?ph=$ph&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

/*function markRead($id){
	global $DB_dir;
	$url = $DB_dir."mark_read.php?reqid=$id";
	$result = doCurl($url);
	return $result;
}*/

function getPhNo()
{
	global $DB_dir;
	global $ocallid;
	$url = $DB_dir."GetPhNo.php?callID=$ocallid";
	$result = doCurl($url);
	return $result;
}

function sendSMS($phno, $type)
{
	global $callid;
	global $userid;
	if($type=='Del'){
		//$url = "http://www.smsall.pk/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20call%20kerain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSUSDelivery", $userid, "Pending");
	}
	else if($type=='JDel'){
		//$url = "http://www.smsall.pk/lumspolly/?username=lums&password=lUmSPoLLy&message=04238333111%20per%20muft%20nokri%20kay%20ishtihar%20sunain.%20Bas%20missed%20call%20kerain%20aur%20Mian%20Mithoo%20khud%20aap%20ko%20call%20karay%20ga.&number=$phno";
		$smsreq = makeNewReq('0', '0', $callid, "SMSUSJDelivery", $userid, "Pending");
	}
	//$result = doCurl($url);
	//return $result;
}

// Function to send Information to BJ
function addToBJLogs($callid, $fh, $sender, $friend, $recid, $count)
{
	global $praat_dir;	
	
	$URL = $praat_dir."ModifiedRecordings/".$count."-s-".$recid.".wav";
	$URLEnc = urlencode($URL);
	$curlString = "http://test.babajob.com/services/service.asmx/PollyInvitation?inviteeMobile=$friend&invitorMobile=$sender&invitorVoiceNameUrl=$URLEnc&serviceName=polly&servicePassword=pollytalks";
	$response = doCurl($curlString);
	writeToLog($callid, $fh, "SendToBJ", "Response: ".$response.", URL invoked: ".$curlString);
}

function doCurl($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;	
}

function isThisAMissedCall(){
	global $userid;
	global $TreatmentGroup;
	
	/*if($userid == '03334204496'){
		return 0;
	}*/
	
	
	if(inAnyTG($userid) > 0){
		$TreatmentGroup = getTG($userid);
	}
	
	$AgeToday = getAgeToday($userid);
	if($AgeToday <= ($TreatmentGroup)){
		return 1;
	}
	else{
		return 0;
	}
}

/////////////////// Cutoff based functions start ///////////////////////////
function getAgeToday($ph)
{
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."AgeToday.php?phno=$ph&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function getAgeinDays($ph)
{
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."AgeinDays.php?phno=$ph&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchCalls($ph)
{
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."search_calls.php?ph=$ph&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchPh($ph)
{
	global $DB_dir;
	global $CallTableCutoff;
	$url = $DB_dir."search_phno.php?ph=$ph&cutoff=$CallTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchCallsReq($ph)
{
	global $DB_dir;
	global $ReqTableCutoff;
	$url = $DB_dir."search_calls_hist.php?ph=$ph&cutoff=$ReqTableCutoff";
	$result = doCurl($url);
	return $result;
}

function updateCallsReq($ph)
{
	global $DB_dir;
	global $ReqTableCutoff;
	$url = $DB_dir."update_calls_hist.php?ph=$ph&cutoff=$ReqTableCutoff";
	$result = doCurl($url);
	return $result;
}

function my_messages($ph){
	global $DB_dir;
	global $ReqTableCutoff;
	$url = $DB_dir."my_messages.php?ph=$ph&cutoff=$ReqTableCutoff";
	$result = doCurl($url);
	return $result;
}
/////////////////// Cutoff based functions end ///////////////////////////

function readAndInc()
{
	global $DB_dir;
	$url = $DB_dir."ReadInc.php";
	$result = doCurl($url);
	return $result;
}

function inAnyTG($ph){
	global $DB_dir;
	$url = $DB_dir."search_TG.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function getTG($ph){
	global $DB_dir;
	$url = $DB_dir."get_TG.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function addToTG($tg){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."add_to_TG.php?ph=$userid&tg=$tg";
	$result = doCurl($url);
	return $result;
}

function setLastPlayedOn(){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."set_last_played_on.php?ph=$userid";
	$result = doCurl($url);
	return $result;
}

function getLastPlayedOn(){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."get_last_played_on.php?ph=$userid";
	$result = doCurl($url);
	return $result;
}

function getCallTableCutoff($days){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."getCallTableCutoff.php?days=$days";
	$result = doCurl($url);
	return $result;
}

function getReqTableCutoff($days){
	global $DB_dir;
	global $userid;
	$url = $DB_dir."getReqTableCutoff.php?days=$days";
	$result = doCurl($url);
	return $result;
}

//-------------------------------
function getMaxAdID()
{
	global $DB_dir;
	$url = $DB_dir."Max_AdID.php";
	$result = doCurl($url);
	return $result;
}
function getMaxAdID_LessThanID($id){
	global $DB_dir;
	$url = $DB_dir."Max_AdID_LessThanID.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function getMinAdID_GreaterThanID($id){
	global $DB_dir;
	$url = $DB_dir."Min_AdID_GreaterThanID.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function incNoOfTimesPlayed($id){
	global $DB_dir;
	$url = $DB_dir."Inc_NoOfTimesPlayed.php?ID=$id";
	$result = doCurl($url);
	return $result;
}
function newAdsPlayedbyPhNo($adid, $phno)
{
	global $DB_dir;
	$url = $DB_dir."New_Ads_played_by_phno.php?adid=$adid&phno=$phno";
	$result = doCurl($url);
	return $result;
}
function newAdsPlayedbyCallID($adid, $cid)
{
	global $DB_dir;
	$url = $DB_dir."New_Ads_played_by_CallID.php?adid=$adid&cid=$cid";
	$result = doCurl($url);
	return $result;
}
function MsgsPlayedinCallID($rid, $cid)
{
	global $DB_dir;
	$url = $DB_dir."Msgs_played_in_CallID.php?rid=$rid&cid=$cid";
	$result = doCurl($url);
	return $result;
}
//-------------------------------
//&&$$** Added Functions
// functions to encode, decode, store phone numbers
function PhToKeyAndStore($phno, $sender)
{
	global $DB_dir;
	$url = $DB_dir."insertNewPh.php?ph=$phno&sender=$sender";
	$result = doCurl($url);
	return $result;
}

function PhToKey($ph)
{
	global $DB_dir;
	$url = $DB_dir."phToKey.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function KeyToPh($key)
{
	global $DB_dir;
	$url = $DB_dir."keyToPh.php?key=$key";
	$result = doCurl($url);
	return $result;
}
//&&$$**
function writeToLog($id, $handle, $tag, $str){
	$writeToTropoLogs = "true";
	$spch1 = "%%";
	$spch2 = "$$";
	$del = "~";
	$colons = ":::";
	$string = $spch1 . $spch2 . $del . $id . $del. date('D_Y-m-d_H-i-s') . $del . $tag . $colons . $del . $str . $spch2 . $spch1;
	if($writeToTropoLogs == "true"){
		_log($string);
	}
	else{
		fwrite($handle, $string . "\n");
		fflush($handle);
	}
}
///////////////////////////////////////// old functions ////////////////////////////////////////
function makeCall()
{
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $DB_dir;
	global $base_dir;
	global $state;
	global $scripts_dir;
	global $praat_dir;
	global $Country;
	global $prompts_dir;
	
	$userID = $currentCall->calledID; //User is called as opposed to caller
	_log("User ID is ".$userID);
	//startCallRecording($scripts_dir."process_session_recording.php?userid=".$userID);
	$result = record($record_prompt,
			  array(
				"beep" => true, "timeout" => 10, "silenceTimeout" => 7, "maxTime" => 60, "bargein" => false, "terminator" => "#",
				"recordURI" => $scripts_dir."process_recording.php?callerid=$userID",
				"format" => "audio/wav",
				"onHangup" => create_function("$event", "hangup()")
				)
			  );
	say($completed_prompt);
	//stopCallRecording();
	hangup();
}
?>