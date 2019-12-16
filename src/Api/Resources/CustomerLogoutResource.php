<?php //strict

namespace IO\Api\Resources;

use Plenty\Modules\Webshop\Frontend\Services\AuthenticationService;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use IO\Api\ApiResource;
use IO\Api\ApiResponse;
use IO\Api\ResponseCode;

/**
 * Class CustomerLogoutResource
 * @package IO\Api\Resources
 */
class CustomerLogoutResource extends ApiResource
{
    /** @var AuthenticationService $authService */
    private $authService;

    /**
     * CustomerLogoutResource constructor.
     *
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
     * @return Response
     */
    public function store(): Response
    {
        $result = $this->authService->logout();

        if ($result) {
            return $this->response->create(ResponseCode::OK);
        }

        return $this->response->create(null, ResponseCode::UNPROCESSABLE_ENTITY);
    }

}
