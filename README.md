# Ninjastic - VESZC Ipari Céges Képzés Szakdolgozat

Üdvözöljük a Ninjastic projekt repozitóriumában, mely a VESZC Ipari Céges Képzés keretében készült szakdolgozat része. Ez a README fájl útmutatást nyújt a szükséges szoftverek telepítéséhez és beállításához.

## Telepítési Útmutató

#### Clone-ozzuk a repot.
 Ha szeretnék https/SSL-t akkor a Ninjastic/certs mappába tegyünk bele a domainhez tartozó .crt és .key file-t (pl: ninjastic.pro.crt, ninjastic.pro.key), majd módosítsuk a Ninjastic/docker-compose.yaml file-t:

#### Az nginx-proxy service-ben vegyük ki a ”#”-eket ports, volumes és az environment résznél. Ezzel elérjük hogy a reverse-proxy-nk https-t használjon és felolvassa a cert-eket.  

```
  nginx-proxy:
    image: jwilder/nginx-proxy:1.5.1
    ports:
      - "80:80"
      #- "443:443"
    volumes:
      - /var/run/docker.sock:/tmp/docker.sock
      #- ./certs:/etc/nginx/certs
    environment:
      - HTTP_PORT=80
      #- HTTPS_PORT=443
      #- VIRTUAL_PROTO=https
```

#### Lépjünk be a Ninjastic mappába és a következő parancsal elindítjuk a database-t, api-t, admin, reverse-proxy-t és a frontendet: 

```sudo docker-compose up –d```

#### Miután a környezet elindult be kell exec-elni az egyik backend containerbe: 

```docker exec -it ninjastic-api bash```

#### Ezután pedig lefuttatjuk a migrációt: 

```php bin/console --no-interaction doctrine:migrations:migrate ```

#### Majd a dirty words commandot ami az adatbázis elmenti a csúnya szavakat: 

```php bin/console app:process-dirty-words-xml ```

#### A host fájlba vegyük fel a következőt: 

```127.0.01 api.ninjastic.pro admin.ninjastic.pro ninjastic.pro```

Windows hosts fájl: ```C:\Windows\System32\drivers\etc\hosts```

Linux hosts fájl: ```/etc/hosts```

#### Ezekután a következő url-eken érhetjük el a weboldalkat: 
- API: api.ninjastic.pro 
- Admin:  admin.ninjastic.pro 
- Frontend: ninjastic.pro 

# További Források

## Trágár Szavak Listája

A projektünkben különböző funkciókhoz használunk egy trágár szavak listáját. Ezt a listát egy nyilvánosan elérhető GitHub repozitóriumból szereztük. Az eredeti lista a következő helyen található:

- [Trágár Szavak Listája](https://github.com/stifolder/kretainsult/blob/master/src/assets/dirtywords.xml)

Köszönetet mondunk a fenti repozitórium szerzőinek a lista összeállításáért és nyilvánosságra hozataláért. A lista használata projektünkben kizárólag [a cél leírása, pl. tartalomszűrés, nyelvi feldolgozás, stb.] céljából történik.

### Figyelmeztetés

Kérjük, vegye figyelembe, hogy a megadott link explicit nyelvezetet tartalmaz. Nem támogatjuk és nem népszerűsítjük az ilyen jellegű nyelvhasználatot; a projektünkben csak [meghatározott célra] használjuk.
