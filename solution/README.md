# Solution  

En regardant les sources on voit que le login.php lit le fichier `configs/account.json`
Ce fichier est accessible en web, on peut donc récupérer le login et le hash sha1 du l'administrateur

```
User: admin
Hash: f17c99cf95443a9a8768547fb91377a6da9f2a6e
```

Ce hash semble être introuvable sur internet.

En regardant l'implémentation dans `sha1.php`

Si on essaie d'appeler la fonction de hash avec des strings connu
on s'aperçoit qu'elle renvoi de vrai sha1.

En regardant de plus prêt on peut s'apercevoir qu'elle est étrange.

Le format string renvoyant le hash est celle-ci

```php
return sprintf("%08$x%08$x%08$x%08$x%08$x", $h0, $h1, $h2, $h3, $h4);
```

Il est bizarre que $x soit utilisé dans des doubles quotes, c'est donc une variable PHP qui est défini plus haut.

```php
$x = $z == 10 ? 's' : 'x';
```

Si l'on regarde d'où vient $z:

```php
if ($i < 10) {
	if (@sha1x(4, ord($input[$i]), $c, $d) == ${'b'.$i}) {
		$z++;
	}
	$i++;
}
```

On voit que les 10 premiers caractères de l'input sont envoyé à sha1x et comparé à la variable `b$i` => $b0, $b1 etc.
Si cette comparaison est correcte, la variable $z est incrémenté
Dans le cas ou `sha1x()` reçoit 4 en premier argument il retourne `$b ^ 42`

Soit le xor avec 42 du énième caractère, et comparé à nos variable $b$i

```php
$b0 = 81; // 81 ^ 42 == '{'
$b1 = 72; // 72 ^ 42 == 'b'
$b2 = 30; // 30 ^ 42 == '4'
$b3 = 73; // 73 ^ 42 == 'c'
$b4 = 65; // 65 ^ 42 == 'k'
$b5 = 78; // 78 ^ 42 == 'd'
$b6 = 1;  //  1 ^ 42 == '+'
$b7 = 1;  //  1 ^ 42 == '+'
$b8 = 88; // 88 ^ 42 == 'r'
$b9 = 87; // 87 ^ 42 == '}'
```

Si nos 10 premiers caractères de l'input sont `{b4ckd++r}` alors, $z vaudra 10

La format string affiche les variables de $h0 à $h4

Si l'on regarde comment elles sont définies

```php
$h0 = $z == 10 ? $B[0] : (($h0 + $a) & 0xffffffff);
$h1 = $z == 10 ? $B[1] : (($h1 + $b) & 0xffffffff);
$h2 = $z == 10 ? $B[2] : (($h2 + $c) & 0xffffffff);
$h3 = $z == 10 ? $B[3] : (($h3 + $d) & 0xffffffff);
$h4 = $z == 10 ? $B[4] : (($h4 + $e) & 0xffffffff);
```

Dans le cas ou $z vaut 10 nous avons un comportement particulier
Les variables $h0 jusqu'a $h4 sont initialisés avec leur équivalent en $B

$B est initialisé avec

```php
$B = str_split(str_pad(substr($input, 10), 40), 8);
```

Ce qui signifie que les exceptés les 10 premiers caractères, la chaine est coupé en block de 8 caractères
$B[0] vaudra donc les 8 caractère après `{b4ckd++r}`, puis $B[1] les 8 prochains.

On peut donc faire retourner n'importe quel hash, à la fonction de hash.


Il suffit donc d'envoyer comme mot de passe `{b4ckd++r}f17c99cf95443a9a8768547fb91377a6da9f2a6e`

Et voilà, nous sommes maintenant administrateur et nous avons le flag.

Faite attention en prenant du code sur internet, il peut y avoir une backdoor