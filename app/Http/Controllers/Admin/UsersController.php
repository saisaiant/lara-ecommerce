<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
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
        ]);
    }

    public function store(UsersRequest $request)
    {
        // dd(123);
        User::create($request->validated());

        return redirect()->route("admin.{$this->routeResourceName}.index")->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return Inertia::render('User/Create', [
            'edit' => true,
            'title' => 'Edit User',
            'item' => new UserResource($user),
            'routeResourceName' => $this->routeResourceName,
        ]);
    }

    public function update(UsersRequest $request, User $user)
    {
        $user->update($request->validated());

        return redirect()->route("admin.{$this->routeResourceName}.index")->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }
}
