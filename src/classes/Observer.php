<?php

namespace iMi\MMChangeLanguage;


class Observer
{

    /**
     * Hook callback for changelanguage extension to support language switching on product reader page
     */
    public function translateMMUrls($arrParams, $strLanguage, $arrRootPage)
    {
        // Remove index.php fragment from uri and drop query parameters as we are not interested in those.
        list($fullUri) = explode('?', str_replace('index.php/', '', \Environment::get('request')), 2);
        // Handle remaining arguments, and do that only if there are exactly two.
        $uri = explode('/', $fullUri);

        // Remove language code
        if (\Config::get('addLanguageToUrl')) {
            array_shift($uri);
        }

        $alias = \Input::get('auto_item');
        if ($alias == null) {
            return $arrParams;
        }

        $metaModel = \MetaModels\Factory::byTableName('mm_landingpages');
        $attribute = $metaModel->getAttribute('alias'); // your attribute name here.
        // Only for safety here - You most definitely know that your alias is translated. ;)
        if (!in_array('MetaModels\Attribute\ITranslated', class_implements($attribute))) {
            return $arrParams;
        }
        $ids = $attribute->searchForInLanguages($alias, array($GLOBALS['TL_LANGUAGE']));
        if (count($ids) < 1) {
            return $arrParams;
        }
        $item = $metaModel->findById($ids[0]);
        $attributeData = array_shift($attribute->getTranslatedDataFor($ids, $strLanguage));

        $value = $attributeData['value'];
        // Override URL parameter now.
        $GLOBALS['TL_CONFIG']['useAutoItem'] = $uri[0];
        $arrParams['url'] = array($value);
        return $arrParams;
    }
}