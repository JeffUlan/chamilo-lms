<?php
/**
 * ==============================================================================
 * File: multibyte_string_functions_internal.lib.php
 * Main API extension library for Dokeos 1.8.6+ LMS,
 * contains functions for internal use only.
 * License: GNU/GPL version 2 or later (Free Software Foundation)
 * @author: Ivan Tcholakov, ivantcholakov@gmail.com, 2009
 * @package dokeos.library
 * ==============================================================================
 * 
 * Note: All functions and data structures here are not to be used directly.
 * See the file multibyte_string_functions.lib.php which contains the "public" API.
 * 
 */

// Global variables used by some callback functions.
$_api_encoding = null;
$_api_collator = null;


/**
 * ----------------------------------------------------------------------------
 * Appendix to "Multibyte string conversion functions"
 * ----------------------------------------------------------------------------
 */

// This is a php-implementation of the function api_convert_encoding().
function _api_convert_encoding($string, $to_encoding, $from_encoding) {
	static $character_map = array();
	static $utf8_compatible = array('UTF-8', 'US-ASCII');
	if (empty($string)) {
		return $string;
	}
	$to_encoding = api_refine_encoding_id($to_encoding);
	$from_encoding = api_refine_encoding_id($from_encoding);
	if (api_equal_encodings($to_encoding, $from_encoding)) {
		return $string;
	}
	$to = _api_get_character_map_name($to_encoding);
	$from = _api_get_character_map_name($from_encoding);
	if (empty($to) || empty($from) || $to == $from || (in_array($to, $utf8_compatible) && in_array($from, $utf8_compatible))) {
		return $string;
	}
	if (!isset($character_map[$to])) {
		$character_map[$to] = _api_parse_character_map($to);
	}
	if ($character_map[$to] === false) {
		return $string;
	}
	if (!isset($character_map[$from])) {
		$character_map[$from] = _api_parse_character_map($from);
	}
	if ($character_map[$from] === false) {
		return $string;
	}
	if ($from != 'UTF-8') {
		$len = api_byte_count($string);
		$codepoints = array();
		for ($i = 0; $i < $len; $i++) {
			$ord = ord($string[$i]);
			if ($ord > 127) {
				if (isset($character_map[$from]['local'][$ord])) {
					$codepoints[] = $character_map[$from]['local'][$ord];
				} else {
					$codepoints[] = 0xFFFD; // U+FFFD REPLACEMENT CHARACTER is the general substitute character in the Unicode Standard.
				}
			} else {
				$codepoints[] = $ord;
			}
		}
	} else {
		$codepoints = _api_utf8_to_unicode($string);
	}
	if ($to != 'UTF-8') {
		foreach ($codepoints as $i => &$codepoint) {
			if ($codepoint > 127) {
				if (isset($character_map[$from]['local'][$codepoint])) {
					$codepoint = chr($character_map[$from]['local'][$codepoint]);
				} else {
					$codepoint = '?'; // Unknown character.
				}
			} else {
				$codepoint = chr($codepoint);
			}
		}
		$string = implode($codepoints);
	} else {
		$string = _api_utf8_from_unicode($codepoints);
	}
	return $string;
}

// This function determines the name of the conversion table, dealing with
// aliases if the encoding identificator.
function _api_get_character_map_name($encoding) {
	static $character_map_selector;
	if (!isset($character_map_selector)) {
		$file = dirname(__FILE__) . '/multibyte_string_database/conversion/character_map_selector.php';
		if (file_exists($file)) {
			$character_map_selector = include ($file);
		} else {
			$character_map_selector = array();
		}
	}
	return isset($character_map_selector[$encoding]) ? $character_map_selector[$encoding] : '';
}

// This function parses a given conversion table (a text file) and creates in the memory
// two tables for conversion - character set from/to Unicode codepoints.
function &_api_parse_character_map($name) {
	$result = array('local' => array(), 'unicode' => array());
	$file = dirname(__FILE__) . '/multibyte_string_database/conversion/' . $name . '.TXT';
	if (file_exists($file)) {
		$text = @file_get_contents($file);
		if ($text !== false) {
			$text = explode(chr(10), $text);
			foreach ($text as $line) {
				if (empty($line)) {
					continue;
				}
				if (!empty($line) && trim($line) && $line[0] != '#') {
					$matches = array();
					preg_match('/[[:space:]]*0x([[:alnum:]]*)[[:space:]]+0x([[:alnum:]]*)[[:space:]]+/', $line, $matches);
					$ord = hexdec(trim($matches[1]));
					if ($ord > 127) {
						$result['local'][$ord] = hexdec(trim($matches[2]));
						$result['unicode'][$result['local'][$ord]] = $ord;
					}
				}
			}
		} else {
			return false ;
		}
	} else {
		return false;
	}
	return $result;
}

