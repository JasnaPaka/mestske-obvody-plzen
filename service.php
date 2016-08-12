<?php
/**
 * Webová služba, která na základě GPS souřadnic ve formátu WGS 84 (lat, long) vrátí informaci o městské části, kde
 * se souřadnice nachází. V původním účelu služby je uvažována Plzeň, ale teoreticky se jedná o obecné řešení.
 *
 * Příklady použití jsou k dispozici v dokumentaci.
 *
 * @author Pavel Cvrček
 */

if (!file_exists("config.php")) {
	die("Nebyl nalezen konfiguracni soubor config.php. Nezapomneli jste prejmenovat a nastavit config-default.php?");
}

include "src/Controller.php";
include "config.php";

$controller = new Controller($SETTING);
$controller->process();
