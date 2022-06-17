# [bwfoto.sk](http://www.bwfoto.sk)

## Nette project of webside www.bwfoto.sk

This is [Nette](https://nette.org) & [Vue.js](https://vuejs.org/) project of webside [bwfoto.sk](http://www.bwfoto.sk) created in collaboration with [Tatravisual.sk -coming soon](http://tatravisual.sk/).

## Nette projekt stránky www.bwfoto.sk

Toto je [Nette](https://nette.org) a [Vue.js](https://vuejs.org/) projekt web-stránky [bwfoto.sk](http://www.bwfoto.sk) tvorený v spolupráci s [Tatravisual.sk - už čoskoro](http://tatravisual.sk/).

## Install

- spusť príkaz `git clone https://github.com/petak23/bwfoto.git`
- presuň sa do adresára `cd bwfoto`
- vytvor podadresáre `temp` a `log` (`mkdir log temp`)
- na linuxe spusť: `chmod -R a+rw temp log` viď aj: [Nette - Nastavení práv adresářů](https://doc.nette.org/cs/troubleshooting#toc-nastaveni-prav-adresaru)
- vytvor databázu `bwfoto_new` a importuj do nej súbor `sql\bwfoto_new.sql`
- spusť `composer install`
- spusť `npm install`
- premenuj `app\cofig\config.local.neon.temp` a `app\cofig\database.neon.temp` odstránením koncovky `.temp` a vyplň v nich potrebné údaje


## Webpack

- `npm run start` - generates development bundles
- `npm run watch` - watch changes in development bundles
- `npm run serve` - starts webpack development server
- `npm run build` - generates production bundles

## Deployment

 - `php ../ftp-deployment/deployment deployment.ini`

## Komponenty tretích strán

 - [vue-photo-collage](https://github.com/seanghay/vue-photo-collage) - Mnou upravené pre moje potreby.