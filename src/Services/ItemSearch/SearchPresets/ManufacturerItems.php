<?php

namespace IO\Services\ItemSearch\SearchPresets;

use IO\Contracts\VariationSearchFactoryContract as VariationSearchFactory;
use IO\Contracts\SortingContract as SortingHelper;

/**
 * Class ManufacturerItems
 * @package IO\Services\ItemSearch\SearchPresets
 * @deprecated since 5.0.0 will be deleted in 6.0.0
 * @see \Plenty\Modules\Webshop\ItemSearch\SearchPresets\ManufacturerItems
 */
class ManufacturerItems implements SearchPreset
{
    /**
     * @inheritDoc
     */
    public static function getSearchFactory($options)
    {
        $page = 1;
        if ( array_key_exists( 'page', $options ) )
        {
            $page = (int) $options['page'];
        }
       
        $sorting = '';
        if ( array_key_exists( 'sorting', $options ) )
        {
            /** @var SortingContract $sortingHelper */
            $sortingHelper = pluginApp(SortingHelper::class);
            $sorting = $sortingHelper->getSorting( $options['sorting'] );
        }

        $itemsPerPage = 20;
        if ( array_key_exists( 'itemsPerPage', $options ) )
        {
            $itemsPerPage = (int) $options['itemsPerPage'];
        }

        $manufacturerId = 1;
        if (array_key_exists( 'manufacturerId', $options))
        {
            $manufacturerId = (int) $options['manufacturerId'];
        }


        /** @var VariationSearchFactory $searchFactory */
        $searchFactory = pluginApp( VariationSearchFactory::class );

        $searchFactory
            ->withLanguage()
            ->withImages()
            ->withUrls()
            ->withPrices()
            ->withDefaultImage()
            ->isVisibleForClient()
            ->isActive()
            ->isHiddenInCategoryList( false )
            ->hasNameInLanguage()
            ->hasPriceForCustomer()
            ->sortByMultiple( $sorting )
            ->hasManufacturer( $manufacturerId )
            ->setPage( $page, $itemsPerPage )
            ->groupByTemplateConfig()
            ->withLinkToContent()
            ->withReducedResults(true);

        return $searchFactory;
    }
}

