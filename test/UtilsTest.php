<?php

include_once "src/Utils.php";

use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{

	public function testRemoveInvalidXMLChars()
	{
		$this->assertEquals("test123", Utils::removeInvalidXMLChars("test123"));
		$this->assertEquals("test123", Utils::removeInvalidXMLChars("test><123"));
	}

}