/**
 * Takes an UTF-8 string and returns an array of ints representing the 
 * Unicode characters. Astral planes are supported ie. the ints in the
 * output can be > 0xFFFF. Occurrances of the BOM are ignored. Surrogates
 * are not allowed.
 * @param string $string				The UTF-8 encoded string.
 * @return array						Returns an array of unicode code points.
 * @author Henri Sivonen, mailto:hsivonen@iki.fi
 * @link http://hsivonen.iki.fi/php-utf8/
 * @author Ivan Tcholakov, 2009, modifications for the Dokeos LMS.
*/
function _api_utf8_to_unicode($string) {
	$state = 0;			// cached expected number of octets after the current octet
						// until the beginning of the next UTF8 character sequence
	$codepoint  = 0;	// cached Unicode character
	$bytes = 1;			// cached expected number of octets in the current sequence
	$result = array();
	$len = api_byte_count($string);
	for ($i = 0; $i < $len; $i++) {
		$byte = ord($string[$i]);
		if ($state == 0) {
			// When state is zero we expect either a US-ASCII character or a
			// multi-octet sequence.
			if (0 == (0x80 & ($byte))) {
				// US-ASCII, pass straight through.
				$result[] = $byte;
				$bytes = 1;
			} else if (0xC0 == (0xE0 & ($byte))) {
				// First octet of 2 octet sequence
				$codepoint = ($byte);
				$codepoint = ($codepoint & 0x1F) << 6;
				$state = 1;
				$bytes = 2;
			} else if (0xE0 == (0xF0 & ($byte))) {
				// First octet of 3 octet sequence
				$codepoint = ($byte);
				$codepoint = ($codepoint & 0x0F) << 12;
				$state = 2;
				$bytes = 3;
			} else if (0xF0 == (0xF8 & ($byte))) {
				// First octet of 4 octet sequence
				$codepoint = ($byte);
				$codepoint = ($codepoint & 0x07) << 18;
				$state = 3;
				$bytes = 4;
            } else if (0xF8 == (0xFC & ($byte))) {
				// First octet of 5 octet sequence.
				// This is illegal because the encoded codepoint must be either
				// (a) not the shortest form or
				// (b) outside the Unicode range of 0-0x10FFFF.
				// Rather than trying to resynchronize, we will carry on until the end
				// of the sequence and let the later error handling code catch it.
                $codepoint = ($byte);
                $codepoint = ($codepoint & 0x03) << 24;
                $state = 4;
                $bytes = 5;
			} else if (0xFC == (0xFE & ($byte))) {
				// First octet of 6 octet sequence, see comments for 5 octet sequence.
				$codepoint = ($byte);
				$codepoint = ($codepoint & 1) << 30;
				$state = 5;
				$bytes = 6;
			} else {
				// Current octet is neither in the US-ASCII range nor a legal first
				// octet of a multi-octet sequence.
				$state = 0;
				$codepoint = 0;
				$bytes = 1;
				$result[] = 0xFFFD; // U+FFFD REPLACEMENT CHARACTER is the general substitute character in the Unicode Standard.
				continue ;
			}
		} else {
			// When state is non-zero, we expect a continuation of the multi-octet
			// sequence
			if (0x80 == (0xC0 & ($byte))) {
				// Legal continuation.
				$shift = ($state - 1) * 6;
				$tmp = $byte;
				$tmp = ($tmp & 0x0000003F) << $shift;
				$codepoint |= $tmp;
				// End of the multi-octet sequence. $codepoint now contains the final
				// Unicode codepoint to be output
                if (0 == --$state) {
					// Check for illegal sequences and codepoints.
					// From Unicode 3.1, non-shortest form is illegal
					if (((2 == $bytes) && ($codepoint < 0x0080)) ||
						((3 == $bytes) && ($codepoint < 0x0800)) ||
						((4 == $bytes) && ($codepoint < 0x10000)) ||
						(4 < $bytes) ||
						// From Unicode 3.2, surrogate characters are illegal
						(($codepoint & 0xFFFFF800) == 0xD800) ||
						// Codepoints outside the Unicode range are illegal
						($codepoint > 0x10FFFF)) {
						$state = 0;
						$codepoint = 0;
						$bytes = 1;
						$result[] = 0xFFFD;
						continue ;
					}
					if (0xFEFF != $codepoint) {
						// BOM is legal but we don't want to output it
						$result[] = $codepoint;
					}
					// Initialize UTF8 cache
					$state = 0;
					$codepoint = 0;
					$bytes = 1;
				}
			} else {
				// ((0xC0 & (*in) != 0x80) && (state != 0))
				// Incomplete multi-octet sequence.
				$state = 0;
				$codepoint = 0;
				$bytes = 1;
				$result[] = 0xFFFD;
			}
		}
	}
	return $result;
}

