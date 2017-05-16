<?php
$GLOBALS['TL_HOOKS']['translateUrlParameters'][] = array('iMi\MMChangeLanguage\ImiMMChangeLanguageObserver', 'translateMMUrlsV2');
$GLOBALS['TL_HOOKS']['changelanguageNavigation'][] = array('iMi\MMChangeLanguage\ImiMMChangeLanguageObserver', 'translateMMUrlsV3');
