<html><head><title>Light Controller</title></head><body><center><?php

#
#BEGIN DEFINES
#

$lat = 42.79656643;													//Latitude of your location
$lon = -83.3416815855555;												//Longitude of your location
$zen = 90;														//Zenith of your location
$gmt = -4;														//GMT offset of your time
$amoffset = 0;													//Seconds before sunrise that lights are turned on
$pmoffset = 0;													//Seconds before sunset that lights are turned on
$sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, $zen, $gmt) - $amoffset;		//The time of today's sunrise (with AM offset)
$tsunrise = date_sunrise(time() + 86400, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, $zen, $gmt) - $amoffset;	//The time of tomorrow's sunrise (with AM offset)
$sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, $zen, $gmt) - $pmoffset;		//The time of today's sunset (with PM offset)
$amoff = 0 + $sunrise;												//Seconds to turn lights off after they are turned on in the morning
$pmoff = 0 + $sunset;												//Seconds to turn lights off after they are turned on at night
$manual = $_REQUEST['manual'];											//See if the command sent was a manual command from a user

#
#END DEFINES
#



#
#BEGIN FUNCTIONS
#

function moveservo($ch,$pos,$ramp)
{
	@exec('stty -F /dev/ttyS0 ispeed 2400 ospeed 2400');
	$fp = @fopen('/dev/ttyS0','w+b'); 
	if (!$fp) { exit(); }
	$header1 = "21";
	$header1 = pack("H*", $header1);
	$header2 = "53";
	$header2 = pack("H*", $header2);
	$header3 = "43";
	$header3 = pack("H*", $header3);
	$footer = "0d";
	$footer = pack("H*", $footer);
	$ch = dec2hex($ch);
	$ch = pack("H*", $ch);
	$ramp = dec2hex($ramp);
	$ramp = pack("H*", $ramp);
	$poslowbyte = getlowbyte($pos);
	$poslowbyte = pack("H*", $poslowbyte);
	$poshighbyte = gethighbyte($pos);
	$poshighbyte = pack("H*", $poshighbyte);
	$sdata = $header1 . $header2 . $header3 . $ch . $ramp . $poslowbyte . $poshighbyte . $footer;
	@fputs ($fp, $sdata);
	@fclose ($fp);
}


function getlowbyte($percent)
{
	if ($percent >= 0 && $percent <= 100)
	{
		$servopos = ($percent * 10) + 250;
		if ($servopos <= 255)
		{
			$lowbyte = dec2hex($servopos);
		}
		else
		{
			$multi256 = (int) ($servopos / 256);
			$servopos = $servopos - ($multi256 * 256);
			$lowbyte = dec2hex($servopos);
		}
	
	}
	else
	{
		exit();
	}
	return $lowbyte;
}


function gethighbyte($percent)
{
	if ($percent >= 0 && $percent <= 100)
	{
		$servopos = ($percent * 10) + 250;
		if ($servopos <= 255)
		{
			$highbyte = "00";
		}
		else
		{
			$multi256 = (int) ($servopos / 256);
			$highbyte = dec2hex($multi256);
		}
	}
	else
	{
		exit();
	}
	return $highbyte;
}


function dec2hex($dec)
{
	$hex = dechex($dec);
	if (strlen($hex) == 1) 
	{ 
		$hex = "0" . $hex;
	}
	return $hex;
}


function sec2hrmn($sec) //Convert seconds to hours or minutes
{
	if ($sec > 59)
	{
		$sec = $sec / 60;
		$sec = round($sec, 3) . " hours.";
	}
	else
	{
		$sec = round($sec, 3) . " minutes.";
	}
	return $sec;
}


function writeoff() //Write light status to off in current.txt
{
	$open_file = fopen("current.txt", "w");
	$text = "0";
	fputs($open_file, $text);
	fclose($open_file);
}


function writeon() //Write light status to on in current.txt
{
	$open_file = fopen("current.txt", "w");
	$text = "1";
	fputs($open_file, $text);
	fclose($open_file);
}

#
#END FUNCTIONS
#



#
#BEGIN DAY JUDGEMENT
#

