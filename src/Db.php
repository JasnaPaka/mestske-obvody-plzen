<?php

/**
 * Class Db slouží ke komunikaci s databázi PostgreSQL s nadstavbou PostGis.
 *
 * @author Pavel Cvrček
 */
class Db
{

	private $settings;

	function __construct($settings)
	{
		$this->settings = $settings;
	}

	private function getConnectionString()
	{
		return sprintf("pgsql:dbname=%s;host=%s;user=%s;password=%s", $this->settings["psql_db"],
			$this->settings["psql_host"], $this->settings["psql_user"], $this->settings["psql_pass"]);
	}

	/**
	 * Dotáže se na UMO dle souřadnice.
	 *
	 * @param $lat první část souřadnice (WGS 84)
	 * @param $long druhá část souřadnice (WGS 84)
	 * @return bool|array|null Vrací kód a název umo, kam GPS souřadnice spadá nebo null, pokud nebylo nic
	 * nalezeno nebo false, pokud nastal při dotazování nějaký problém.
	 */
	public function findUmo($lat, $long)
	{
		$db = null;
		try {
			$db = new PDO($this->getConnectionString());

			$statement = $db->prepare("SELECT kod, nazev FROM umo WHERE 
				st_contains(geom, ST_Transform(ST_SetSRID(ST_MakePoint(:lng, :lat), 4326), 5514)) = true LIMIT 1");
			$statement->execute(array(':lat' => $lat, ':lng' => $long));
			$result = $statement->fetch(PDO::FETCH_ASSOC);

			if (!$result) {
				return null;
			}

			return $result;
		} catch (PDOException $e) {
			if ($this->settings["debug"]) {
				print $e->getMessage();
			}
			return false;
		}
	}

	/**
	 * Dotáže se na část obce dle souřadnice.
	 *
	 * @param $lat první část souřadnice (WGS 84)
	 * @param $long druhá část souřadnice (WGS 84)
	 * @return bool|array|null Vrací název části obce, kam GPS souřadnice spadá nebo null, pokud nebylo nic
	 * nalezeno nebo false, pokud nastal při dotazování nějaký problém.
	 */
	public function findCityPart($lat, $long)
	{
		$db = null;
		try {
			$db = new PDO($this->getConnectionString());

			$statement = $db->prepare("SELECT nazev FROM castiobce WHERE 
				st_contains(geom, ST_Transform(ST_SetSRID(ST_MakePoint(:lng, :lat), 4326), 5514)) = true LIMIT 1");
			$statement->execute(array(':lat' => $lat, ':lng' => $long));
			$result = $statement->fetch(PDO::FETCH_ASSOC);

			if (!$result) {
				return null;
			}

			return $result;
		} catch (PDOException $e) {
			if ($this->settings["debug"]) {
				print $e->getMessage();
			}
			return false;
		}
	}
}

