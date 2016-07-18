# Detekce městského obvodu Plzně ze souřadnic

Webová služba, která na základě vstupních souřadnic ve formátu [WGS 84](https://en.wikipedia.org/wiki/World_Geodetic_System) vrátí XML soubor s informací, v jakém městském obvodu Plzně se daný bod nachází.

## Instalace

Je vyžadován webový server s podporou PHP, databáze PostgreSQL s nadstavbou PostGIS. Otestována byla následující
konfigurace, ale může být i jiná.

* Apache 2.4
* PHP 5.6
* PostgreSQL 9.3 či 9.5
* [PostGIS](http://postgis.net/) k odpovídající verzi PostgreSQL

Tento projekt si stačí stáhnout, a následně přejmenovat soubor `config-default.php` v rootu projektu na `config.php`. V něm pak nastavit přístupové údaje k databázi s daty hranic městských obvodů.

### Příprava dat

Hranice městských obvodů v Plzni lze volně získat v [Databázi otevřených dat města Plzně](https://opendata.plzen.eu/dataset/gis-uzemni-celky-plzen-mestske-casti) ve formátu [shp](https://en.wikipedia.org/wiki/Shapefile). Ten lze následně naimportovat do databáze pomocí příkazu `shp2pgsql`, který je součástí PostGisu.

```shp2pgsql -s 5514 mestskecasti_SHP.shp public.umo | psql -h localhost -d plzen -U postgres```

Výše uvedený příklad předpokládá, že výsledná databáze se bude jmenovat `plzen` a informace o městských obvodech se naimportuje do tabulky `umo`. Tu následně upravíme tak, aby jednotlivé sloupce byly pojmenovány následovně:

* gid
* id
* nazev
* geom
* kod 

Reálně přejmenujeme sloupec pro název městského obvodu a přidáme sloupec `kod`. Protože se chybně naimportovala diakritika, bude potřeba ještě upravit názvy ve sloupci `nazev`. Do nově přidaného sloupce `kod` je potřeba přidat zkratky městských obvodů. Např. `umo3` pro Plzeň 3 a ekvivalentně pro další.

**Pozor!** Pokud budete vytvořenou databázi zálohovat a obnovovat jinde, ujistěte se, že v tabulce `spatial_ref_sys`, která je součástí databáze, bude po obnovení uveden souřadnicový systém S-JTSK [EPSG:5514](http://epsg.io/5514). Stačí si vyfiltrovat hodnotu `5514` ve sloupci `srid`. Pokud ji zde nenaleznete, stáhněte si následující skript a nad databází jej proveďte:

```
wget http://epsg.io/5514.sql
psql -U postgres -f 5514.sql plzen
```

## Příklad použití




