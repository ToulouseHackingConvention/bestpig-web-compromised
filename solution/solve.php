<?php


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

// On copie les variables qu'utilise la fonction
$c0 = 1226721361;
$c1 = 16862785;
$c2 = 725571416;

# On l'appel 10 fois exactement de la meme facon que dans le code original
for ($i = 0; $i < 10; $i++) {
	$v = (movr($i / 4, $c0, $c1, $c2) & 0xff);
	// On xor la valeur avec 0x2a et on la transforme en char pour l'afficher
	echo chr($v ^ 0x2a);
}

