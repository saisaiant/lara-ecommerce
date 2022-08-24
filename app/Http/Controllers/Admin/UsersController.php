<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\Admin\UsersRequest;
use Illuminate\Database\Eloquent\Builder;

class UsersController extends Controller
{
    private string $routeResourceName = 'users';

    public function __construct()
    {
        $this->middleware('can:view users list')->only('index');
        $this->middleware('can:create user')->only(['create', 'store']);
        $this->middleware('can:edit user')->only(['edit', 'update']);
        $this->middleware('can:delete user')->only('destroy');
    }

    public function index(Request $request)
    {
        $users = User::query()
        ->select([
            'id',
            'name',
            'email',
            'created_at'
        ])
        ->with(['roles:roles.id,roles.name'])
        ->when($request->name, fn(Builder $builder, $name) => $builder->where('name', 'like', "%{$name}%"))
        ->latest('id')
        ->paginate(10);

        return Inertia::render('User/Index', [
            'title' => 'Users',
            'items' => UserResource::collection($users),
            'headers' => [
                [
                    'label' => 'Name',
                    'name' => 'name'
                ],
                [
                    'label' => 'Email',
                    'name' => 'headers'
                ],
                [
                    'label' => 'Role',
                    'name' => 'role'
                ],
                [
                    'label' => 'Created At',
                    'name' => 'created_at'
                ],
                [
                    'label' => 'Actions',
                    'name' => 'actions'
                ]
            ],
            'filters' => (object) $request->all(),
            'routeResourceName' => $this->routeResourceName,
            'can' => [
                'create' => $request->user()->can('create user')
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Create', [
            'title' => 'Add User',
            'edit' => false,
            'routeResourceName' => $this->routeResourceName,
            'roles' => RoleResource::collection(Role::get(['id', 'name'])),
        ]);
    }

    public function store(UsersRequest $request)
    {
       $user = User::create($request->safe()->only(['name', 'email', 'password']));
       $user->assignRole($request->roleId);

        return redirect()->route("admin.{$this->routeResourceName}.index")->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $user->load(['roles:roles.id']);
        return Inertia::render('User/Create', [
            'edit' => true,
            'title' => 'Edit User',
            'item' => new UserResource($user),
            'routeResourceName' => $this->routeResourceName,
            'roles' => RoleResource::collection(Role::get(['id', 'name'])),
        ]);
    }

    public function update(UsersRequest $request, User $user)
    {
        $user->update($request->safe()->only(['name', 'email', 'password']));
        $user->syncRoles($request->roleId);

        return redirect()->route("admin.{$this->routeResourceName}.index")->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }
}