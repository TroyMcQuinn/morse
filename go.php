<?
//Morse code generator by TMC.  Coded on Monday, June 5, 2006
//Output file will be an 8-bit, 4kHz, mono wav file.
if(isset($_POST["text"])){
	header('Content-type: audio/x-wav');
	header('Content-Disposition: attachment;filename=morse.wav');
	$vol = $_POST["volume"]; //volume, or amplitude of wave.  From zero to 255.
	$vol = ceil(128 * ($vol / 100));
	$frequency = $_POST["frequency"]; //Frequency to use, in Hertz.
	$samplerate = $_POST["samplerate"]; //sample rate of wav file to be produced.
	$wpm = $_POST["speed"]; //Adjust speed by adjusting length of pauses
	$reps = ceil(($samplerate / $frequency) / 2);  //Sets the number of repetitions for each half of the square wave.
	$speed = floor($reps / 2); //Compensate speed for higher sample rates
	$mid = 128; //zero is value 128.  Wave will be 128 + and - volume level.
	$eps = ($wpm * 50) / 60;  //elements per second
	$tslen = $samplerate / $eps; //Samples per element.	
	$t = ""; $s = "";
	$i = 1;
	while($i <= ($tslen + 1)){
		for($x = 0; $x < $reps; $x++){
			$t .= chr($mid + $vol);
			$i++;
		}
		for($x = 0; $x < $reps; $x++){
			$t .= chr($mid - $vol);
			$i++;
		}
	}
	for($i = 0; $i < $tslen; $i++){
		$s .= chr($mid.$mid);
	}
	//Setup morse code elements
	$dot = $t.$s;
	$dash = $t.$t.$t.$s;
	$space = $s.$s.$s.$s;
	$codes = Array(
		"A" => $dot.$dash,
		"B" => $dash.$dot.$dot.$dot,
		"C" => $dash.$dot.$dash.$dot,
		"D" => $dash.$dot.$dot,
		"E" => $dot,
		"F" => $dot.$dot.$dash.$dot,
		"G" => $dash.$dash.$dot,
		"H" => $dot.$dot.$dot.$dot,
		"I" => $dot.$dot,
		"J" => $dot.$dash.$dash.$dash,
		"K" => $dash.$dot.$dash,
		"L" => $dot.$dash.$dot.$dot,
		"M" => $dash.$dash,
		"N" => $dash.$dot,
		"O" => $dash.$dash.$dash,
		"P" => $dot.$dash.$dash.$dot,
		"Q" => $dash.$dash.$dot.$dash,
		"R" => $dot.$dash.$dot,
		"S" => $dot.$dot.$dot,
		"T" => $dash,
		"U" => $dot.$dot.$dash,
		"V" => $dot.$dot.$dot.$dash,
		"W" => $dot.$dash.$dash,
		"X" => $dash.$dot.$dot.$dash,
		"Y" => $dash.$dot.$dash.$dash,
		"Z" => $dash.$dash.$dot.$dot,
		"Á" => $dot.$dash.$dash.$dot.$dash,
		"Ä" => $dot.$dash.$dot.$dash,
		"É" => $dot.$dot.$dash.$dot.$dot,
		"Ñ" => $dash.$dash.$dot.$dash.$dash,
		"Ö" => $dash.$dash.$dash.$dot,
		"Ü" => $dot.$dot.$dash.$dash,
		"1" => $dot.$dash.$dash.$dash.$dash,
		"2" => $dot.$dot.$dash.$dash.$dash,
		"3" => $dot.$dot.$dot.$dash.$dash,
		"4" => $dot.$dot.$dot.$dot.$dash,
		"5" => $dot.$dot.$dot.$dot.$dot,
		"6" => $dash.$dot.$dot.$dot.$dot,
		"7" => $dash.$dash.$dot.$dot.$dot,
		"8" => $dash.$dash.$dash.$dot.$dot,
		"9" => $dash.$dash.$dash.$dash.$dot,
		"0" => $dash.$dash.$dash.$dash.$dash,
		"," => $dash.$dash.$dot.$dot.$dash.$dash,
		"." => $dot.$dash.$dot.$dash.$dot.$dash,
		"?" => $dot.$dot.$dash.$dash.$dot.$dot,
		";" => $dash.$dot.$dash.$dot.$dash.$dot,
		":" => $dash.$dash.$dash.$dot.$dot.$dot,
		"/" => $dash.$dot.$dot.$dash.$dot,
		"-" => $dash.$dot.$dot.$dot.$dot.$dash,
		"'" => $dot.$dash.$dash.$dash.$dash.$dot,
		"()" => $dash.$dot.$dash.$dash.$dot.$dash,
		"_" => $dot.$dot.$dash.$dash.$dot.$dash,
		" " => $space
	);
	$phrase = $_POST["text"]; //Get user input as phrase to convert to morse code.
	$output = $h; //Begin building wav file
	for($i = 0; $i <= strlen($phrase); $i++){
		$wavdata .= $codes[strtoupper($phrase[$i])].$s.$s.$s;
	}
	//Create header
	//Create first subchunk
	$subchunk1 .= pack("NVvvVVvv", 0x666d7420, 16, 1, 1, $samplerate, $samplerate, 1, 8);
	//Create second subchunk
	$subchunk2 = pack("NV", 0x64617461, strlen($wavdata));
	$subchunk2 .= $wavdata;
	//Build chunk descriptor
	$h = pack("NVN", 0x52494646, (36 + strlen($subchunk2)), 0x57415645);
	//Construct file
	$output = $h.$subchunk1.$subchunk2;
	
	print $output; //Output audio
}else{
?>
<form name="morsecode" action="go.php" method="POST">
	Enter text to translate to morse code: <br /><textarea name="text" id="text" cols="44" rows="10"/></textarea><br />
	<table><tr><td>
	Sample Rate: 
	</td><td>
	<select name="samplerate">
		<option value="2000">2 kHz</option>
		<option value="4000" selected>4 kHz</option>
		<option value="8000">8 kHz</option>
		<option value="11025">11.025 kHz</option>
		<option value="16000">16 kHz</option>
		<option value="22050">22.05 kHz</option>
		<option value="32000">32 kHz</option>
		<option value="44100">44.1 kHz</option>
		<option value="48000">48 kHz</option>
		<option value="96000">96 kHz</option>
	</select>
	</td></tr><tr><td>
	Frequency: </td><td><input type="text" name="frequency" value="1000" /> Hz
	</td></tr></tr><td>
	Volume:</td></td><td>
	<select name="volume">
		<option value="10">10%</option>
		<option value="20" selected>20%</option>
		<option value="30">30%</option>
		<option value="40">40%</option>
		<option value="50">50%</option>
		<option value="60">60%</option>
		<option value="70">70%</option>
		<option value="80">80%</option>
		<option value="90">90%</option>
		<option value="100">100%</option>
	</select>
	</td></tr><tr><td>
	Speed: 	</td><td><input type="text" name="speed" value="25" /> wpm
	</td></tr></table>
	<input type="submit" name="submit" value="Go" />
</form>
<?php
}
?>
