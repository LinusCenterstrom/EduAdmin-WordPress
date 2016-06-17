<?php
function edu_getQueryString($prepend = "?", $removeParameters = array())
{
	foreach($removeParameters as $par)
	{
		unset($_GET[$par]);
	}
	if(!empty($_GET)) { return $prepend . http_build_query($_GET); }
	return "";
}

if(!function_exists('edu_ConvertToMoney'))
{
	function edu_ConvertToMoney($value, $currency = "SEK", $decimal = ',', $thousand = ' ')
	{
		$d = $value;
		if(empty($d))
			$d = 0;
		$d = sprintf('%1$s %2$s', number_format($d, 0, $decimal, $thousand), $currency);
		return $d;
	}
}

if(!function_exists('edu_encrypt'))
{
	function edu_encrypt($key, $toEncrypt)
	{
	    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $toEncrypt, MCRYPT_MODE_CBC, md5(md5($key))));
	}
}

if(!function_exists('edu_decrypt'))
{
	function edu_decrypt($key, $toDecrypt)
	{
	    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($toDecrypt), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	}
}

if(!function_exists('edu_DecryptApiKey'))
{
	function edu_DecryptApiKey($key) {
		$decrypted = explode('|', base64_decode($key));
		if(count($decrypted) == 2)
		{
			$apiKey = new stdClass();
			$apiKey->UserId = $decrypted[0];
			$apiKey->Hash = $decrypted[1];
			return $apiKey;
		}
		return false;
	}
}

function edu_getSpotsLeft($freeSpots, $maxSpots, $spotOption = 'exactNumbers', $spotSettings = "1-5\n5-10\n10+", $alwaysFewSpots = 3)
{
	if($maxSpots === 0)
		return edu__('Spots left');

	if($freeSpots === 0)
		return edu__('No spots left');

	switch($spotOption)
	{
		case "exactNumbers":
			return sprintf(edu_n('%1$d spot left', '%1$d spots left', $freeSpots), $freeSpots);
		case "onlyText":
			return ($freeSpots > 0 ? ($freeSpots > 5 ? edu__('Spots left') : edu__('Few spots left')) : edu__('No spots left'));
		case "intervals":
			$interval = $spotSettings;
			if(empty($interval)) {
				return sprintf(edu_n('%1$s spot left', '%1$s spots left', $freeSpots), $freeSpots);
			} else {
				$lines = explode("\n", $interval);
				foreach($lines as $line)
				{
					if(stripos($line, '-') > -1) {
						$range = explode("-", $line);
						$min = $range[0];
						$max = $range[1];
						if($freeSpots <= $max && $freeSpots >= $min) {
							return sprintf(edu__('%1$s spots left'), $line);
						}
					} else if(stripos($line, '+') > -1) {
						return sprintf(edu__('%1$s spots left'), $line);
					}
				}
				return sprintf(edu_n('%1$s spot left', '%1$s spots left', $freeSpots), $freeSpots);
			}

		case "alwaysFewSpots":
			$minParticipants = $alwaysFewSpots;
			if(($maxSpots - $freeSpots) >= $minParticipants)
				return edu__('Few spots left');
			return edu__('Spots left');
	}
}

function edu_GetDisplayDate($inDate, $short = false)
{
	$months = array(
		1 => !$short ? edu__('january'): edu__('jan'),
		2 => !$short ? edu__('february') : edu__('feb'),
		3 => !$short ? edu__('march') : edu__('mar'),
		4 => !$short ? edu__('april') : edu__('apr'),
		5 => !$short ? edu__('may') : edu__('may_short'),
		6 => !$short ? edu__('june') : edu__('jun'),
		7 => !$short ? edu__('july') : edu__('jul'),
		8 => !$short ? edu__('august') : edu__('aug'),
		9 => !$short ? edu__('september') : edu__('sep'),
		10 => !$short ? edu__('october') : edu__('oct'),
		11 => !$short ? edu__('november') : edu__('nov'),
		12 => !$short ? edu__('december') : edu__('dec')
	);

	$year = date('Y', strtotime($inDate));
	$nowYear = date('Y');
	return '<span style="white-space: nowrap;">' . date('d', strtotime($inDate)) . ' ' . $months[date('n', strtotime($inDate))] . ($nowYear != $year ? ' ' . $year : '') . '</span>';
}

