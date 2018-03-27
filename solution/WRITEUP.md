
# Solution  

En regardant les sources on voit que le login.php lit le fichier `configs/account.json`
Ce fichier est accessible en web, on peut donc récupérer le login et le hash sha1 du l'administrateur

```
User: admin
Hash: f17c99cf95443a9a8768547fb91377a6da9f2a6e
```

Ce hash semble être introuvable sur internet.

En regardant l'implémentation dans `sha1.php`

Si on essaye d'appeler la fonction de hash avec des chaines dont nous connaissont le hash, on s'aperçoit qu'elle renvoi de vrai sha1.

En regardant de plus prêt on peut s'apercevoir qu'elle est étrange.

Le format string renvoyant le hash est celle-ci

```php
return sprintf("%08$x%08$x%08$x%08$x%08$x", $p0, $p1, $p2, $p3, $p4);
```

Il est bizarre que $x soit utilisé dans des doubles quotes, c'est donc une variable PHP qui est défini plus haut.

```php
$x = $z == 0xa ? chr(115) : chr(120);
```

0xa est égale à 10
chr(115) vaut 's'
chr(120) vaut 'x'

Suivant la valeur de $z, soit un %s ou %x est fait.

Si on regarde ce que vaut $p0 à $p4.
```php
$p0 = $z == 10 ? $B[0] : (($p0 + $a) & 0xffffffff);
$p1 = $z == 10 ? $B[1] : (($p1 + $b) & 0xffffffff);
$p2 = $z == 10 ? $B[2] : (($p2 + $c) & 0xffffffff);
$p3 = $z == 10 ? $B[3] : (($p3 + $d) & 0xffffffff);
$p4 = $z == 10 ? $B[4] : (($p4 + $e) & 0xffffffff);
```

On remarque que encore une fois, $z est comparé à 10.

Si on regarde $B
```php
$B = str_split(str_pad(substr($input, 10), 40), 8);
```

Dans l'ordre, on prend notre chaine d'entrée en supprimant les 10 premiers caractères (substr).
Si la chaine fait moins de 40 caracteres, elle sera agrandi à une chaine de taille 40 en étant complété par des espaces (str_pad).
On split ensuite cette chaine en paquet de 8 caracteres, ce qui donnera nos $B[0] à $[4] (Voir plus si la chaine fait plus de 40 caracteres).

On comprend bien que il est donc possible de faire retourné à cette fonction de hash, les 40 caractères de notre choix (pratique c'est la taille d'un hash).

Regardons maintenant d'où vient $z:

```php
if ($i < 10) {
	if (@sha1x(4, ord($input[$i]), $c, $d) == (movr($i / 4, $c0, $c1, $c2) & 0xff)) {
		$z++;
	}
	$i++;
}
```

Pour les 10 premiers caractères, on fait une opération, qui si elle est vrai, incrémente $z.
Il faut donc qu'elles soit vrai 10 fois pour qu'on puisse retourner ce qu'on veut.
Regardons du coté de `sha1x`.

```php
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
```

```php
@sha1x(4, ord($input[$i]), $c, $d)
```
On voit donc que le seul cas qui nous interesse est le `case 4`, et il fait juste un xor avec `0x2a` de nos 10 premiers caracteres.
Et notre input xoré est comparé avec le retour de la fonction movr.

Regardons maintenant `movr`
```php
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
```

Cette fonction fait définitivement des choses obscures ^^, le plus facile est juste de print en l'appelant 10 fois sans oublié de xor son résultat à `0x2a` pour retrouver la valeur original.

```php
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

// On copie les variables que la fonction utilis
$c0 = 1226721361;
$c1 = 16862785;
$c2 = 725571416;

// On l'appel 10 fois exactement de la même façon que dans le code original
for ($i = 0; $i < 10; $i++) {
	$v = (movr($i / 4, $c0, $c1, $c2) & 0xff);
	// On xor la valeur avec 0x2a et on la transforme en char pour l'afficher
	echo chr($v ^ 0x2a);
}

```

On lance le code qui nous affiche alors `{b4ckd++r}`.
Ce sont donc les 10 premiers caractères à donner comme mot de passe pour que la fonction de hash nous retournent les 40 caractères suivants.

Dans la page de login on donne donc comme login `admin` et comme mot de passe `{b4ckd++r}f17c99cf95443a9a8768547fb91377a6da9f2a6e`

Et voilà, nous sommes maintenant administrateur et nous avons le flag qui est `THC{1nv1s1ble_b4ckdoor1ng_4lgor1thm_1s_e4sy}`.

Faite attention en prenant du code sur internet, il peut y avoir une backdoor :)