if (date("w") == 0)
{
	$offday = yes;
}
elseif (date("w") == 6)
{
	$offday = yes;
}
else
{
	$offday = no;
}

#
#END DAY JUDGEMENT
#



#
#BEGIN LIGHT JUDGEMENT
#

if ($offday == no)
{
if (time() > $sunset && time() > $pmoff) //The sun has set and it's bed time
{
	$changelights = off;
	echo "The sun has set (or it is later than the offset) but it's after the time to turn the lights off.<br><br>The lights will be turning on in ";
	$temp = ($tsunrise - time()) / 60;
	echo sec2hrmn($temp);
}
elseif (time() >= $sunset && time() < $pmoff) //The sun has set and it's not time to turn the lights off yet
{
	$changelights = on;
	echo "The sun has set (or it is later than the offset) and it's not time to turn the lights off yet.<br><br>The lights will be turning off in ";
	$temp = ($pmoff - time()) / 60;
	echo sec2hrmn($temp);
}
elseif (time() > $sunrise && time() > $amoff) //The sun has risen but it's daytime
{
	$changelights = off;
	echo "It is currently daytime (or it is later than the offset).<br><br>The lights will be turning on in ";
	$temp = ($sunset - time()) / 60;
	echo sec2hrmn($temp);
}
elseif (time() > $sunrise && time() < $amoff) //The sun has risen and it's not time to turn the lights off yet
{
	$changelights = on;
	echo "The sun has risen (or it is later than the offset) and it's not time to turn the lights off yet.<br><br>The lights will be turning off in ";
	$temp = ($amoff - time()) / 60;
	echo sec2hrmn($temp);
}
elseif (time() < $sunrise) //It's early morning before sunrise
{
	$changelights = off;
	echo "It is currently early morning (or it is later than the offset) so the lights are off.<br><br>The lights will be turning on in ";
	$temp = ($sunrise - time()) / 60;
	echo sec2hrmn($temp);
}
}
else
{
	$changelights = off;
	echo "Today is an off day so the lights will be off.";
}

#
#END LIGHT JUDGEMENT
#



#
#BEGIN MANUAL/AUTO CONTROL
#

$currentarray = file("current.txt");
$currentlights = $currentarray[0];

if ($manual == on)
{
	$lights = on;
	echo '<br><img src="lb-on.jpg" alt="Lights are on." border="0" width="35" height="38">';
}
elseif ($manual == off)
{
	$lights = off;
	echo '<br><img src="lb-off.jpg" alt="Lights are off." border="0" width="35" height="38">';
}
elseif ($changelights == off)
{
	if ($currentlights == '1') { $lights = off; }
	writeoff();
	echo '<br><img src="lb-off.jpg" alt="Lights are off." border="0" width="35" height="38">';
}
elseif ($changelights == on)
{
	if ($currentlights == '0') { $lights = on; }
	writeon();
	echo '<br><img src="lb-on.jpg" alt="Lights are on." border="0" width="35" height="38">';
}

#
#END MANUAL/AUTO CONTROL
#



#
#BEGIN SERVO CONTROL
#

if ($lights == on)
{
	$ch = "14";			//Servo Channel
	$pos = "66";			//Position A
	$ramp = "00";			//Ramp
	moveservo($ch,$pos,$ramp);	//Move the servo to position A
	$pos = "56";			//Position B
	usleep(500000);		//Wait
	moveservo($ch,$pos,$ramp);	//Move the servo to position B
	setholidayon();		//Set Holiday On
	setlastaction();		//Update last action
}
elseif ($lights == off)
{
	$ch = "14";			//Servo Channel
	$pos = "45";			//Position A
	$ramp = "00";			//Ramp
	moveservo($ch,$pos,$ramp);	//Move the servo to position A
	$pos = "56";			//Position B
	usleep(500000);		//Wait
	moveservo($ch,$pos,$ramp);	//Move the servo to position B
	setholidayoff();		//Set Holiday Off
	setlastaction();		//Update last action
}

#
#END SERVO CONTROL
#
?><br>Manual Override<br><a href="?manual=on">Lights On</a><br><a href="?manual=off">Lights Off</a></center></body></html>