function edu_GetStartEndDisplayDate($startDate, $endDate, $short = false)
{
	$months = array(
		1 => !$short ? edu__('january'): edu__('jan'),
		2 => !$short ? edu__('february') : edu__('feb'),
		3 => !$short ? edu__('march') : edu__('mar'),
		4 => !$short ? edu__('april') : edu__('apr'),
		5 => !$short ? edu__('may') : edu__('may_short'),
		6 => !$short ? edu__('june') : edu__('jun'),
		7 => !$short ? edu__('july') : edu__('jul'),
		8 => !$short ? edu__('august') : edu__('aug'),
		9 => !$short ? edu__('september') : edu__('sep'),
		10 => !$short ? edu__('october') : edu__('oct'),
		11 => !$short ? edu__('november') : edu__('nov'),
		12 => !$short ? edu__('december') : edu__('dec')
	);

	$startYear = date('Y', strtotime($startDate));
	$startMonth = date('n', strtotime($startDate));
	$endYear = date('Y', strtotime($endDate));
	$endMonth = date('n', strtotime($endDate));
	$nowYear = date('Y');
	$str =  '<span style="white-space: nowrap;">';
	$str .= date('d', strtotime($startDate));
	if(date('Y-m-d', strtotime($startDate)) != date('Y-m-d', strtotime($endDate)))
	{
		if($startYear === $endYear)
		{
			if($startMonth === $endMonth)
			{
				$str .= ' - ' . date('d', strtotime($endDate));
				$str .= ' ';
				$str .= $months[date('n', strtotime($startDate))];
				$str .= ($nowYear != $startYear ? ' ' . $startYear : '');
			}
			else
			{
				$str .= ' ';
				$str .= $months[date('n', strtotime($startDate))];
				$str .= ' - ' . date('d', strtotime($endDate));
				$str .= ' ';
				$str .= $months[date('n', strtotime($endDate))];
				$str .= ($nowYear != $startYear ? ' ' . $startYear : '');
			}
		}
		else
		{
				$str .= ' ';
				$str .= $months[date('n', strtotime($startDate))];
				$str .= ($nowYear != $startYear ? ' ' . $startYear : '');
				$str .= ' - ' . date('d', strtotime($endDate));
				$str .= ' ';
				$str .= $months[date('n', strtotime($endDate))];
				$str .= ($nowYear != $endYear ? ' ' . $endYear : '');
		}
	}
	else
	{
		$str .= ' ';
		$str .= $months[date('n', strtotime($startDate))];
		$str .= ($nowYear != $startYear ? ' ' . $startYear : '');
	}


	$str .= '</span>';

	return $str;
}

function edu_DateComparer($a, $b)
{
	$aDate = date("Y-m-d H:i:s", strtotime($a->PeriodStart));
	$bDate = date("Y-m-d H:i:s", strtotime($b->PeriodStart));
	if($aDate === $bDate) {
		return 0;
	}

	return ($aDate < $bDate ? -1 : 1);
}

function edu_KeySort($key)
{
	return function ($a, $b) use ($key) {
		return strcmp($a->{$key},$b->{$key});
	};
}

function edu__($key)
{
	global $_SESSION;
	$phrases = $_SESSION['eduadmin-phrases'];

	if(!array_key_exists($key, $phrases))
	{
		$phrases[$key] = $key;
	}

	return $phrases[$key];
}

function edu_e($key)
{
	echo edu__($key);
}

function edu_n($single, $plural, $number)
{
	return $number === 1 ? edu__($single) : edu__($plural);
}


// Credits go to https://code.google.com/p/php-slugs/
if(!function_exists('my_str_split'))
{
	function my_str_split($string)
	{
		$slen = strlen($string);
		for($i = 0; $i < $slen; $i++)
		{
			$sArray[$i] = $string{$i};
		}
		return $sArray;
	}
}

if(!function_exists('noDiacritics'))
{
	function noDiacritics($string)
	{
		//cyrylic transcription
		$cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$cyrylicTo   = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');

		$from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
		$to   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");


		$from = array_merge($from, $cyrylicFrom);
		$to   = array_merge($to, $cyrylicTo);

		$newstring=str_replace($from, $to, $string);
		return $newstring;
	}
}

if(!function_exists('makeSlugs'))
{
	function makeSlugs($string, $maxlen=0)
	{
		$newStringTab = array();
		$string = strtolower(noDiacritics($string));
		if(function_exists('str_split'))
		{
			$stringTab = str_split($string);
		}
		else
		{
			$stringTab = my_str_split($string);
		}

		$numbers = array("0","1","2","3","4","5","6","7","8","9","-");

		foreach($stringTab as $letter)
		{
			if(in_array($letter, range("a", "z")) || in_array($letter, $numbers))
			{
				$newStringTab[] = $letter;
	 		}
			elseif($letter === " ")
			{
				$newStringTab[] = "-";
			}
		}

		if(!empty($newStringTab))
		{
			$newString = implode($newStringTab);
			if($maxlen > 0)
			{
	        	$newString = substr($newString, 0, $maxlen);
			}

			$newString = removeDuplicates('--', '-', $newString);
		}
		else
		{
			$newString = '';
		}

		return $newString;
	}
}

if(!function_exists('checkSlug'))
{
	function checkSlug($sSlug)
	{
		if(ereg("^[a-zA-Z0-9]+[a-zA-Z0-9\_\-]*$", $sSlug))
		{
			return true;
		}

		return false;
	}
}

if(!function_exists('removeDuplicates'))
{
	function removeDuplicates($sSearch, $sReplace, $sSubject)
	{
		$i = 0;
		do
		{
			$sSubject = str_replace($sSearch, $sReplace, $sSubject);
			$pos = strpos($sSubject, $sSearch);

			$i++;
			if($i > 100)
			{
				die('removeDuplicates() loop error');
			}
		}
		while($pos !== false);

		return $sSubject;
	}
}
?>