/**
 * Takes an array of codepoints (integer) representing Unicode characters and returns a UTF-8 string.
 * @param array $codepoints				An array of Unicode codepoints representing a string.
 * @return string						Returns a UTF-8 string constructed using the given codepoints.
*/
function _api_utf8_from_unicode($codepoints) {
	return implode(array_map('_api_utf8_chr', $codepoints));
}

/**
 * Takes an integer value (codepoint) and returns its correspondent representing the Unicode character.
 * Astral planes are supported, ie the intger input can be > 0xFFFF. Occurrances of the BOM are ignored.
 * Surrogates are not allowed.
 * @param array $array					An array of unicode code points representing a string
 * @return string						Returns the corresponding  UTF-8 character.
 * @author Henri Sivonen, mailto:hsivonen@iki.fi
 * @link http://hsivonen.iki.fi/php-utf8/
 * @author Ivan Tcholakov, 2009, modifications for the Dokeos LMS.
 * @see _api_utf8_from_unicode()
 * This is a UTF-8 aware version of the function chr().
 * @link http://php.net/manual/en/function.chr.php
 */
function _api_utf8_chr($codepoint) {
	// ASCII range (including control chars)
	if ( ($codepoint >= 0) && ($codepoint <= 0x007f) ) {
		$result = chr($codepoint);
	// 2 byte sequence
	} else if ($codepoint <= 0x07ff) {
		$result = chr(0xc0 | ($codepoint >> 6)) . chr(0x80 | ($codepoint & 0x003f));
	// Byte order mark (skip)
	} else if($codepoint == 0xFEFF) {
		// nop -- zap the BOM
		$result = '';
	// Test for illegal surrogates
	} else if ($codepoint >= 0xD800 && $codepoint <= 0xDFFF) {
		// found a surrogate
		$result = _api_utf8_chr(0xFFFD); // U+FFFD REPLACEMENT CHARACTER is the general substitute character in the Unicode Standard.
	// 3 byte sequence
	} else if ($codepoint <= 0xffff) {
		$result = chr(0xe0 | ($codepoint >> 12)) . chr(0x80 | (($codepoint >> 6) & 0x003f)) . chr(0x80 | ($codepoint & 0x003f));
	// 4 byte sequence
	} else if ($codepoint <= 0x10ffff) {
		$result = chr(0xf0 | ($codepoint >> 18)) . chr(0x80 | (($codepoint >> 12) & 0x3f)) . chr(0x80 | (($codepoint >> 6) & 0x3f)) . chr(0x80 | ($codepoint & 0x3f));
	} else {
 		// out of range
		$result = _api_utf8_chr(0xFFFD);
	}
	return $result;
}

/**
 * Takes the first UTF-8 character in a string and returns its codepoint (integer).
 * @param string $utf8_character	The UTF-8 encoded character.
 * @return int						Returns: the codepoint; or 0xFFFD (unknown character) when the input string is empty.
 * This is a UTF-8 aware version of the function ord().
 * @link http://php.net/manual/en/function.ord.php
 * Note about a difference with the original funtion ord(): ord('') returns 0.
 */
function _api_utf8_ord($utf8_character) {
	if (empty($utf8_character)) {
		return 0xFFFD;
	}
	$codepoints = _api_utf8_to_unicode($utf8_character);
	return $codepoints[0];
}


/**
 * ----------------------------------------------------------------------------
 * Appendix to "Common multibyte string functions"
 * ----------------------------------------------------------------------------
 */

