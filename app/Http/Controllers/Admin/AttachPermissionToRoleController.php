<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class AttachPermissionToRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:edit role');
    }

    public function __invoke(Request $request)
    {
        $permission = Permission::findById($request->permissionId);

        $permission->assignRole($request->roleId);

        return redirect()->back()->with('success', 'Permission attached successfully');
    }
}
