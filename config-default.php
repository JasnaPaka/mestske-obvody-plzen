<?php

/** Zda je webová služba povolena. Pokud ne, vrací o tom chybové hlášení. */
$SETTING["enabled"] = true;

/** Host, kde běží databáze PostgreSQL s PostGISem. */
$SETTING["psql_host"] = "localhost";
/** Uživatelské jméno do PostgreSQL. */
$SETTING["psql_user"] = "";
/** Heslo uživatele do PostgreSQL. */
$SETTING["psql_pass"] = "";
/** Databáze, kde jsou data k městským částem. */
$SETTING["psql_db"]   = "";