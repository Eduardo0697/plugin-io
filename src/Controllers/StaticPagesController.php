<?php //strict
namespace IO\Controllers;

use IO\Extensions\Constants\ShopUrls;

/**
 * Class HomepageController
 * @package IO\Controllers
 */
class StaticPagesController extends LayoutController
{
    /**
     * Prepare and render the data for the cancellation rights page
     * @return string
     * @throws \ErrorException
     */
    public function showCancellationRights():string
    {
        return $this->renderTemplate(
            "tpl.cancellation-rights",
            [
                "object" => ""
            ]
        );
    }

    public function redirectCancellationRights()
    {
        return $this->redirect(pluginApp(ShopUrls::class)->cancellationRights);
    }

    private function redirect(string $url)
    {
        return pluginApp(CategoryController::class)->redirectToCategory($url);
    }

    /**
     * Prepare and render the data for the cancellation form page
     * @return string
     * @throws \ErrorException
     */
    public function showCancellationForm():string
    {
        return $this->renderTemplate(
            "tpl.cancellation-form",
            [
                "object" => ""
            ]
        );
    }

    public function redirectCancellationForm()
    {
        return $this->redirect(pluginApp(ShopUrls::class)->cancellationForm);
    }

    /**
     * Prepare and render the data for the legal disclosure page
     * @return string
     * @throws \ErrorException
     */
    public function showLegalDisclosure():string
    {
        return $this->renderTemplate(
            "tpl.legal-disclosure",
            [
                "object" => ""
            ]
        );
    }

    public function redirectLegalDisclosure()
    {
        return $this->redirect(pluginApp(ShopUrls::class)->legalDisclosure);
    }

    /**
     * Prepare and render the data for the privacy policy page
     * @return string
     * @throws \ErrorException
     */
    public function showPrivacyPolicy():string
    {
        return $this->renderTemplate(
            "tpl.privacy-policy",
            [
                "object" => ""
            ]
        );
    }

    public function redirectPrivacyPolicy()
    {
        return $this->redirect(pluginApp(ShopUrls::class)->privacyPolicy);
    }

    /**
     * Prepare and render the data for the terms and conditions page
     * @return string
     * @throws \ErrorException
     */
    public function showTermsAndConditions():string
    {
        return $this->renderTemplate(
            "tpl.terms-conditions",
            [
                "object" => ""
            ]
        );
    }

    public function redirectTermsAndConditions()
    {
        return $this->redirect(pluginApp(ShopUrls::class)->termsConditions);
    }

    /**
     * Prepare and render the data for the page not found page
     * @return string
     * @throws \ErrorException
     */
    public function showPageNotFound():string
    {
        return $this->renderTemplate(
            "tpl.page-not-found",
            [
                "object" => ""
            ],
            false
        );
    }
}
