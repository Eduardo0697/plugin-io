<?php

namespace IO\Services\ItemSearch\SearchPresets;

use IO\Services\ItemSearch\Extensions\ContentCacheVariationLinkExtension;
use IO\Services\ItemSearch\Factories\VariationSearchFactory;
use IO\Services\ItemSearch\Helper\SortingHelper;

/**
 * Class TagItems
 *
 * Search preset for tagged variations
 * Available options (see VariationList for inherited options)
 * - tagIds: List of tag ids to get assigned items for
 *
 * @package IO\Services\ItemSearch\SearchPresets
 */
class TagItems extends VariationList
{
    public static function getSearchFactory($options)
    {
        $tagIds = [];
        if ( array_key_exists('tagIds', $options ) )
        {
            $tagIds = $options['tagIds'];
        }
    
        $sorting = $options['sorting'];
        if ( array_key_exists('sorting', $options ) )
        {
            $sorting = SortingHelper::splitPathAndOrder($options['sorting']);
        }
        
        /** @var VariationSearchFactory $factory */
        $factory = parent::getSearchFactory($options)
            ->hasAnyTag($tagIds)
            ->sortBy($sorting['path'], $sorting['order'])
            ->groupByTemplateConfig();

        return $factory;
    }
}