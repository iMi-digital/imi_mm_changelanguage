<?php

namespace iMi\MMChangeLanguage;


class ImiMMChangeLanguageObserver
{

    /**
     * Detect the attribute name for the auto_item parameter which is used in the filter
     *
     * @param $filterId Filter ID
     * @return bool/string
     */
    protected function detectFilterAttribute($filterId) {
        $filterCollection = \Contao\System::getContainer()
            ->get('metamodels.filter_setting_factory')
            ->createCollection($filterId);

        // find out the attribute name for the auto_item parameter (if used)
        $parameters = $filterCollection->getParameters();
        $attributes = $filterCollection->getReferencedAttributes();
        $autoItemIndex = array_search('auto_item', $parameters);
        if ($autoItemIndex !== false) {
            $attributeName = $attributes[$autoItemIndex];
            return $attributeName;
        }

        return false;
    }

    /**
     * @return \MetaModels\IFactory
     */
    protected function getMMFactory()
    {
	return \Contao\System::getContainer()->get('metamodels.factory');
    }

    /**
     * Detect meta models which are used in the current page
     * - via layout modules
     * - via content elements
     *
     * @return array metamodel name => attribute name
     */
    protected function getCurrentMetamodels() {
        global $objPage;

        $curModel = array();
        $factory = $this->getMMFactory();

        $layout = \LayoutModel::findByPk($objPage->layout);
        $modules = unserialize($layout->modules);

        foreach ($modules as $module) {
            $objModule = ( \ModuleModel::findByPk($module['mod'] ));
            if ($objModule->metamodel_layout) {
                $modelName = $factory->translateIdToMetaModelName($objModule->metamodel);
                $filterAttribute = $this->detectFilterAttribute($objModule->metamodel_filtering);
                if ($filterAttribute !== false) {
                    $curModel[$modelName] = $filterAttribute;
                }
            };
        }

        $objArticles = \ArticleModel::findPublishedByPidAndColumn($objPage->id, 'main');
        if ($objArticles) {
            foreach($objArticles as $article ) {
                $contents = \ContentModel::findPublishedByPidAndTable($article->id, 'tl_article');
                if ($contents) {
                    foreach( $contents as $content ) {
                        if ($content->type == 'module') { // resolve insert module
                            $objModule = ( \ModuleModel::findByPk($content->module));
                            if ($objModule->metamodel_layout) {
                                $modelName = $factory->translateIdToMetaModelName($objModule->metamodel);
                                $filterAttribute = $this->detectFilterAttribute($objModule->metamodel_filtering);
                                if ($filterAttribute !== false) {
                                    $curModel[$modelName] = $filterAttribute;
                                }
                            };
                            continue;
                        }

                        $modelName = $factory->translateIdToMetaModelName($content->metamodel);
                        if (!$modelName) {
                            continue;
                        }

                        $filterAttribute = $this->detectFilterAttribute($content->metamodel_filtering);
                        if ($filterAttribute !== false) {
                            $curModel[$modelName] = $filterAttribute;
                        }
                    };
                }
            }
        };

        return $curModel;
    }


	/**
	 * For the new changelanguage v3
	 *
	 * @param \Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent $event
	 */
	public function translateMMUrlsV3(
		\Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent $event
	) {
		// The target root page for current event
		$targetRoot = $event->getNavigationItem()->getRootPage();
		$targetLanguage   = $targetRoot->language; // The target language

        $factory = $this->getMMFactory();

        $currentMetaModels = $this->getCurrentMetamodels();

		$alias = \Input::get('auto_item');
		if ($alias == null) {
			return;
		}

		// allow overwriting of the auto-detected definition
		if (isset($GLOBALS['TL_CONFIG']['mm_changelanguage'])) {
			$currentMetaModels = array_merge($currentMetaModels, $GLOBALS['TL_CONFIG']['mm_changelanguage']);
		}

		foreach($currentMetaModels as $modelName=>$attributeName) {
			$metaModel = $factory->getMetaModel($modelName);
			$attribute = $metaModel->getAttribute($attributeName); // your attribute name here.
			// Only for safety here - You most definitely know that your alias is translated. ;)
			if (!in_array('MetaModels\Attribute\ITranslated', class_implements($attribute))) {
                continue;
			}

			$arrLanguages = array($GLOBALS['TL_LANGUAGE']);

			// we need this for fallback processing
			// see also https://github.com/MetaModels/core/issues/1092 (all_langs does not help here)
			$strFallbackLanguage = $metaModel->getFallbackLanguage();
			array_unshift($arrLanguages, $strFallbackLanguage);

			// find the current language's metamodel (current language, with fallback if it is "virtual" i.e. date not yet copied)
			$ids = $attribute->searchForInLanguages($alias, $arrLanguages);
			if (count($ids) < 1) {
				continue;
			}

			$attributeData = array_shift($attribute->getTranslatedDataFor($ids, $targetLanguage));
			if ($attributeData == null) {
				$attributeData = array_shift($attribute->getTranslatedDataFor($ids, $strFallbackLanguage));
			}

			if (is_null($attributeData)) {
			    continue;
			} else {
				$value = $attributeData['value'];
				// Override URL parameter now.
				$event->getUrlParameterBag()->setUrlAttribute('items', $value);
				return;
			}
		}

		// fallback - if we do not have any translated values: use untranslated alias
        $event->getUrlParameterBag()->setUrlAttribute('items', $alias);
	}
}
