# Ninjastic - VESZC Ipari Céges Képzés Szakdolgozat

Üdvözöljük a Ninjastic projekt repozitóriumában, mely a VESZC Ipari Céges Képzés keretében készült szakdolgozat része. Ez a README fájl útmutatást nyújt a szükséges szoftverek telepítéséhez és beállításához.

## Telepítési Útmutató

A Ninjastic projekt futtatásához szükséges szoftverek telepítéséhez kövesse az alábbi lépéseket. Az utasítások Ubuntu rendszerre vonatkoznak.

### Előfeltételek

Győződjön meg arról, hogy rendszere naprakész:

#### PHP 8.2 Telepítése

```bash
sudo apt update
sudo apt install software-properties-common
sudo apt update
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2
sudo apt install php8.2-mysql php8.2-gd
php -v
```

# További Források

## Trágár Szavak Listája

A projektünkben különböző funkciókhoz használunk egy trágár szavak listáját. Ezt a listát egy nyilvánosan elérhető GitHub repozitóriumból szereztük. Az eredeti lista a következő helyen található:

- [Trágár Szavak Listája](https://github.com/stifolder/kretainsult/blob/master/src/assets/dirtywords.xml)

Köszönetet mondunk a fenti repozitórium szerzőinek a lista összeállításáért és nyilvánosságra hozataláért. A lista használata projektünkben kizárólag [a cél leírása, pl. tartalomszűrés, nyelvi feldolgozás, stb.] céljából történik.

### Figyelmeztetés

Kérjük, vegye figyelembe, hogy a megadott link explicit nyelvezetet tartalmaz. Nem támogatjuk és nem népszerűsítjük az ilyen jellegű nyelvhasználatot; a projektünkben csak [meghatározott célra] használjuk.
