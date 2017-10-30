<?php

namespace Litepie\Workflow\Http\Controllers\Api;

use App\Http\Controllers\Api\ResourceController as BaseController;
use Litepie\Workflow\Http\Requests\WorkflowRequest;
use Litepie\Workflow\Interfaces\WorkflowRepositoryInterface;
use Litepie\Workflow\Models\Workflow;

/**
 * Admin API controller class.
 */
class WorkflowResourceController extends BaseController
{
    /**
     * Initialize workflow controller.
     *
     * @param type WorkflowRepositoryInterface $workflow
     *
     * @return type
     */
    public function __construct(WorkflowRepositoryInterface $workflow)
    {
        $this->repository = $workflow;
        parent::__construct();
    }

    /**
     * Display a list of workflow.
     *
     * @return json
     */
    public function index(WorkflowRequest $request)
    {
        $workflows  = $this->repository
            ->setPresenter('\\Litepie\\Workflow\\Repositories\\Presenter\\WorkflowListPresenter')
            ->scopeQuery(function($query){
                return $query->orderBy('id','DESC');
            })->all();
        $workflows['code'] = 2000;
        return response()->json($workflows) 
                         ->setStatusCode(200, 'INDEX_SUCCESS');

    }

    /**
     * Display workflow.
     *
     * @param Request $request
     * @param Model   Workflow
     *
     * @return Json
     */
    public function show(WorkflowRequest $request, Workflow $workflow)
    {
        $workflow         = $workflow->presenter();
        $workflow['code'] = 2001;
        return response()->json($workflow)
                         ->setStatusCode(200, 'SHOW_SUCCESS');;

    }

    /**
     * Show the form for creating a new workflow.
     *
     * @param Request $request
     *
     * @return json
     */
    public function create(WorkflowRequest $request, Workflow $workflow)
    {
        $workflow         = $workflow->presenter();
        $workflow['code'] = 2002;
        return response()->json($workflow)
                         ->setStatusCode(200, 'CREATE_SUCCESS');

    }

    /**
     * Create new workflow.
     *
     * @param Request $request
     *
     * @return json
     */
    public function store(WorkflowRequest $request)
    {
        try {
            $attributes             = $request->all();
            $attributes['user_id']  = user_id('admin.api');
            $workflow          = $this->repository->create($attributes);
            $workflow          = $workflow->presenter();
            $workflow['code']  = 2004;

            return response()->json($workflow)
                             ->setStatusCode(201, 'STORE_SUCCESS');
        } catch (Exception $e) {
            return response()->json([
                'message'  => $e->getMessage(),
                'code'     => 4004,
            ])->setStatusCode(400, 'STORE_ERROR');
        }
    }

    /**
     * Show workflow for editing.
     *
     * @param Request $request
     * @param Model   $workflow
     *
     * @return json
     */
    public function edit(WorkflowRequest $request, Workflow $workflow)
    {
        $workflow         = $workflow->presenter();
        $workflow['code'] = 2003;
        return response()-> json($workflow)
                        ->setStatusCode(200, 'EDIT_SUCCESS');;
    }

    /**
     * Update the workflow.
     *
     * @param Request $request
     * @param Model   $workflow
     *
     * @return json
     */
    public function update(WorkflowRequest $request, Workflow $workflow)
    {
        try {

            $attributes = $request->all();

            $workflow->update($attributes);
            $workflow         = $workflow->presenter();
            $workflow['code'] = 2005;

            return response()->json($workflow)
                             ->setStatusCode(201, 'UPDATE_SUCCESS');


        } catch (Exception $e) {

            return response()->json([
                'message'  => $e->getMessage(),
                'code'     => 4005,
            ])->setStatusCode(400, 'UPDATE_ERROR');

        }
    }

    /**
     * Remove the workflow.
     *
     * @param Request $request
     * @param Model   $workflow
     *
     * @return json
     */
    public function destroy(WorkflowRequest $request, Workflow $workflow)
    {
        try {
            $t = $workflow->delete();

            return response()->json([
                'message'  => trans('messages.success.delete', ['Module' => trans('workflow::workflow.name')]),
                'code'     => 2006
            ])->setStatusCode(202, 'DESTROY_SUCCESS');

        } catch (Exception $e) {

            return response()->json([
                'message'  => $e->getMessage(),
                'code'     => 4006,
            ])->setStatusCode(400, 'DESTROY_ERROR');
        }
    }
}
