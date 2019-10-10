<?php

namespace App\Http\Controllers\API;

use App\Criteria\CleanifyRequest\FilterByUserCriteria;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CleanifyRequest\CreateRequest;
use App\Http\Requests\API\CleanifyRequest\ListRequest;
use App\Models\Settings;
use App\Repositories\CleanifyRequestRepository;
use App\Repositories\TemplateRepository;
use App\Transformers\CleanifyRequestTransformer;
use Illuminate\Http\Response;

/**
 * Class CleanifyRequestAPIController
 * @package App\Http\Controllers\API
 */
class CleanifyRequestAPIController extends AppBaseController
{
    /** @var  CleanifyRequestRepository */
    private $repo;
    /**
     * @var CleanifyRequestTransformer
     */
    private $transformer;

    /**
     * CleanifyRequestAPIController constructor.
     * @param CleanifyRequestRepository $repo
     * @param CleanifyRequestTransformer $transf
     */
    public function __construct(CleanifyRequestRepository $repo, CleanifyRequestTransformer $transf)
    {
        $this->repo = $repo;
        $this->transformer = $transf;
    }

    /**
     * @SWG\Get(
     *      path="/cleanify",
     *      summary="Get a listing of the cleanify requests.",
     *      tags={"CleanifyRequest"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/CleanifyRequest")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param ListRequest $request
     * @return Response
     * @throws /Exception
     */
    public function index(ListRequest $request)
    {
        $this->repo->pushCriteria(new FilterByUserCriteria($request));

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $reqs = $this->repo->with([
            'user.resident',
        ])->paginate($perPage);

        $out = $this->transformer->transformPaginator($reqs);
        return $this->sendResponse($out, __('models.cleanify.retrieved'));
    }

    /**
     * @SWG\Post(
     *      path="/cleanify",
     *      summary="Store a newly created cleanify request in storage",
     *      tags={"CleanifyRequest"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Request that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CleanifyRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CleanifyRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     *
     * @param CreateRequest $request
     * @param TemplateRepository $tRepo
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRequest $request, TemplateRepository $tRepo)
    {
        $creq = $this->repo->create([
            'user_id' => \Auth::id(),
            'form' => $request->all(),
        ]);
        $settings = Settings::first();
        if (empty($settings)) {
            return $this->sendError('Real estate settings not found');
        }

        $this->repo->notify($creq, $settings->cleanify_email);
        $out = $this->transformer->transform($creq);
        return $this->sendResponse($out, __('models.cleanify.saved'));
    }
}
