<?php //strict

namespace IO\Api\Resources;

use Plenty\Modules\Webshop\Frontend\Services\AuthenticationService;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use IO\Api\ApiResource;
use IO\Api\ApiResponse;
use IO\Api\ResponseCode;
use Plenty\Plugin\Log\Loggable;

/**
 * Class CustomerAuthenticationResource
 * @package IO\Api\Resources
 */
class CustomerAuthenticationResource extends ApiResource
{
    use Loggable;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * CustomerAuthenticationResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     * @param AuthenticationService $authService
     */
    public function __construct(Request $request, ApiResponse $response, AuthenticationService $authService)
    {
        parent::__construct($request, $response);
        $this->authService = $authService;
    }

    /**
     * Perform the login with email and password
     *
     * @return Response
     */
    public function store(): Response
    {
        $email = $this->request->get('email', '');
        $password = $this->request->get('password', '');

        $result = $this->authService->login((string)$email, (string)$password);

        if ($result) {
            return $this->response->create(null, ResponseCode::OK);
        }

        return $this->response->create(null, ResponseCode::UNAUTHORIZED);
    }

}
