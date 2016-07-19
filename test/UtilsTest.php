<?php

include "src/Utils.php";

class UtilsTest extends PHPUnit_Framework_TestCase
{

	public function testRemoveInvalidXMLChars()
	{
		$this->assertEquals("test123", Utils::removeInvalidXMLChars("test123"));
	}

}

