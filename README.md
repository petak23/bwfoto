# [bwfoto.sk](http://www.bwfoto.sk)

## My Nette project of webside www.bwfoto.sk

This is my [Nette](https://nette.org) project of webside [bwfoto.sk](http://www.bwfoto.sk)...

## Môj Nette projekt stránky www.bwfoto.sk

Toto je môj [Nette](https://nette.org) projekt web-stránky [bwfoto.sk](http://www.bwfoto.sk)...

## Inštalácia

- `git clone https://github.com/petak23/bwfoto.git`
- `cd bwfoto`
- `mkdir log temp`
- pre linux: `chmod -R a+rw temp log`
- v adresáry app\config vytvor súbory config.local.neon(konfiguračné údaje pre localhost) a database.neon(konfiguračné údaje pre produkciu) s obsahom
 config.local.neon:
 ```
  database:
    dsn: 'mysql:host=localhost;dbname=bwfoto_new'
    user:	root
    password: my_strong_local_psswd
    options:
      lazy: yes

  webpack:
    devServer:
      enabled: %debugMode%
      url: http://http://localhost/bwfoto/assets
 ```
 database.neon:
 ```
  database:
    dsn: 'mysql:host=production.server.sk;dbname=bwfoto_new'
    user:	production_user
    password: production_psswd
    options:
      lazy: yes
 ```
- `composer install`
- `npm install`
- vytvor súbor deployment.ini podľa [ftp-deployment](https://github.com/dg/ftp-deployment)

## Webpack

- `npm run start` - generates development bundles
- `npm run watch` - watch changes in development bundles
- `npm run serve` - starts webpack development server
- `npm run build` - generates production bundles

## Deployment

 - `php ../ftp-deployment/deployment deployment.ini`