<?php

namespace iMi\MMChangeLanguage;


class Observer
{

    private function getCurrentMetamodels() {
        global $objPage;

        $serviceContainer = $GLOBALS['container']['metamodels-service-container'];

        $curModel = array();
        $factory = \MetaModels\Factory::getDefaultFactory();

        $layout = \LayoutModel::findByPk($objPage->layout);
        $modules = unserialize($layout->modules);

        foreach ($modules as $module) {
            $objModule = ( \ModuleModel::findByPk($module['mod'] ));
            if ($objModule->metamodel_layout) {
                $modelName = $factory->translateIdToMetaModelName($objModule->metamodel);
                if ($modelName) $curModel[] = $modelName;
            };
        }

        $objArticles = \ArticleModel::findPublishedByPidAndColumn($objPage->id, 'main');
        if ($objArticles) {
            foreach($objArticles as $article ) {
                $contents = \ContentModel::findPublishedByPidAndTable($article->id, 'tl_article');
                if ($contents) {
                    foreach( $contents as $content ) {
                        $modelName = $factory->translateIdToMetaModelName($content->metamodel);

                        if (!$modelName) {
                            continue;
                        }

                        $filterCollection = $serviceContainer
                            ->getFilterFactory()
                            ->createCollection($content->metamodel_filtering);

                        // find out the attribute name for the auto_item parameter (if used)
                        $parameters = $filterCollection->getParameters();
                        $attributes = $filterCollection->getReferencedAttributes();
                        $autoItemIndex = array_search('auto_item', $parameters);
                        if ($autoItemIndex !== false) {
                            $attributeName = $attributes[$autoItemIndex];
                            $curModel[$modelName] = $attributeName;
                        }

                    };
                }
            }
        };

        return $curModel;
    }

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

        $currentMetaModels = $this->getCurrentMetamodels();

        // allow overwriting of the auto-detected definition
        if (isset($GLOBALS['TL_CONFIG']['mm_changelanguage'])) {
            $currentMetaModels = array_merge($currentMetaModels, $GLOBALS['TL_CONFIG']['mm_changelanguage']);
        }
        foreach($currentMetaModels as $modelName=>$attributeName) {
            $metaModel = \MetaModels\Factory::byTableName($modelName);
            $attribute = $metaModel->getAttribute($attributeName); // your attribute name here.
            // Only for safety here - You most definitely know that your alias is translated. ;)
            if (!in_array('MetaModels\Attribute\ITranslated', class_implements($attribute))) {
                continue;
            }
            $ids = $attribute->searchForInLanguages($alias, array($GLOBALS['TL_LANGUAGE']));
            if (count($ids) < 1) {
                continue;;
            }
            $attributeData = array_shift($attribute->getTranslatedDataFor($ids, $strLanguage));

            if (is_null($attributeData)) {
                // this requires https://github.com/terminal42/contao-changelanguage/pull/48
                $arrParams['hide'] = true;
            } else {
                $value = $attributeData['value'];
                // Override URL parameter now.
                $GLOBALS['TL_CONFIG']['useAutoItem'] = $uri[0];
                $arrParams['url'] = array($value);
            }
            return $arrParams;
        }

        return $arrParams;
    }
}