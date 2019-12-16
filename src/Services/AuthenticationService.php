<?php //strict

namespace IO\Services;

use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contact\Models\Contact;
use Plenty\Modules\Authentication\Contracts\ContactAuthenticationRepositoryContract;
use IO\Constants\SessionStorageKeys;
use Plenty\Plugin\Log\Loggable;

/**
 * Class AuthenticationService
 * @package IO\Services
 *
 * @deprecated since 4.5.0 will be removed in 5.0.0 use Plenty\Modules\Webshop\Services\AuthenticationService instead
 * @see \Plenty\Modules\Webshop\Services\AuthenticationService
 */
class AuthenticationService
{
    use Loggable;

    /**
     * @var ContactAuthenticationRepositoryContract
     */
    private $contactAuthRepository;

    /**
     * @var SessionStorageService $sessionStorage
     */
    private $sessionStorage;

    /** @var CustomerService */
    private $customerService;

    /**
     * AuthenticationService constructor.
     * @param ContactAuthenticationRepositoryContract $contactAuthRepository
     * @param \IO\Services\SessionStorageService $sessionStorage
     */
    public function __construct(
        ContactAuthenticationRepositoryContract $contactAuthRepository,
        SessionStorageService $sessionStorage,
        CustomerService $customerService
    ) {
        $this->contactAuthRepository = $contactAuthRepository;
        $this->sessionStorage = $sessionStorage;
        $this->customerService = $customerService;
    }

    /**
     * Perform the login with email and password
     * @param string $email
     * @param string $password
     * @return int|null
     *
     * @deprecated since 4.5.0 will be removed in 5.0.0 use login() from Plenty\Modules\Webshop\Services\AuthenticationService instead
     * @see \Plenty\Modules\Webshop\Services\AuthenticationService::login()
     */
    public function login(string $email, string $password)
    {
        $this->customerService->deleteGuestAddresses();
        $this->customerService->resetGuestAddresses();

        $this->contactAuthRepository->authenticateWithContactEmail($email, $password);
        $this->sessionStorage->setSessionValue(SessionStorageKeys::GUEST_WISHLIST_MIGRATION, true);

        /** @var ContactRepositoryContract $contactRepository */
        $contactRepository = pluginApp(ContactRepositoryContract::class);

        return $contactRepository->getContactIdByEmail($email);
    }

    /**
     * Perform the login with customer ID and password
     * @param int $contactId
     * @param string $password
     *
     * @deprecated since 4.5.0 will be removed in 5.0.0 use loginWithContactId() from Plenty\Modules\Webshop\Services\AuthenticationService instead
     * @see \Plenty\Modules\Webshop\Services\AuthenticationService::loginWithContactId()
     */
    public function loginWithContactId(int $contactId, string $password): void
    {
        $this->contactAuthRepository->authenticateWithContactId($contactId, $password);
        $this->sessionStorage->setSessionValue(SessionStorageKeys::GUEST_WISHLIST_MIGRATION, true);
    }

    /**
     * Log out the customer
     *
     * @deprecated since 4.5.0 will be removed in 5.0.0 use logout() from Plenty\Modules\Webshop\Services\AuthenticationService instead
     * @see \Plenty\Modules\Webshop\Services\AuthenticationService::logout()
     */
    public function logout(): void
    {
        $this->contactAuthRepository->logout();

        /**
         * @var BasketService $basketService
         */
        $basketService = pluginApp(BasketService::class);
        $basketService->setBillingAddressId(0);
        $basketService->setDeliveryAddressId(0);
    }

    /**
     * @param string $password
     * @return bool
     *
     * @deprecated since 4.5.0 will be removed in 5.0.0 use verifyPassword() from Plenty\Modules\Webshop\Services\AuthenticationService instead
     * @see \Plenty\Modules\Webshop\Services\AuthenticationService::verifyPassword()
     */
    public function checkPassword($password): bool
    {
        /** @var CustomerService $customerService */
        $customerService = pluginApp(CustomerService::class);
        $contact = $customerService->getContact();
        if ($contact instanceof Contact) {
            try {
                $this->login(
                    $contact->email,
                    $password
                );
                return true;
            } catch (\Exception $e) {
                $this->getLogger(__CLASS__)->info(
                    'IO::Debug.AuthenticationService_invalidPassword',
                    [
                        'contactId' => $contact->id
                    ]
                );
            }
        }

        return false;
    }

    /**
     * @return bool
     *
     * @deprecated since 4.5.0 will be removed in 5.0.0 use isContactLoggedIn() from Plenty\Modules\Webshop\Services\AuthenticationService instead
     * @see \Plenty\Modules\Webshop\Services\AuthenticationService::isContactLoggedIn()
     */
    public function isLoggedIn(): bool
    {
        /** @var CustomerService $customerService */
        $customerService = pluginApp(CustomerService::class);

        $contactId = $customerService->getContactId();
        $email = $this->sessionStorage->getSessionValue(SessionStorageKeys::GUEST_EMAIL);

        return $contactId > 0 || !empty(trim($email));
    }
}
