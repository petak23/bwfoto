# Nákupný košík

## Session

Nákupný košík vaužíva session k ukladaniu objednávky.

-   basket-item-x - produkty v košíku,
-   basket-adress - adresa nakupujúceho,
-   basket-nav - navigácia a jej sprístupnenie.

## Horná navigácia

Formát prichádzajúcich dát:

    {
    	id: x, // Id položky, ktorej sa týka zmena
    	enabled: true|false, // Povolenie alebo zakázanie položky
    	view_part: y, // Ktorá položka sa má zobraziť (nepovinné)
    	disable_another: true|false // Či sa majú ostatné položky zakázať (nepovinné)
    }
