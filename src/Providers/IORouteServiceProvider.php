<?php //strict

namespace IO\Providers;

use IO\Controllers\CategoryController;
use IO\Extensions\Constants\ShopUrls;
use IO\Helper\RouteConfig;
use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;
use Plenty\Plugin\Routing\ApiRouter;

/**
 * Class IORouteServiceProvider
 * @package IO\Providers
 */
class IORouteServiceProvider extends RouteServiceProvider
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ShopUrls
     */
    private $shopUrls;

	public function register()
	{
	}

    /**
     * Define the map routes to templates or REST resources
     * @param Router $router
     * @param ApiRouter $api
     * @throws \Plenty\Plugin\Routing\Exceptions\RouteReservedException
     */
	public function map(Router $router, ApiRouter $api)
	{
	    $this->router = $router;
        $this->shopUrls = pluginApp(ShopUrls::class);

		$api->version(['v1'], ['namespace' => 'IO\Api\Resources'], function ($api)
		{
			$api->get('io/basket', 'BasketResource@index');
            $api->resource('io/basket/items', 'BasketItemResource');
            $api->get('io/order', 'OrderResource@index');
            $api->post('io/order', 'OrderResource@store');
			$api->get('io/order/paymentMethods', 'OrderPaymentResource@paymentMethodListForSwitch');
            $api->resource('io/order/payment', 'OrderPaymentResource');
            $api->resource('io/checkout/paymentId', 'CheckoutSetPaymentResource');
            $api->resource('io/checkout/shippingId', 'CheckoutSetShippingIdResource');
            $api->resource('io/order/contactWish', 'OrderContactWishResource');
            $api->resource('io/order/additional_information', 'OrderAdditionalInformationResource');
            $api->resource('io/order/return', 'OrderReturnResource');
            $api->resource('io/order/template', 'OrderTemplateResource');
            $api->resource('io/order/property/file', 'OrderPropertyFileResource');
            $api->get('io/checkout', 'CheckoutResource@index');
            $api->post('io/checkout', 'CheckoutResource@store');
            $api->put('io/checkout', 'CheckoutResource@update');
            $api->resource('io/category/description', 'CategoryDescriptionResource');
			$api->resource('io/checkout/payment', 'CheckoutPaymentResource');
			$api->resource('io/customer', 'CustomerResource');
			$api->resource('io/customer/address', 'CustomerAddressResource');
			$api->resource('io/customer/login', 'CustomerAuthenticationResource');
			$api->resource('io/customer/logout', 'CustomerLogoutResource');
			$api->resource('io/customer/password', 'CustomerPasswordResource');
            $api->resource('io/customer/password_reset', 'CustomerPasswordResetResource');
            $api->resource('io/customer/mail', 'CustomerMailResource');
            $api->resource('io/customer/contact/mail', 'ContactMailResource');
            $api->resource('io/customer/bank_data', 'ContactBankResource');
            $api->get('io/customer/order/list', 'CustomerOrderResource@index');
            $api->resource('io/customer/order/return', 'CustomerOrderReturnResource');
            $api->resource('io/customer/newsletter', 'CustomerNewsletterResource');
            $api->resource('io/variations', 'VariationResource');
            $api->resource('io/item/availability', 'AvailabilityResource');
            $api->resource('io/item/condition', 'ItemConditionResource');
            $api->resource('io/item/last_seen', 'ItemLastSeenResource');
            $api->get('io/item/search', 'ItemSearchResource@index');
            $api->get('io/item/search/autocomplete', 'ItemSearchAutocompleteResource@index');
			$api->resource('io/coupon', 'CouponResource');
            $api->resource('io/guest', 'GuestResource');
            $api->resource('io/category', 'CategoryItemResource');
            $api->resource('io/template', 'TemplateResource');
            $api->resource('io/localization/language', 'LanguageResource');
            $api->resource('io/itemWishList', 'ItemWishListResource');
            $api->resource('io/shipping/country', 'ShippingCountryResource');
            $api->resource('io/live-shopping', 'LiveShoppingResource');
            $api->resource('io/facet', 'FacetResource');
            $api->resource('io/categorytree', 'CategoryTreeResource');

		});

		/** @var ShopUrls $shopUrls */
		$shopUrls = pluginApp(ShopUrls::class);

		/*
		 * STATIC ROUTES
		 */
		//Basket route
        if ( RouteConfig::isActive(RouteConfig::BASKET) )
        {
            // TODO: get slug from config
            $router->get('basket', 'IO\Controllers\BasketController@showBasket');
        }

        $this->registerCustomizeableRoute(
            RouteConfig::CHECKOUT,
            'IO\Controllers\CheckoutController@showCheckout',
            $shopUrls->checkout,
            'IO\Controllers\CheckoutController@redirect'
        );

        $this->registerCustomizeableRoute(
            RouteConfig::MY_ACCOUNT,
            'IO\Controllers\MyAccountController@showMyAccount',
            $shopUrls->myAccount,
            'IO\Controllers\MyAccountController@redirect'
        );

		if ( RouteConfig::isActive(RouteConfig::CONFIRMATION) )
        {
            //Confirmation route
            $router->get('confirmation/{orderId?}/{orderAccessKey?}', 'IO\Controllers\ConfirmationController@showConfirmation');

            $router->get('-/akQQ{orderAccessKey}/idQQ{orderId}', 'IO\Controllers\ConfirmationEmailController@showConfirmation');
            $router->get('_py-/akQQ{orderAccessKey}/idQQ{orderId}', 'IO\Controllers\ConfirmationEmailController@showConfirmation');
            $router->get('_py_/akQQ{orderAccessKey}/idQQ{orderId}', 'IO\Controllers\ConfirmationEmailController@showConfirmation');
            $router->get('_plentyShop__/akQQ{orderAccessKey}/idQQ{orderId}', 'IO\Controllers\ConfirmationEmailController@showConfirmation');
        }

		if ( RouteConfig::isActive(RouteConfig::LOGIN) )
        {
            //Login page route
            $router->get('login', 'IO\Controllers\LoginController@showLogin');
        }

		if ( RouteConfig::isActive(RouteConfig::REGISTER) )
        {
            //Register page route
            $router->get('register', 'IO\Controllers\RegisterController@showRegister');
            $router->get('registration', 'IO\Controllers\RegisterController@redirectRegister');
        }

		if ( RouteConfig::isActive(RouteConfig::PLACE_ORDER) )
        {
            // PaymentPlugin entry points
            // place the current order and redirect to /execute_payment
            $router->get('place-order', 'IO\Controllers\PlaceOrderController@placeOrder');

            // execute payment after order is created. PaymentPlugins can redirect to this route if order was created by the PaymentPlugin itself.
            $router->get('execute-payment/{orderId}/{paymentId?}', 'IO\Controllers\PlaceOrderController@executePayment')
                ->where('orderId', '[0-9]+');
        }

        if ( RouteConfig::isActive(RouteConfig::SEARCH) )
        {
            $router->get('search', 'IO\Controllers\ItemSearchController@showSearch');
            //Callisto Tag route
            $router->get('tag/{tagName}', 'IO\Controllers\ItemSearchController@redirectToSearch');
        }

        if ( RouteConfig::isActive(RouteConfig::HOME) )
        {
            //homepage route
            $router->get('', 'IO\Controllers\HomepageController@showHomepage');
        }
        else if( in_array(RouteConfig::HOME, RouteConfig::getEnabledRoutes())
            && RouteConfig::getCategoryId(RouteConfig::HOME) > 0)
        {
            $router->get('', 'IO\Controllers\HomepageController@showHomepageCategory');
        }

        $this->registerCustomizeableRoute(
            RouteConfig::CANCELLATION_RIGHTS,
            'IO\Controllers\StaticPagesController@showCancellationRights',
            $shopUrls->cancellationRights,
            'IO\Controllers\StaticPagesController@redirectCancellationRights'
        );

        $this->registerCustomizeableRoute(
            RouteConfig::CANCELLATION_FORM,
            'IO\Controllers\StaticPagesController@showCancellationForm',
            $shopUrls->cancellationForm,
            'IO\Controllers\StaticPagesController@redirectCancellationForm'
        );

        $this->registerCustomizeableRoute(
            RouteConfig::LEGAL_DISCLOSURE,
            'IO\Controllers\StaticPagesController@showLegalDisclosure',
            $shopUrls->legalDisclosure,
            'IO\Controllers\StaticPagesController@redirectLegalDisclosure'
        );

        $this->registerCustomizeableRoute(
            RouteConfig::PRIVACY_POLICY,
            'IO\Controllers\StaticPagesController@showPrivacyPolicy',
            $shopUrls->privacyPolicy,
            'IO\Controllers\StaticPagesController@redirectPrivacyPolicy'
        );

        $this->registerCustomizeableRoute(
            RouteConfig::TERMS_CONDITIONS,
            'IO\Controllers\StaticPagesController@showTermsAndConditions',
            $shopUrls->termsConditions,
            'IO\Controllers\StaticPagesController@redirectTermsAndConditions'
        );


        if( RouteConfig::isActive(RouteConfig::WISH_LIST) )
        {
            $router->get('wish-list', 'IO\Controllers\ItemWishListController@showWishList');
        }

        if( RouteConfig::isActive(RouteConfig::ORDER_RETURN) )
        {
            $router->get('returns/{orderId}', 'IO\Controllers\OrderReturnController@showOrderReturn');
        }

        if( RouteConfig::isActive(RouteConfig::ORDER_RETURN_CONFIRMATION) )
        {
            $router->get('return-confirmation', 'IO\Controllers\OrderReturnConfirmationController@showOrderReturnConfirmation');
        }

        $this->registerCustomizeableRoute(
            RouteConfig::CONTACT,
            'IO\Controllers\ContactController@showContact',
            $shopUrls->contact,
            'IO\Controllers\ContactController@redirectContact'
        );

        if( RouteConfig::isActive(RouteConfig::PASSWORD_RESET) )
        {
            $router->get('password-reset/{contactId}/{hash}', 'IO\Controllers\CustomerPasswordResetController@showReset');
        }

        if( RouteConfig::isActive(RouteConfig::CHANGE_MAIL) )
        {
            $router->get('change-mail/{contactId}/{hash}', 'IO\Controllers\CustomerChangeMailController@show');
        }

        if( RouteConfig::isActive(RouteConfig::ORDER_PROPERTY_FILE) )
        {
            $router->get('order-property-file/{hash1}', 'IO\Controllers\OrderPropertyFileController@downloadTempFile');
            $router->get('order-property-file/{hash1}/{hash2}', 'IO\Controllers\OrderPropertyFileController@downloadFile');
        }
        
        if( RouteConfig::isActive(RouteConfig::ORDER_DOCUMENT) )
        {
            $router->get('order-document/preview/{documentId}', 'IO\Controllers\DocumentController@preview');
        }

        
        if( RouteConfig::isActive(RouteConfig::NEWSLETTER_OPT_IN) )
        {
            $router->get('newsletter/subscribe/{authString}/{newsletterEmailId}', 'IO\Controllers\NewsletterOptInController@showOptInConfirmation');
        }
        
        if( RouteConfig::isActive(RouteConfig::NEWSLETTER_OPT_OUT) )
        {
            $router->get('newsletter/unsubscribe', 'IO\Controllers\NewsletterOptOutController@showOptOut');
            $router->post('newsletter/unsubscribe', 'IO\Controllers\NewsletterOptOutConfirmationController@showOptOutConfirmation');
        }

        /*
         * ITEM ROUTES
         */
        if ( RouteConfig::isActive(RouteConfig::ITEM) )
        {
            $router->get('{itemId}_{variationId?}', 'IO\Controllers\ItemController@showItemWithoutName')
                ->where('itemId', '[0-9]+')
                ->where('variationId', '[0-9]+');

            $router->get('{slug}_{itemId}_{variationId?}', 'IO\Controllers\ItemController@showItem')
                ->where('slug', '[^_]+')
                ->where('itemId', '[0-9]+')
                ->where('variationId', '[0-9]+');

            //old webshop routes mapping
            $router->get('{slug}/a-{itemId}', 'IO\Controllers\ItemController@showItemOld')
                ->where('slug', '.*')
                ->where('itemId', '[0-9]+');

            $router->get('a-{itemId}', 'IO\Controllers\ItemController@showItemFromAdmin')
                ->where('itemId', '[0-9]+');
        }

        /*
         * CATEGORY ROUTES
         */
        if ( RouteConfig::isActive(RouteConfig::CATEGORY) )
        {
            $router->get('{level1?}/{level2?}/{level3?}/{level4?}/{level5?}/{level6?}', 'IO\Controllers\CategoryController@showCategory');
        }
        else
        {
            if ( RouteConfig::getCategoryId(RouteConfig::HOME) > 0 )
            {
                $router->get('', 'IO\Controllers\HomepageController@showHomepageCategory');
            }
            
            if ( RouteConfig::getCategoryId(RouteConfig::CHECKOUT) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showCheckout');
            }

            if ( RouteConfig::getCategoryId(RouteConfig::MY_ACCOUNT) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showMyAccount');
            }
    
            if ( RouteConfig::getCategoryId(RouteConfig::CANCELLATION_RIGHTS) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showCancellationRights');
            }
    
            if ( RouteConfig::getCategoryId(RouteConfig::CANCELLATION_FORM) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showCancellationForm');
            }
    
            if ( RouteConfig::getCategoryId(RouteConfig::LEGAL_DISCLOSURE) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showLegalDisclosure');
            }
    
            if ( RouteConfig::getCategoryId(RouteConfig::PRIVACY_POLICY) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showPrivacyPolicy');
            }
    
            if ( RouteConfig::getCategoryId(RouteConfig::TERMS_CONDITIONS) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showTermsConditions');
            }
    
            if ( RouteConfig::getCategoryId(RouteConfig::CONTACT) > 0 )
            {
                $router->get($shopUrls->checkout, 'IO\Controllers\CategoryController@showContact');
            }
        }

        if ( RouteConfig::isActive(RouteConfig::PAGE_NOT_FOUND) )
        {
            $router->get('{anything?}', 'IO\Controllers\StaticPagesController@showPageNotFound');
        }
	}

    /**
     * @param string $route
     * @param string $simpleAction
     * @param string $shopUrl
     * @param string $customAction
     * @throws \Plenty\Plugin\Routing\Exceptions\RouteReservedException
     */
    private function registerCustomizeableRoute(
        string $route,
        string $simpleAction,
        string $shopUrl,
        string $customAction
    )
    {
        if (RouteConfig::isActive($route))
        {
            $this->router->get($route, $simpleAction);
        }
        else if(in_array($route, RouteConfig::getEnabledRoutes())
            && RouteConfig::getCategoryId($route) > 0
            && !$this->shopUrls->equals($shopUrl,'/' . $route) )
        {
            $this->router->get($route, $customAction);
        }
    }

}
