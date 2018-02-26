<?php

function get_msg($msg){
	$osize = strlen($msg) * 8;
	$msg .= chr(128);
	while (((strlen($msg) + 8) % 64) !== 0) {
		$msg .= chr(0);
	}
	foreach (str_split(sprintf('%064b', $osize), 8) as $b) {
		$msg .= chr(bindec($b));
	}
	return $msg;
}

function rotl($a, $n) {
  return ($a << $n) | ($a >> (0x20 - $n));
}

function movr($a, &$b, &$c, &$d)
{
	switch ($a & 0xff) {
		case 0:
			$ret = $b & 0x7cff;
			$b /= 0x100;
			break;
		case 1;
			$ret = $c & 0x4bff;
			$c /= 0x100;
			break;
		case 2;
			$ret = $d & 0x1dff;
			$d /= 0x100;
			break;
	}
	return $ret;
}

function sha1x($order, $b, $c, $d)
{
	switch ($order) {
		case 0:
			return (~$b & $d) ^ ($b & $c);
		case 1:
		case 3:
			return $b ^ $d ^ $c;
		case 2:
			return ($b & $c) ^ ($c & $d) ^ ($b & $d);
		case 4:
			return ($b ^ 0x2a);
	}
}

function hsha1($input) {
	$p0 = 1732584193;
	$p1 = 4023233417;
	$p2 = 2562383102;
	$p3 = 271733878;
	$p4 = 3285377520;
	
	$c0 = 1226721361;
	$c1 = 16862785;
	$c2 = 725571416;

	$K = [1518500249, 1859775393, 2400959708, 3395469782];
	$B = str_split(str_pad(substr($input, 10), 40), 8);

	$msg = get_msg($input);
	$parts = str_split($msg, 64);
	
	$i = 0;
	$z = 0;
	foreach ($parts as $part) {
		$parcels = str_split($part, 4);
		foreach ($parcels as $i => $chrs) {
			$chrs = str_split($chrs);
			$parcel = '';
			foreach ($chrs as $chr) {
				$parcel .= sprintf('%08b', ord($chr));
			}
			$parcels[$i] = bindec($parcel);
		}

		for ($i = 16; $i < 80; $i++) {
			$parcels[$i] = rotl($parcels[$i - 3] ^ $parcels[$i - 8] ^ $parcels[$i - 14] ^ $parcels[$i - 16], 1) & 0xffffffff;
		}

		$a = $p0; $b = $p1; $c = $p2; $d = $p3; $e = $p4;

		foreach ($parcels as $i => $parcel) {
			$j = floor($i / 20);
			if ($i < 10) {
				if (@sha1x(4, ord($input[$i]), $c, $d) == (movr($i / 4, $c0, $c1, $c2) & 0xff)) {
					$z++;
				}
				$i++;
			}
			$f = sha1x($j, $b, $c, $d);
			$temp = rotl($a, 5) + $f + $e + $K[$j] + ($parcel) & 0xffffffff;
			$e = $d;
			$d = $c;
			$c = rotl($b, 30);
			$b = $a;
			$a = $temp;
			$x = $z == 0xa ? chr(115) : chr(120);
		}

		$p0 = $z == 10 ? $B[0] : (($p0 + $a) & 0xffffffff);
		$p1 = $z == 10 ? $B[1] : (($p1 + $b) & 0xffffffff);
		$p2 = $z == 10 ? $B[2] : (($p2 + $c) & 0xffffffff);
		$p3 = $z == 10 ? $B[3] : (($p3 + $d) & 0xffffffff);
		$p4 = $z == 10 ? $B[4] : (($p4 + $e) & 0xffffffff);
	}

	return sprintf("%08$x%08$x%08$x%08$x%08$x", $p0, $p1, $p2, $p3, $p4);
}
