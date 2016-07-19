<?php

include_once "Db.php";
include_once "Error.php";
include_once "Utils.php";


/**
 * Class Controller zpracuje vstup od uživatele a provede dotaz do databáze. Hlavní vstupní bod je funkce process().
 *
 * @author Pavel Cvrček
 */
class Controller
{

	private $settings;
	private $db;

	function __construct($settings)
	{
		$this->settings = $settings;
		$this->db = new Db($settings);
	}

	/**
	 * Provede načtení parametrů volání v URL. Načtené parametry jsou validovány na problematický kód.
	 *
	 * @return array pole s načtenými parametry
	 */
	private function getParameters()
	{
		$parameter["lat"] = filter_input(INPUT_GET, "lat", FILTER_VALIDATE_FLOAT);
		$parameter["long"] = filter_input(INPUT_GET, "long", FILTER_VALIDATE_FLOAT);

		return $parameter;
	}

	/**
	 * Provede kontrolu, zda jsou v URL potřebné parametry.
	 *
	 * @param $values pole s načtenými parametry
	 * @return bool vrací true, pokud jsou parametry v pořádku, jinak false
	 */
	private function validate($values)
	{
		if ($values["lat"] == null || !is_float($values["lat"])) {
			return false;
		}
		if ($values["long"] == null || !is_float($values["long"])) {
			return false;
		}

		return true;
	}

	/**
	 * Hlavní metoda třídy. Po zavolání se provede zpracování dotazu.
	 */
	public function process()
	{
		// Je služba povolena?
		if (!$this->settings["enabled"]) {
			$this->processError(Error::ERROR_CODE_1, Error::ERROR_CODE_1_MSG);
			return;
		}

		// Jsou v pořádku vstupní parametry?
		$parameter = $this->getParameters();
		if (!$this->validate($parameter)) {
			$this->processError(Error::ERROR_CODE_2, Error::ERROR_CODE_2_MSG);
			return;
		}

		// Zpracování výsledku
		$output = $this->db->findUmo($parameter["lat"], $parameter["long"]);
		if ($output === false) {
			$this->processError(Error::ERROR_CODE_4, Error::ERROR_CODE_4_MSG);
			return;
		}
		if ($output == null) {
			$this->processError(Error::ERROR_CODE_3, Error::ERROR_CODE_3_MSG, 404);
			return;
		}

		$this->processResult($output);
	}

	/**
	 * Provede zpracování pozitivní odpovědi (něco bylo na základě vstupu nalezeno). Výsledkem je XML.
	 * @param $parameters array načtené hodnoty z databáze (ty, které jsou použity pro generování výstupu)
	 */
	private function processResult($parameters)
	{
		$content = file_get_contents("./template/result.xml");
		$content = str_replace("%CODE%", Utils::removeInvalidXMLChars($parameters["kod"]), $content);
		$content = str_replace("%NAME%", Utils::removeInvalidXMLChars($parameters["nazev"]), $content);

		$this->printOutput(200, $content);
	}

	/**
	 * Provede vygenerování chybového XML.
	 *
	 * @param string $code kód chyby
	 * @param string $msg text chyby
	 * @param int $statusCode stavový http kód (nepovinné)
	 */
	private function processError($code, $msg, $statusCode = 500)
	{
		$content = file_get_contents("./template/error.xml");
		$content = str_replace("%CODE%", Utils::removeInvalidXMLChars($code), $content);
		$content = str_replace("%MSG%", Utils::removeInvalidXMLChars($msg), $content);

		$this->printOutput($statusCode, $content);
	}

	/**
	 * Provede zobrazení vygenerovaného XML na výstup.
	 * @param $statusCode int stavový kód http
	 * @param $content string obsah xml
	 */
	private function printOutput($statusCode, $content)
	{
		http_response_code($statusCode);
		header('Content-Type: application/xml');
		header("Content-Length: " . strlen($content));
		print $content;
	}


}