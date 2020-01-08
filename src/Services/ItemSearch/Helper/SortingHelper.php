<?php

namespace IO\Services\ItemSearch\Helper;

use IO\Services\ItemSearch\Factories\BaseSearchFactory;
use IO\Services\TemplateConfigService;

/**
 * Class SortingHelper
 *
 * Generate sorting values from plugin configuration.
 *
 * @package IO\Services\ItemSearch\Helper
 */
class SortingHelper
{
    /**
     * Get sorting values from plugin configuration
     *
     * @param string $sortingConfig The configuration value from plugin
     * @param string $configKeyPrefix
     * @return array
     */
    public static function getSorting($sortingConfig = null, $configKeyPrefix = 'sorting.priorityCategory')
    {
        $sortings = [];
        if ($sortingConfig === 'default.recommended_sorting' || !strlen($sortingConfig)) {
            /** @var TemplateConfigService $templateConfigService */
            $templateConfigService = pluginApp(TemplateConfigService::class);

            foreach ([1, 2, 3] as $priority) {
                $defaultSortingValue = $templateConfigService->get($configKeyPrefix . $priority);
                if ($defaultSortingValue !== 'notSelected' && !is_null($defaultSortingValue)) {
                    $defaultSorting = self::getSorting($defaultSortingValue, $configKeyPrefix);
                    $sortings[] = $defaultSorting[0];
                }
            }
        } else {
            list($sortingField, $sortingOrder) = explode('_', $sortingConfig);
            if ($sortingField === 'item.score') {
                $sortingField = '_score';
                $sortingOrder = BaseSearchFactory::SORTING_ORDER_DESC;
            } elseif ($sortingField === 'texts.name') {
                $sortingField = self::getUsedItemName();
            }

            $sortings[] = ['field' => $sortingField, 'order' => $sortingOrder];
        }

        return $sortings;
    }

    /**
     * Get sorting values for categories from config
     *
     * @param string $sortingConfig The configuration value
     * @return array
     */
    public static function getCategorySorting($sortingConfig = null)
    {
        return self::getSorting($sortingConfig);
    }

    /**
     * Get sorting values for searches from config
     *
     * @param string $sortingConfig The configuration value
     * @return array
     */
    public static function getSearchSorting($sortingConfig = null)
    {
        return self::getSorting($sortingConfig, 'sorting.prioritySearch');
    }

    /**
     * @return string
     */
    public static function getUsedItemName()
    {
        $templateConfigService = pluginApp(TemplateConfigService::class);

        $usedItemNameIndex = $templateConfigService->get('item.name');

        $usedItemName = [
            'texts.name1',
            'texts.name2',
            'texts.name3'
        ][$usedItemNameIndex];

        return $usedItemName;
    }

    public static function splitPathAndOrder($sorting)
    {
        $e = explode('_', $sorting);

        $sorting = [
            'path' => $e[0],
            'order' => $e[1]
        ];

        if ($sorting['path'] == 'texts.name') {
            $sorting['path'] = self::getUsedItemName();
        }

        return $sorting;
    }
}