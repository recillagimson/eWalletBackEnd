<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Repositories\Admin\Role\IRoleRepository;
use App\Http\Requests\Admin\SetRolePermissionRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\Admin\Permission\IPermissionRepository;

class RoleController extends Controller
{
    
    private IRoleRepository $iRoleRepository;
    private IPermissionRepository $iPermissionRepository;
    private IResponseService $responseService;


    public function __construct(
                                IRoleRepository $iRoleRepository,
                                IPermissionRepository $iPermissionRepository,
                                IResponseService $responseService
                                )
    {
        // $this->encryptionService = $encryptionService;
        $this->iRoleRepository = $iRoleRepository;
        $this->iPermissionRepository = $iPermissionRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // GET REQUEST VALUES
        $params = $request->all();
        $records = $this->iRoleRepository->getAll($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $details['slug'] = Str::slug($details['name'], '-');
        $createRecord = $this->iRoleRepository->create($details);
        return $this->responseService->createdResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $Role)
    {
        return $this->responseService->successResponse($Role->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, RoleRequest $request)
    {
        $details = $request->validated();
        $updateRecord = $this->iRoleRepository->update($role, $details);
        return $this->responseService->successResponse($role->toArray(), SuccessMessages::success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $Role)
    {
        $deleteRecord = $this->iRoleRepository->delete($Role);
        return $this->responseService->noContentResponse([], SuccessMessages::recordDeleted);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  null
     * @return \Illuminate\Http\Response
     */

     public function rolePermissions() {
        $records = $this->iPermissionRepository->listPermissionsByGroup();
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
     }

     public function setRolePermission(SetRolePermissionRequest $request) {
        $records = $this->iPermissionRepository->setRolePermissions($request->all());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
     }
}