// Reads case folding properties about a given character from a file-based "database".
function _api_utf8_get_letter_case_properties($codepoint, $type = 'lower') {
	static $config = array();
	static $range = array();
	if (!isset($range[$codepoint])) {
		if ($codepoint > 128 && $codepoint < 256)  {
			$range[$codepoint] = '0080_00ff'; // Latin-1 Supplement
		} elseif ($codepoint < 384) {
			$range[$codepoint] = '0100_017f'; // Latin Extended-A
		} elseif ($codepoint < 592) {
			$range[$codepoint] = '0180_024F'; // Latin Extended-B
		} elseif ($codepoint < 688) {
			$range[$codepoint] = '0250_02af'; // IPA Extensions
		} elseif ($codepoint >= 880 && $codepoint < 1024) {
			$range[$codepoint] = '0370_03ff'; // Greek and Coptic
		} elseif ($codepoint < 1280) {
			$range[$codepoint] = '0400_04ff'; // Cyrillic
		} elseif ($codepoint < 1328) {
			$range[$codepoint] = '0500_052f'; // Cyrillic Supplement
		} elseif ($codepoint < 1424) {
			$range[$codepoint] = '0530_058f'; // Armenian
		} elseif ($codepoint >= 7680 && $codepoint < 7936) {
			$range[$codepoint] = '1e00_1eff'; // Latin Extended Additional
		} elseif ($codepoint < 8192) {
			$range[$codepoint] = '1f00_1fff'; // Greek Extended
		} elseif ($codepoint >= 8448 && $codepoint < 8528) {
			$range[$codepoint] = '2100_214f'; // Letterlike Symbols
		} elseif ($codepoint < 8592) {
			$range[$codepoint] = '2150_218f'; // Number Forms
		} elseif ($codepoint >= 9312 && $codepoint < 9472) {
			$range[$codepoint] = '2460_24ff'; // Enclosed Alphanumerics
		} elseif ($codepoint >= 11264 && $codepoint < 11360) {
			$range[$codepoint] = '2c00_2c5f'; // Glagolitic
		} elseif ($codepoint < 11392) {
			$range[$codepoint] = '2c60_2c7f'; // Latin Extended-C
		} elseif ($codepoint < 11520) {
			$range[$codepoint] = '2c80_2cff'; // Coptic
		} elseif ($codepoint >= 65280 && $codepoint < 65520) {
			$range[$codepoint] = 'ff00_ffef'; // Halfwidth and Fullwidth Forms
		} else {
			$range[$codepoint] = false;
		}
		if ($range[$codepoint] === false) {
			return null;
		}
		if (!isset($config[$range[$codepoint]])) {
			$file = dirname(__FILE__) . '/multibyte_string_database/casefolding/' . $range[$codepoint] . '.php';
			if (file_exists($file)) {
				include $file;
			}
		}
	}
	if ($range[$codepoint] === false || !isset($config[$range[$codepoint]])) {
		return null;
	}
	$result = array();
	$count = count($config[$range[$codepoint]]);
	for ($i = 0; $i < $count; $i++) {
		if ($type === 'lower' && $config[$range[$codepoint]][$i][$type][0] === $codepoint) {
			$result[] = $config[$range[$codepoint]][$i];
		} elseif ($type === 'upper' && $config[$range[$codepoint]][$i][$type] === $codepoint) {
			$result[] = $config[$range[$codepoint]][$i];
		}
	}
	return $result;
}

/**
 * A callback function for serving the function api_ucwords()
 * @author Harry Fuecks
 * @link http://dev.splitbrain.org/view/darcs/dokuwiki/inc/utf8.php
 * @author Ivan Tcholakov, adaptation for the Dokeos LMS, 2009
 * @param array $matches	Input array of matches corresponding to a single word
 * @return string			Returns a with first char of the word in uppercase
 */
function _api_utf8_ucwords_callback($matches) {
	$leadingws = $matches[2];
	$ucfirst = api_strtoupper($matches[3], 'UTF-8');
	$ucword = api_substr_replace(ltrim($matches[0]), $ucfirst, 0, 1, 'UTF-8');
	return $leadingws . $ucword;
}

/**
 * ----------------------------------------------------------------------------
 * Appendix to "Common sting operations with arrays"
 * ----------------------------------------------------------------------------
 */

// This (callback) function convers from UTF-8 to other encoding.
// It works with arrays of strings too.
function _api_array_utf8_decode($variable) {
	global $_api_encoding;
	if (is_array($variable)) {
		return array_map('_api_array_utf8_decode', $variable);
	}
    if (is_string($var)) {
    	return api_utf8_decode($variable, $_api_encoding);
    }
    return $variable;
}


/**
 * ----------------------------------------------------------------------------
 * Appendix to "String comparison"
 * ----------------------------------------------------------------------------
 */

// Returns an instance of Collator class (ICU) created for a specified language.
function _api_get_collator($language = null) {
	static $collator = array();
	if (!isset($collator[$language])) {
		$locale = api_get_locale_from_language($language);
		$collator[$language] = collator_create($locale);
		if (is_object($collator[$language])) {
			collator_set_attribute($collator[$language], Collator::CASE_FIRST, Collator::UPPER_FIRST);
		}
	}
	return $collator[$language];
}

