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

	/**
	 * Dotáže se na UMO dle souřadnice.
	 *
	 * @param $lat první část souřadnice (WGS 84)
	 * @param $long druhá část souřadnice (WGS 84)
	 * @return bool|mixed|null Vrací záznam o městské části, kam GPS souřadnice spadá nebo null, pokud nebylo nic
	 * nalezeno nebo false, pokud nastal při dotazování nějakýk problém.
	 */
	public function findUmo($lat, $long)
	{
		$connection_string = sprintf("pgsql:dbname=%s;host=%s;user=%s;password=%s", $this->settings["psql_db"],
			$this->settings["psql_host"], $this->settings["psql_user"], $this->settings["psql_pass"]);

		$db = null;
		try {
			$db = new PDO($connection_string);

			$statement = $db->prepare("SELECT kod, nazev FROM umo WHERE st_contains(geom, ST_Transform(ST_SetSRID(ST_MakePoint(:lng, :lat), 4326), 5514)) = true LIMIT 1");
			$statement->execute(array(':lat' => $lat, ':lng' => $long));
			$result = $statement->fetch(PDO::FETCH_ASSOC);

			if (!$result) {
				return null;
			}

			return $result;
		} catch (PDOException $e) {
			return false;
		}
	}
}

