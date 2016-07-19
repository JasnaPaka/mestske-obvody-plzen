<?php

/**
 * Class Utils obsahuje pouze pomocné funkce.
 *
 * @author Pavel Cvrček
 */
class Utils
{

	/**
	 * Odstraní z řetězce problematické znaky, které by ve výsledném XML mohly dělat problém.
	 *
	 * http://stackoverflow.com/questions/3466035/how-to-skip-invalid-characters-in-xml-file-using-php
	 *
	 * @param $value řetězec pro zápis do XML
	 * @return string ošetřený řetězec pro zápis do XML.
	 */
	public static function removeInvalidXMLChars($value) {
		$ret = "";
		if (empty($value))
		{
			return $ret;
		}

		$length = strlen($value);
		for ($i=0; $i < $length; $i++)
		{
			$current = ord($value{$i});
			if (($current == 0x9) ||
				($current == 0xA) ||
				($current == 0xD) ||
				(($current >= 0x20) && ($current <= 0xD7FF)) ||
				(($current >= 0xE000) && ($current <= 0xFFFD)) ||
				(($current >= 0x10000) && ($current <= 0x10FFFF)))
			{
				$ret .= chr($current);
			}
			else
			{
				$ret .= " ";
			}
		}
		return $ret;
	}
}