// Returns an instance of Collator class (ICU) created for a specified language.
// This collator treats substrings of digits as numbers.
function _api_get_alpha_numerical_collator($language = null) {
	static $collator = array();
	if (!isset($collator[$language])) {
		$locale = api_get_locale_from_language($language);
		$collator[$language] = collator_create($locale);
		if (is_object($collator[$language])) {
			collator_set_attribute($collator[$language], Collator::CASE_FIRST, Collator::UPPER_FIRST);
			collator_set_attribute($collator[$language], Collator::NUMERIC_COLLATION, Collator::ON);
		}
	}
	return $collator[$language];
}

// A string comparison function that serves sorting functions.
function _api_cmp($string1, $string2) {
	global $_api_collator, $_api_encoding;
	$result = collator_compare($_api_collator, api_utf8_encode($string1, $_api_encoding), api_utf8_encode($string2, $_api_encoding));
	return $result === false ? 0 : $result;
}

// A reverse string comparison function that serves sorting functions.
function _api_rcmp($string1, $string2) {
	global $_api_collator, $_api_encoding;
	$result = collator_compare($_api_collator, api_utf8_encode($string2, $_api_encoding), api_utf8_encode($string1, $_api_encoding));
	return $result === false ? 0 : $result;
}

// A case-insensitive string comparison function that serves sorting functions.
function _api_casecmp($string1, $string2) {
	global $_api_collator, $_api_encoding;
	$result = collator_compare($_api_collator, api_strtolower(api_utf8_encode($string1, $_api_encoding), 'UTF-8'), api_strtolower(api_utf8_encode($string2, $_api_encoding), 'UTF-8'));
	return $result === false ? 0 : $result;
}

// A reverse case-insensitive string comparison function that serves sorting functions.
function _api_casercmp($string1, $string2) {
	global $_api_collator, $_api_encoding;
	$result = collator_compare($_api_collator, api_strtolower(api_utf8_encode($string2, $_api_encoding), 'UTF-8'), api_strtolower(api_utf8_encode($string1, $_api_encoding), 'UTF-8'));
	return $result === false ? 0 : $result;
}

// A reverse function from strnatcmp().
function _api_strnatrcmp($string1, $string2) {
	return strnatcmp($string2, $string1);
}

// A reverse function from strnatcasecmp().
function _api_strnatcasercmp($string1, $string2) {
	return strnatcasecmp($string2, $string1);
}

// A fuction that translates sorting flag constants from php core to correspondent constants from intl extension.
function _api_get_collator_sort_flag($sort_flag = SORT_REGULAR) {
	switch ($sort_flag) {
		case SORT_STRING:
		case SORT_SORT_LOCALE_STRING:
			return Collator::SORT_STRING;
		case SORT_NUMERIC:
			return Collator::SORT_NUMERIC;
	}
	return Collator::SORT_REGULAR;
}


/**
 * ----------------------------------------------------------------------------
 * Appendix to "Encoding management functions"
 * ----------------------------------------------------------------------------
 */

/**
 * Returns a table with non-UTF-8 encodings for all system languages.
 * @return array		Returns an array in the form array('language1' => array('encoding1', encoding2', ...), ...)
 * Note: The function api_get_non_utf8_encoding() returns the first encoding from this array that is correspondent to the given language. 
 */
function & _api_non_utf8_encodings() {
	// The following list may have some inconsistencies.
	// Place the most used for your language encoding at the first place.
	// If you are adding an encoding, check whether it is supported either by
	// mbstring library, either by iconv library.
	// If you modify this list, please, follow the given syntax exactly.
	// The language names must be stripped of any suffixes, such as _unicode, _corporate, _org, etc.
	static $encodings =
'
arabic: WINDOWS-1256, ISO-8859-6;
asturian: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
bosnian: WINDOWS-1250;
brazilian: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
bulgarian: WINDOWS-1251;
catalan: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
croatian: WINDOWS-1250;
czech: WINDOWS-1250, ISO-8859-2;
danish: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
dari: WINDOWS-1256;
dutch: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
english: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
euskera:  ISO-8859-15, WINDOWS-1252, ISO-8859-1;
esperanto: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
finnish: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
french: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
friulian: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
galician: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
georgian: GEORGIAN-ACADEMY, GEORGIAN-PS;
german: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
greek: WINDOWS-1253, ISO-8859-7;
hebrew: ISO-8859-8, WINDOWS-1255;
hungarian: WINDOWS-1250, ISO-8859-2;
indonesian: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
italian: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
japanese: EUC-JP, ISO-2022-JP, Shift-JIS;
korean: EUC-KR, ISO-2022-KR, CP949;
latvian: WINDOWS-1257, ISO-8859-13;
lithuanian: WINDOWS-1257, ISO-8859-13;
macedonian: WINDOWS-1251;
malay: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
norwegian: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
occitan: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
pashto: WINDOWS-1256;
persian: WINDOWS-1256;
polish: WINDOWS-1250, ISO-8859-2;
portuguese: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
quechua_cusco: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
romanian: WINDOWS-1250, ISO-8859-2;
russian: KOI8-R, WINDOWS-1251;
serbian: ISO-8859-15, WINDOWS-1252, ISO-8859-1, WINDOWS-1251;
simpl_chinese: GB2312, WINDOWS-936;
slovak: WINDOWS-1250, ISO-8859-2;
slovenian: WINDOWS-1250, ISO-8859-2;
spanish: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
swahili: ISO-8859-1;
swedish: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
thai: WINDOWS-874, ISO-8859-11;
trad_chinese: BIG-5, EUC-TW;
turkce: WINDOWS-1254, ISO-8859-9;
ukrainian: KOI8-U;
vietnamese: WINDOWS-1258, VISCII, TCVN;
yoruba: ISO-8859-15, WINDOWS-1252, ISO-8859-1;
';

	if (!is_array($encodings)) {
		$table = explode(';', str_replace(' ', '', $encodings));
		$encodings = array();
		foreach ($table as & $row) {
			$row = trim($row);
			if (!empty($row)) {
				$row = explode(':', $row);
				$encodings[$row[0]] = explode(',', strtoupper($row[1]));
			}
		}
	}
	return $encodings;
}

