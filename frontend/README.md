# Ninjastic Fórum rendszer

Üdvözöljük a Ninjastic projekt repozitóriumában, mely a VESZC Ipari Céges Képzés keretében készült szakdolgozat része. Ez a README fájl útmutatást nyújt a szükséges szoftverek telepítéséhez és beállításához.

## Javasolt fejlesztői környezet

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (és tiltsa le a Vetur-t).

## Típus támogatás a `.vue` importálásokhoz TS-ben

A TypeScript alapértelmezés szerint nem tudja kezelni a típusinformációkat a `.vue` importálásokhoz, ezért a `tsc` CLI-t helyettesíti a `vue-tsc` a típusellenőrzéshez. Az szerkesztőkben szükség van a [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) -ra, hogy a TypeScript nyelvszolgáltatás tudjon a `.vue` típusokról.

## Testreszabott konfiguráció

Lásd a [Vite Konfigurációs Referenciát](https://vitejs.dev/config/).

## Projekt beállítása

```sh
npm install
```

### Fordítás és gyors újratöltés fejlesztéshez

```sh
npm run dev
```

### Típusellenőrzés, fordítás és minifikálás produkcióhoz

```sh
npm run build
```

### Egységtesztek futtatása a [Vitest](https://vitest.dev/) segítségével

```sh
npm run test:unit
```

### Lintezés az [ESLint](https://eslint.org/)  segítségével

```sh
npm run lint
```
