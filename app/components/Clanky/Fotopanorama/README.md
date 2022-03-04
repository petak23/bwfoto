# Komponenta: Fotopanoráma k článku

*Zoprazenie fotopanorám na stránke článku vo vue.js.*

**Inštalácia**
1. nakopírovanie archývu do `app\components`,
2. do `app\AdminModule\presenters\ArticlePresenter` doplniť `use PeterVojtech\Clanky\Fotopanorama\fotopanoramaTrait;`,
3. do `app\FrontModule\presenters\ClankyPresenter` doplniť `use PeterVojtech\Clanky\Fotopanorama\fotopanoramaTrait;`,
4. do `app\config\komponenty.neon` doplniť:
```neon
parameters:
  komponenty:
#...
    fotopanorama:
      nazov: 'Fotopanorámi k článku'
      jedinecna: FALSE  # Ci je mozne pridat len raz k clanku
      fa_ikonka: 'images'
      parametre:
        template:
          nazov: 'Názov vzhľadu'
#          hodnoty: 
#            default: 'Základný'
#            bez_avatara: 'Len odkaz bez avatara'
#            to_foto_galery: 'Odkaz na fotogalériu'
#            to_article: 'Odkaz na článok'
#            foto_album: 'Odkaz pre fotogalériu'
#...

# Component Clanky\Fotopanorama
  - PeterVojtech\Clanky\Fotopanorama\IFotopanoramaControl
```