/**
 * Sets/Gets internal character encoding of the common string functions within the PHP mbstring extension.
 * @param string $encoding (optional)	When this parameter is given, the function sets the internal encoding.
 * @return string						When $encoding parameter is not given, the function returns the internal encoding.
 * Note: This function is used in the global initialization script for setting the internal encoding to the platform's character set.
 * @link http://php.net/manual/en/function.mb-internal-encoding
 */
function _api_mb_internal_encoding($encoding = null) {
	static $mb_internal_encoding = null;
	if (empty($encoding)) {
		if (is_null($mb_internal_encoding)) {
			if (MBSTRING_INSTALLED) {
				$mb_internal_encoding = @mb_internal_encoding();
			} else {
				$mb_internal_encoding = 'ISO-8859-15';
			}
		}
		return $mb_internal_encoding;
	}
	$mb_internal_encoding = $encoding;
	if (_api_mb_supports($encoding)) {
		return @mb_internal_encoding($encoding);
	}
	return false;
}

/**
 * Sets/Gets internal character encoding of the regular expression functions (ereg-like) within the PHP mbstring extension.
 * @param string $encoding (optional)	When this parameter is given, the function sets the internal encoding.
 * @return string						When $encoding parameter is not given, the function returns the internal encoding.
 * Note: This function is used in the global initialization script for setting the internal encoding to the platform's character set.
 * @link http://php.net/manual/en/function.mb-regex-encoding
 */
function _api_mb_regex_encoding($encoding = null) {
	static $mb_regex_encoding = null;
	if (empty($encoding)) {
		if (is_null($mb_regex_encoding)) {
			if (MBSTRING_INSTALLED) {
				$mb_regex_encoding = @mb_regex_encoding();
			} else {
				$mb_regex_encoding = 'ISO-8859-15';
			}
		}
		return $mb_regex_encoding;
	}
	$mb_regex_encoding = $encoding;
	if (_api_mb_supports($encoding)) {
		return @mb_regex_encoding($encoding);
	}
	return false;
}

/**
 * Retrieves specified internal encoding configuration variable within the PHP iconv extension.
 * @param string $type	The parameter $type could be: 'iconv_internal_encoding', 'iconv_input_encoding', or 'iconv_output_encoding'.
 * @return mixed		The function returns the requested encoding or FALSE on error.
 * @link http://php.net/manual/en/function.iconv-get-encoding
 */
function _api_iconv_get_encoding($type) {
	return _api_iconv_set_encoding($type);
}

/**
 * Sets specified internal encoding configuration variables within the PHP iconv extension.
 * @param string $type					The parameter $type could be: 'iconv_internal_encoding', 'iconv_input_encoding', or 'iconv_output_encoding'.
 * @param string $encoding (optional)	The desired encoding to be set.
 * @return bool							Returns TRUE on success, FALSE on error.
 * Note: This function is used in the global initialization script for setting these three internal encodings to the platform's character set.
 * @link http://php.net/manual/en/function.iconv-set-encoding
 */
