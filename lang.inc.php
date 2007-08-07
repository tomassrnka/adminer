<?php
static $langs = array(
	'en' => 'English',
	'cs' => 'Čeština', // Jakub Vrána - http://php.vrana.cz
	'sk' => 'Slovenčina', // Ivan Suchy - http://www.ivansuchy.com
	'nl' => 'Nederlands', // Maarten Balliauw - http://blogs.balliauw.be/blogs/maarten/
);

function lang($idf, $number = null) {
	global $LANG, $translations;
	$translation = $translations[$idf];
	if (is_array($translation) && $translation) {
		switch ($LANG) {
			case 'cs': $pos = ($number == 1 ? 0 : (!$number || $number >= 5 ? 2 : 1)); break;
			case 'sk': $pos = ($number == 1 ? 0 : (!$number || $number >= 5 ? 2 : 1)); break;
			default: $pos = ($number == 1 ? 0 : 1);
		}
		$translation = $translation[$pos];
	}
	$args = func_get_args();
	array_shift($args);
	return vsprintf(($translation ? $translation : $idf), $args);
}

function switch_lang() {
	global $langs;
	echo "<p>" . lang('Language') . ":";
	$base = remove_from_uri("lang");
	foreach ($langs as $lang => $val) {
		echo ' <a href="' . htmlspecialchars($base . (strpos($base, "?") !== false ? "&" : "?")) . "lang=$lang\" title='$val'>$lang</a>";
	}
	echo "</p>\n";
}

if (isset($_GET["lang"])) {
	$_COOKIE["lang"] = $_GET["lang"];
	$_SESSION["lang"] = $_GET["lang"];
}

if (isset($langs[$_COOKIE["lang"]])) {
	setcookie("lang", $_GET["lang"], strtotime("+1 month"), preg_replace('~\\?.*~', '', $_SERVER["REQUEST_URI"]));
	$LANG = $_COOKIE["lang"];
} elseif (isset($langs[$_SESSION["lang"]])) {
	$LANG = $_SESSION["lang"];
} else {
	$accept_language = array();
	preg_match_all('~([-a-z_]+)(;q=([0-9.]+))?~', strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]), $matches, PREG_SET_ORDER);
	foreach ($matches as $match) {
		$accept_language[str_replace("_", "-", $match[1])] = (isset($match[3]) ? $match[3] : 1);
	}
	arsort($accept_language);
	$LANG = "en";
	foreach ($accept_language as $lang => $q) {
		if (isset($langs[$lang])) {
			$LANG = $lang;
			break;
		}
		$lang = preg_replace('~-.*~', '', $LANG);
		if (!isset($accept_language[$lang]) && isset($langs[$lang])) {
			$LANG = $lang;
			break;
		}
	}
}
