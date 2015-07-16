Contao Module imi_mm_changelanguage
====================================

This module adds compatibility between MetaModels (tng branch) and changelanguage

Repository link: t.b.a

Installation
------------

Install the module as usual using the composer client with the module name `imi/imi_mm_changelanguage` in the backend.


Configuration
-------------

The module automatically detects the meta models which used on the current page
and whether a parameter `auto_item` is used as a filter. This parameter's
value is then translated.

The autodetection can be overwritten via `$GLOBALS['TL_CONFIG']['mm_changelanguage']` in `initconfig.php`. It must be an array with `$metamodelName => $attributeName`.

About Us
=================

[iMi digital GmbH](http://www.imi.de/) offers Contao related open source modules. If you are confronted with any bugs, you may want to open an issue here.

In need of support or an implementation of a modul in an existing system, [free to contact us](mailto:digital@iMi.de). In this case, we will provide full service support for a fee.

Of course we provide development of closed-source moduls as well.


Contao Modul imi_mm_changelanguage
==================================

Dieses Modul macht das Modul Changelanguage mit den MetaModels kompatibel.

Installation
------------

Installieren Sie das Modul mit dem Composer Client. Der Modulname lautet `imi/imi_mm_changelanguage`

Bekannte Probleme
-----------------

siehe oben (Englisch)

Über iMi digital
================

[iMi digital GmbH](http://www.imi.de/) bietet eine breite Auswahl an verschiedenen Open-Source-Module für Contao an. Beim Auftreten von Fragen oder Bugs kann hier sehr gerne ein Thread geöffnet werden.

[Kontaktieren Sie uns](mailto:digital@iMi.de) gerne, wenn Sie Support für die Implementierung eines Moduls in ein bereits bestehendes Shop-System benötigen.In diesem Fall bieten wir einen kostenpflichtigen Full-Service-Support an.