// Sets current setting for character encoding conversion.
// The parameter $type could be: 'iconv_internal_encoding', 'iconv_input_encoding', or 'iconv_output_encoding'.
function _api_iconv_set_encoding($type, $encoding = null) {
	static $iconv_internal_encoding = null;
	static $iconv_input_encoding = null;
	static $iconv_output_encoding = null;
	if (!ICONV_INSTALLED) {
		return false;
	}
	switch ($type) {
		case 'iconv_internal_encoding':
			if (empty($encoding)) {
				if (is_null($iconv_internal_encoding)) {
					$iconv_internal_encoding = @iconv_get_encoding($type);
				}
				return $iconv_internal_encoding;
			}
			if (_api_iconv_supports($encoding)) {
				if(@iconv_set_encoding($type, $encoding)) {
					$iconv_internal_encoding = $encoding;
					return true;
				}
				return false;
			}
			return false;
		case 'iconv_input_encoding':
			if (empty($encoding)) {
				if (is_null($iconv_input_encoding)) {
					$iconv_input_encoding = @iconv_get_encoding($type);
				}
				return $iconv_input_encoding;
			}
			if (_api_iconv_supports($encoding)) {
				if(@iconv_set_encoding($type, $encoding)) {
					$iconv_input_encoding = $encoding;
					return true;
				}
				return false;
			}
			return false;
		case 'iconv_output_encoding':
			if (empty($encoding)) {
				if (is_null($iconv_output_encoding)) {
					$iconv_output_encoding = @iconv_get_encoding($type);
				}
				return $iconv_output_encoding;
			}
			if (_api_iconv_supports($encoding)) {
				if(@iconv_set_encoding($type, $encoding)) {
					$iconv_output_encoding = $encoding;
					return true;
				}
				return false;
			}
			return false;
	}
	return false;
}

// Ckecks whether a given encoding defines single-byte characters.
// The result might be not accurate for unknown by this library encodings.
function _api_is_single_byte_encoding($encoding) {
	static $checked = array();
	if (!isset($checked[$encoding])) {
		$character_map = _api_get_character_map_name(api_refine_encoding_id($encoding));
		$checked[$encoding] = (!empty($character_map) && $character_map != 'UTF-8');
	}
	return $checked[$encoding];
}

/**
 * Checks whether the specified encoding is supported by the PHP mbstring extension.
 * @param string $encoding	The specified encoding.
 * @return bool				Returns TRUE when the specified encoding is supported, FALSE othewise.
 */
function _api_mb_supports($encoding) {
	static $supported = array();
	$encoding = api_refine_encoding_id($encoding);
	if (!isset($supported[$encoding])) {
		if (MBSTRING_INSTALLED) {
			$mb_encodings = mb_list_encodings();
			$mb_encodings = array_map('api_refine_encoding_id', $mb_encodings);
		} else {
			$mb_encodings = array();
		}
		$supported[$encoding] = in_array($encoding, $mb_encodings);
	}
	return $supported[$encoding];
}

/**
 * Checks whether the specified encoding is supported by the PHP iconv extension.
 * @param string $encoding	The specified encoding.
 * @return bool				Returns TRUE when the specified encoding is supported, FALSE othewise.
 */
function _api_iconv_supports($encoding) {
	static $supported = array();
	$encoding = api_refine_encoding_id($encoding);
	if (!isset($supported[$encoding])) {
		if (ICONV_INSTALLED) {
			$test_string = '';
			for ($i = 32; $i < 128; $i++) {
				$test_string .= chr($i);
			}
			$supported[$encoding] = (@iconv_strlen($test_string, $encoding)) ? true : false;
		} else {
			$supported[$encoding] = false;
		}
	}
	return $supported[$encoding];
}

// This function checks whether the function _api_convert_encoding() (the php-
// implementation) is able to convert from/to a given encoding.
function _api_convert_encoding_supports($encoding) {
	static $supports = array();
	if (!isset($supports[encoding])) {
		$supports[encoding] = _api_get_character_map_name($encoding) != '';
	}
	return $supports[encoding];
}

/**
 * Checks whether the specified encoding is supported by the html-entitiy related functions.
 * @param string $encoding	The specified encoding.
 * @return bool				Returns TRUE when the specified encoding is supported, FALSE othewise.
 */
