# Komponenta: Foto koláž k článku

*Vytvorenie foto koláže na stránke článku vo vue.js.*

**Inštalácia**
1. nakopírovanie archývu do `app\components`,
2. do `app\AdminModule\presenters\ArticlePresenter` doplniť `use PeterVojtech\Clanky\Fotocollage\fotocollageTrait;`,
3. do `app\FrontModule\presenters\ClankyPresenter` doplniť `use PeterVojtech\Clanky\Fotocollage\fotocollageTrait;`,
4. do `app\config\komponenty.neon` doplniť:
```neon
parameters:
  komponenty:
#...
    fotocollage:
      nazov: 'Foto koláž k článku'
      jedinecna: FALSE  # Ci je mozne pridat len raz k clanku
      fa_ikonka: 'images'
      parametre: null
#        id_hlavne_menu: 
#          nazov: 'Id článku'
#...

# Component Clanky\Fotogalery
  - PeterVojtech\Clanky\Fotocollage\IFotocollageControl
```