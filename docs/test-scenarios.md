# 1. Testovacie scenáre 

## 1.1. Administrator

- Navracie prihlášky študentom, ak sú údaje chybné
- Pridáva body za aktivitu, výsledky jazykových testov
- Potrebuje zmenit deadline podavanie prihlasok
- Zverjni výsledky podania prihlášok
- Chce zmeniť/alebo vymazať prihlášku
- Potvrdit prihlášku
- Sortovanie tabuliek

### 1.1.1.Editácia prihlášky

Administrátor chce editovať konkrétnu prihlášku študenta. 

**vstup:** Administrátor klikne na zoznam prihlášok v ľavom paneli.

**výstup:** Zobrazí sa zoznam s prihláškami

**vstup:** Dvojklik na riadok s konkrétnou prihláškou

**výstup:** Zobrazenie prihlášky s vyplnenými údajmi pre editáciu

**vstup:** Administrátor edituje údaje, vyberie stav prihlášky(potvrdená, vrátená, zamietnutá...)

**výstup:** správa o ne/úspešnom editovaní prihlášky

### 1.1.2. Vymazanie prihlasky

Vymazanie prihlasky funguje podobnym sposobom ako editacia prihlasky.

**vstup:** Administrátor klikne na zoznam prihlášok v ľavom paneli.

**výstup:** Zobrazí sa zoznam s prihláškami

**vstup:** Nasledne klikne na nazov prihlasky

**výstup:** Zobrazenie 3 tlacidiel na editaciu prihlasky

**vstup:** Kliknutie na tlacidlo kosa, cize akcia vymazania danej prihlasky 

**výstup:** Vyskoci dialogove okno pre potvrdenie akcie vymazania prihlasky

**vstup:** Potvrdenie akcie

**výstup:** Prihlaska sa vymaze z tabulky

### 1.1.3. Tlac prihlasky

Tlac prihlasky funguje podobnym sposobom ako editacia prihlasky.

**vstup:** Administrátor klikne na zoznam prihlášok v ľavom paneli.

**výstup:** Zobrazí sa zoznam s prihláškami

**vstup:** Nasledne klikne na nazov prihlasky

**výstup:** Zobrazenie 3 tlacidiel na editaciu prihlasky

**vstup:** Kliknutie na tlacidlo lupy, cize akcia nahladu danej prihlasky

**výstup:** Zobrazi sa nova karta v prehliadaci s nahladom pre tlac prihlasky

### 1.1.4. Zmena deadline podávania prihlášok

V každom akademickom roku adminisitrátor mení dátum dokedy sa môžu 
prihlášky podávať.

**vstup:** Zmení dátum v ľavom paneli.

**výstup:** Informácia o ne/úspešnej zmene.

### 1.1.5. Rozbalovanie a zabalovanie dlhych nazvov vymennych kurzov

Kvoli dlhym nazvom kurzov bolo pridane tlacidlo more/less

**vstup:** Administrator zobrazi vsetky vycestovania

**výstup:** Tabulka s danymi informaciami

**vstup:** Admin klikne v stlpci courses na tlacidlo **more** pre zobrazenie rozsirenej infomracie

**výstup:** Zobrazenie rozsirenej informacie v danom stlpci

Pre spatne zbalenie informacie admin klikne v stlpci courses na tlacidlo **less**, ktore zbali text.

## 1.2. Študent

- si chce pozrieť výsledky
- Potrebuje vidie´t kde sa koľko ľudí hlasi

### 1.2.1. Registrácia vyplnenim prihlášky

registrácia vyplnením prihlášky znamená, že daný Študent ešte nexeistuje
v našej databáze. Registrácia Študenta nastáva v momente, ak Študent 
vyplní prvykrát prihlášku. Študentovi bude následne zaslaný e-mail 
na potvrdenie registrácie do nášho systému spolu s údajmi pre prihlasenie.

**vstup:** Študent klikne na vyplnenie prihlášky v pravom hornom rohu.

**výstup:** Študentovi sa zobrazí formulár na vyplnenie prihlášky

**vstup:** Študent vyplní formulár na podanie prihlášky, zároveň vyplní
aj e-mail a prihlasováci heslo. Kliknutim na tlačidlo odoslať.

**výstup:** Zobrazenie správy o tom, či študent vyplnil celý formulár 
správne. Po spravnom vyplnení, je študentovi zaslaný e-mail na potvrdenie
registrácie.

**vstup:** Kliknutie na odkaz, ktorym potvrdí svoju registráciu.

**výstup:** Uživateľ je úspešne registrovaný

### 1.2.2. Prihlasovanie študenta do systému

Prihlásiť sa môže len uživateľ, ktorý vyplnil prihlášku a potvrdil 
svoju registráciu kliknutim na potvrdzovací odkaz v e-maily.

**vstup:** Uživateľ nachadzajúci sa na našom webe vyplní prihlasovacie 
meno a heslo nachadzajúce sa v pravom hornom rohu a klikne na tlačidlo
prihlásiť sa.

**výstup:** Správa o ne/úspešnom prihlasení do systému.

### 1.2.3. Vyplnenie prihlášky prihlaseným uživateľom

Prihlasený uživateľ chce podať prihlášku o rok neskôr.

**vstup:** Uživateľ klikne na tlačidlo v ľavom menu na podanie prihlášky.

**výstup:** Zobrazenie formulára na podanie prihlašky.

**vstup:** Uživateľ vyplní údaje a klikne na tlačidlo odoslať

**výstup:** Po overení údajaov je prihláška je úspešne pridaná.   

### 1.2.4. Tlač už existujúcej prihlášky

Používateľ potrebuje vytláčiť podanú prihlášku.

**vstup:** Z ľavého panelu si vyberie prihlášku, ktorú potrebuje vytlačiť

**výstup:** Uživateľoví sa zobrazia detaily a prihláške.

**vstup:** Pre tlač, uživateľ klikni na tlačidlo Print

**výstup:** Otvorí sa nová stránka, kde bude vygenerovaná prihláška a zároveň sa zobrazí nahľad tlače.

### 1.2.5. Úprava aktualnej prihlášky, ktorá je ešte platná

Na poslednú chvíľu sa uživateľ rozhodne upraviť podanú prihlášku. 

**vstup:** Z ľavého panelu si vyberie prihlášku, ktorú chce upraviť

**výstup:** Zobrazenie prihlášky

**vstup:** Uživateľ klikne na tlačidlo upraviť

**výstup:** Zobrazenie formulára, kde je možne upraviť údaje

**vstup:** Po zmenení potrebných údajov, uživateľ klikne na Potvrdiť

**výstup:** Zobrazenie zmenenej prihlášky

### 1.2.6. Upload suborov

