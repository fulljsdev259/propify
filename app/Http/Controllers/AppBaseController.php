<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use OwenIt\Auditing\Models\Audit;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Propify API",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
/**
 *   @SWG\SecurityScheme(
 *     securityDefinition="passport-swaggervel_auth",
 *     description="OAuth2 grant provided by Laravel Passport",
 *     type="oauth2",
 *     authorizationUrl="/oauth/authorize",
 *     tokenUrl="/oauth/token",
 *     flow="accessCode",
 *     scopes={
 *       *
 *     }
 *   ),
 */
class AppBaseController extends Controller
{
    /**
     * @param $result
     * @param $message
     * @return mixed
     */
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    /**
     * @param $error
     * @param int $code
     * @return mixed
     */
    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }
    
}
