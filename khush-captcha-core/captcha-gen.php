<?php
/*
License: GPL2

  Copyright 2013  khushapps.com : khushapps@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.q

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
session_start();
header("Content-Type: image/jpeg");

function hex2rgb($hex) {
	$hex = str_replace("#", "", $hex);

	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
	//return implode(",", $rgb); // returns the rgb values separated by commas
	return $rgb; // returns an array with the rgb values
}

switch ($_SESSION['color_text'])
{
	case "red":
		$hexval = '#FF0000';
		break;
	case "blue":
		$hexval = '#0000FF';
		break;
	case "green":
		$hexval = '#00FF00';
		break;
	default:
		$hexval = '#FF0000'; // red
}

$answer_color_rgb = hex2rgb($hexval);

function getCaptcha($color,$new_input,$length=24) {
	$xrandom = rand(2,$length);
	if ($xrandom <= 2) {
		$pos2calcnum = 14 ;
		$_SESSION['$pos2'] = $xrandom * $pos2calcnum;
		$_SESSION['$pos3'] = $xrandom * $pos2calcnum * 2.7;
	} elseif($xrandom <= 5) {
		$pos2calcnum = 12;
		$_SESSION['$pos2'] = $xrandom * $pos2calcnum;
		$_SESSION['$pos3'] = $xrandom * $pos2calcnum * 2.2;
	} elseif($xrandom > 5)  {
		$pos2calcnum = 10;
		$_SESSION['$pos2'] = $xrandom * $pos2calcnum;
		$_SESSION['$pos3'] = $xrandom * $pos2calcnum * 1.8;
	} elseif($xrandom >= 8)  {
		$pos2calcnum = 10;
		$_SESSION['$pos2'] = $xrandom * $pos2calcnum;
		$_SESSION['$pos3'] = $xrandom * $pos2calcnum * 1.1;
	}
	$new_random = $xrandom + 1;

	$whats_left = $length - $xrandom;
	$new_input = substr($new_input,0,$length);
	$new_input1 = substr($new_input,0,$xrandom);
	$new_input2 = substr($new_input,$new_random,$whats_left);
	$code1 = str_split($new_input1);
	$code2 = str_split($new_input2);
	$i = 0;
	$_SESSION['capstr'] = '';
	$_SESSION['capstr2'] = '';
	$_SESSION['capstr3'] = '';
	$_SESSION['xrandom'] = '';
	$_SESSION['answer'] = '';
	foreach ($code1 as $val) {
		$_SESSION['capstr'] .= $val;
		$a = $i + 2;
		if($a == $xrandom) {
			$_SESSION['answer'] = substr(md5(rand(10000,99999)),0,5);
		} else {
			//echo $val;
		}
		$i++;
	}
	foreach ($code2 as $val) {
		$_SESSION['capstr2'] .= $val;
		$i++;
	}
} // end getCaptcha

// values for getCaptcha()
$random_val = rand(100,10000);
$new_input = md5($random_val);
$characters = 12;
// call Captcha Generate Function
getCaptcha($_SESSION['color'],$new_input,$characters);

$height =40;
$width = 250;
$image_p = imagecreate($width, $height);
$black = imagecolorallocate($image_p, 0, 0, 0);
$white = imagecolorallocate($image_p, 255, 255, 255);
$answer_color = imagecolorallocate($image_p, $answer_color_rgb[0], $answer_color_rgb[1], $answer_color_rgb[2]);
$font_size = 14;
$text1 = $_SESSION['capstr'];
$text2 = $_SESSION['answer'];
$_SESSION['khush_captcha'] = $_SESSION["answer"];
$text3 = $_SESSION['capstr2'];
$position2 = $_SESSION['$pos2'];
$position3 = $_SESSION['$pos3'];
imagestring($image_p, $font_size, 5, 5, $text1, $white);
imagestring($image_p, $font_size, $position2, 5, $text2, $answer_color);
imagestring($image_p, $font_size, $position3, 5, $text3, $white);
imagejpeg($image_p, null, 80);

//remove image from memory
ImageDestroy($image_p);
?>
