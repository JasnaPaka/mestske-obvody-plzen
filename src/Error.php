<?php

/**
 * Class Error obsahuje definici chybových stavů služby.
 */
class Error
{
	const ERROR_CODE_1 = 1;
	const ERROR_CODE_2 = 2;
	const ERROR_CODE_3 = 3;
	const ERROR_CODE_4 = 4;
	const ERROR_CODE_5 = 5;

	const ERROR_CODE_1_MSG = "Služba je vypnuta.";
	const ERROR_CODE_2_MSG = "Vstupní parametry 'lat' a 'long' musí být reálná čísla.";
	const ERROR_CODE_3_MSG = "Na základě vstupních parametrů 'lat' a 'long' nebyl nalezen žádný městský obvod.";
	const ERROR_CODE_4_MSG = "Nastala interní chyba služby. Databáze není dostupná.";
	const ERROR_CODE_5_MSG = "V POST Payload nebyl nalezen validní JSON.";
}