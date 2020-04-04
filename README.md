Contao Module imi_mm_changelanguage
====================================

This module adds compatibility between Contao [MetaModels](https://now.metamodel.me/) and [Terminal 42 changelanguage](terminal42/contao-changelanguage).

Example:

If you have a URL like https://example.com/products and a German fallback page https://example.com/produkte where products are added via Metamodels (https://example.com/products/vacum-cleaner), the language switcher will link to  https://example.com/produke/staubsauger and all other translated products.

Tested up to Contao 4.4 and MetaModels 2.1

Installation
------------

Install the module as usual using the composer client with the module name `imi/imi_mm_changelanguage` in the backend or using Contao Manager by searching for "imi_mm_changelanguage"


Configuration
-------------

The module automatically detects the meta models which used on the current page
and whether a parameter `auto_item` is used as a filter. This parameter's
value is then translated.

The autodetection can be overwritten via `$GLOBALS['TL_CONFIG']['mm_changelanguage']` in `initconfig.php`. It must be an array with `$metamodelName => $attributeName`.

Example:

    $GLOBALS['TL_CONFIG']['mm_changelanguage']['mm_products] = 'alias_translated';
    
About Us
=================

[iMi digital GmbH](http://www.imi.de/) offers Contao related open source modules. If you are confronted with any bugs, you may want to open an issue here.

In need of support or an implementation of a modul in an existing system, [free to contact us](mailto:a.menk@iMi.de). In this case, we will provide full service support for a fee.

Of course we provide development of closed-source moduls as well.


Contao Modul imi_mm_changelanguage
==================================

Dieses Modul macht Contao [MetaModels](https://now.metamodel.me/) und das Sprachwechsler Modul [Terminal 42 changelanguage](terminal42/contao-changelanguage) kompatibel.

Beispiel:

Wenn Sie eine URL wie https://example.com/products und eine deutsche Fallback-Seite https://example.com/produkte haben, auf der Produkte über Metamodells (https://example.com/products/vacum-cleaner) hinzugefügt werden, wird der Sprachwechsler einen Links zu https://example.com/produke/staubsauger und allen anderen übersetzten Produkten verwenden.

Installation
------------

Installieren Sie das Modul mit dem Composer Client oder Contao Manager. Der Modulname lautet `imi/imi_mm_changelanguage`

Bekannte Probleme
-----------------

siehe oben (Englisch)

Über iMi digital
================

[iMi digital GmbH](http://www.imi.de/) bietet eine breite Auswahl an verschiedenen Open-Source-Module für Contao an. Beim Auftreten von Fragen oder Bugs kann hier sehr gerne ein Thread geöffnet werden.

[Kontaktieren Sie uns](mailto:a.menk@iMi.de) gerne, wenn Sie Support für die Implementierung eines Moduls in ein bereits bestehendes CMS-System benötigen. In diesem Fall bieten wir einen kostenpflichtigen Full-Service-Support an.