function _api_html_entity_supports($encoding) {
	static $supported = array();
	$encoding = api_refine_encoding_id($encoding);
	if (!isset($supported[$encoding])) {
		// See http://php.net/manual/en/function.htmlentities.php
		$html_entity_encodings = array(explode(',',
'
ISO-8859-1, ISO8859-1,
ISO-8859-15, ISO8859-15,
UTF-8,
cp866, ibm866, 866,
cp1251, Windows-1251, win-1251, 1251,
cp1252, Windows-1252, 1252,
KOI8-R, koi8-ru, koi8r,
BIG5, 950,
GB2312, 936,
BIG5-HKSCS,
Shift_JIS, SJIS, 932,
EUC-JP, EUCJP
'));
		$html_entity_encodings = array_map('trim', $html_entity_encodings);
		$html_entity_encodings = array_map('api_refine_encoding_id', $html_entity_encodings);
		$supported[$encoding] = in_array($encoding, $html_entity_encodings);
	}
	return $supported[$encoding] ? true : false;
}


/**
 * ----------------------------------------------------------------------------
 * Appendix to "Language management functions"
 * ----------------------------------------------------------------------------
 */

/**
 * This function returns an array of those languages that can use Latin 1 encoding.
 * @return array	The array of languages that can use Latin 1 encoding (ISO-8859-15, ISO-8859-1, WINDOWS-1252, ...).
 * Note: The returned language identificators are purified, without suffixes.
 */
function _api_get_latin1_compatible_languages() {
	static $latin1_languages;
	if (!isset($latin1_languages)) {
		$latin1_languages = array();
		$encodings = & _api_non_utf8_encodings();
		foreach ($encodings as $key => $value) {
			if (api_is_latin1($value[0])) {
				$latin1_languages[] = $key;
			}
		}
	}
	return $latin1_languages;
}


/**
 * ----------------------------------------------------------------------------
 * Upgrading the PHP5 mbstring extension
 * ----------------------------------------------------------------------------
 */

// This is a multibyte replacement of strchr().
// This function exists in PHP 5 >= 5.2.0
// See http://php.net/manual/en/function.mb-strrchr
if (MBSTRING_INSTALLED && !function_exists('mb_strchr')) {
	function mb_strchr($haystack, $needle, $part = false, $encoding = null) {
		if (empty($encoding)) {
			$encoding = mb_internal_encoding();
		}
		return mb_strstr($haystack, $needle, $part, $encoding);
	}
}

// This is a multibyte replacement of stripos().
// This function exists in PHP 5 >= 5.2.0
// See http://php.net/manual/en/function.mb-stripos
if (MBSTRING_INSTALLED && !function_exists('mb_stripos')) {
	function mb_stripos($haystack, $needle, $offset = 0, $encoding = null) {
		if (empty($encoding)) {
			$encoding = mb_internal_encoding();
		}
		return mb_strpos(mb_strtolower($haystack, $encoding), mb_strtolower($needle, $encoding), $offset, $encoding);
	}
}

// This is a multibyte replacement of stristr().
// This function exists in PHP 5 >= 5.2.0
// See http://php.net/manual/en/function.mb-stristr
if (MBSTRING_INSTALLED && !function_exists('mb_stristr')) {
	function mb_stristr($haystack, $needle, $part = false, $encoding = null) {
		if (empty($encoding)) {
			$encoding = mb_internal_encoding();
		}
		$pos = mb_strpos(mb_strtolower($haystack, $encoding), mb_strtolower($needle, $encoding), 0, $encoding);
		if ($pos === false) {
			return false;
		}
		if($part == true) {
			return mb_substr($haystack, 0, $pos + 1, $encoding);
		}
		return mb_substr($haystack, $pos, mb_strlen($haystack, $encoding), $encoding);
	}
}

// This is a multibyte replacement of strrchr().
// This function exists in PHP 5 >= 5.2.0
// See http://php.net/manual/en/function.mb-strrchr
if (MBSTRING_INSTALLED && !function_exists('mb_strrchr')) {
	function mb_strrchr($haystack, $needle, $part = false, $encoding = null) {
		if (empty($encoding)) {
			$encoding = mb_internal_encoding();
		}
		$needle = mb_substr($needle, 0, 1, $encoding);
		$pos = mb_strrpos($haystack, $needle, mb_strlen($haystack, $encoding) - 1, $encoding);
		if ($pos === false) {
			return false;
		} 
		if($part == true) {
			return mb_substr($haystack, 0, $pos + 1, $encoding);
		}
		return mb_substr($haystack, $pos, mb_strlen($haystack, $encoding), $encoding);
	}
}

// This is a multibyte replacement of strstr().
// This function exists in PHP 5 >= 5.2.0
// See http://php.net/manual/en/function.mb-strstr
if (MBSTRING_INSTALLED && !function_exists('mb_strstr')) {
	function mb_strstr($haystack, $needle, $part = false, $encoding = null) {
		if (empty($encoding)) {
			$encoding = mb_internal_encoding();
		}
		$pos = mb_strpos($haystack, $needle, 0, $encoding);
		if ($pos === false) {
			return false;
		}
		if($part == true) {
			return mb_substr($haystack, 0, $pos + 1, $encoding);
		}
		return mb_substr($haystack, $pos, mb_strlen($haystack, $encoding), $encoding);
	}
}
