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
		$parameter["format"] = trim(filter_input(INPUT_GET, "format", FILTER_SANITIZE_STRING));

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
		$parameters = $this->getParameters();

		// Je služba povolena?
		if (!$this->settings["enabled"]) {
			$this->processError(Error::ERROR_CODE_1, Error::ERROR_CODE_1_MSG,
				$this->getFormat($parameters));
			return;
		}

		// Jsou v pořádku vstupní parametry?
		if (!$this->validate($parameters)) {
			$this->processError(Error::ERROR_CODE_2, Error::ERROR_CODE_2_MSG,
				$this->getFormat($parameters));
			return;
		}

		// Zpracování výsledku
		$umo = $this->db->findUmo($parameters["lat"], $parameters["long"]);
		if ($umo === false) {
			$this->processError(Error::ERROR_CODE_4, Error::ERROR_CODE_4_MSG,
				$this->getFormat($parameters));
			return;
		}
		if ($umo == null) {
			$this->processError(Error::ERROR_CODE_3, Error::ERROR_CODE_3_MSG,
				$this->getFormat($parameters), 404);
			return;
		}

		$part = $this->db->findCityPart($parameters["lat"], $parameters["long"]);
		if ($part === false) {
			$this->processError(Error::ERROR_CODE_4, Error::ERROR_CODE_4_MSG,
				$this->getFormat($parameters));
			return;
		}

		$this->processResult($umo, $part, $this->getFormat($parameters));
	}

	/**
	 * Zjistí formát výstupu.
	 *
	 * @param array $parameters pole se vstupními parametry
	 * @return string formát výstupu ("xml" či "json")
	 */
	private function getFormat($parameters)
	{
		if ($parameters["format"] !== "xml" && $parameters["format"] !== "json") {
			return "xml";
		}

		return $parameters["format"];
	}

	/**
	 * Provede zpracování pozitivní odpovědi (něco bylo na základě vstupu nalezeno). Výsledkem je XML.
	 * @param $umo array načtené hodnoty z databáze (ty, které jsou použity pro generování výstupu)
	 * @param $part array název části obce
	 * @param string $format formát výstupu (XML či JSON)
	 */
	private function processResult($umo, $part, $format)
	{
		if ($format === "xml") {
			$content = file_get_contents("./template/result.xml");
			$content = str_replace("%CODE%", $umo["kod"], $content);
			$content = str_replace("%NAME%", Utils::removeInvalidXMLChars($umo["nazev"]), $content);
			$content = str_replace("%PART%", Utils::removeInvalidXMLChars($part["nazev"]), $content);
		} else {
			$arr = array ("code" => $umo["kod"], "umo" => $umo["nazev"], "part" => $part["nazev"]);
			$content = json_encode($arr, JSON_UNESCAPED_UNICODE);
		}

		$this->printOutput(200, $content, $format);
	}

	/**
	 * Provede vygenerování chybového obsahu na výstup.
	 *
	 * @param string $code kód chyby
	 * @param string $format formát výstupu (XML či JSON)
	 * @param string $msg text chyby
	 * @param int $statusCode stavový http kód (nepovinné)
	 */
	private function processError($code, $msg, $format, $statusCode = 500)
	{
		if ($format === "xml") {
			$content = file_get_contents("./template/error.xml");
			$content = str_replace("%CODE%", $code, $content);
			$content = str_replace("%MSG%", Utils::removeInvalidXMLChars($msg), $content);
		} else {
			$arr = array ("code" => $code, "msg" => $msg);
			$content = json_encode($arr, JSON_UNESCAPED_UNICODE);
		}

		$this->printOutput($statusCode, $content, $format);
	}

	/**
	 * Provede zobrazení vygenerovaného obsahu na výstup.
	 *
	 * @param int $statusCode stavový kód http
	 * @param string $content obsah na výstup
	 * @param string $format formát výstupu (XML či JSON)
	 */
	private function printOutput($statusCode, $content, $format)
	{
		$contentType = "application/xml";
		if ($format === "json") {
			$contentType = "application/json";
		}

		http_response_code($statusCode);
		header('Content-Type: '.$contentType);
		header("Content-Length: " . strlen($content));
		print $content;
	}


}