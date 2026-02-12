<?php

namespace App\Http\Controllers\User;

use App\Helpers\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\Dashboard\DashboardService;
use App\Services\User\UserService;
use App\Traits\CrudOperationNotificationAlert;
use App\Traits\RoleManipulation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    use CrudOperationNotificationAlert;
    use RoleManipulation;

    public function __construct(
        protected UserService $userService,
    ) {}

    /**
     * Display User Dashboard
     */
    public function index(): View
    {
        return view('pages.user.dashboard');
    }

    /**
     * Display Admins List View
     */
    public function list(Request $request): View
    {
        $roles = UserRole::getPossibleRolesForAssining();
        $showInactive = $request->boolean('showInactive', false);

        return view('pages.user.list', compact('roles', 'showInactive'));
    }

    /**
     * Handles the storage of an user request and returns a response.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->back();
    }

    /**
     * Display the edit user form page.
     */
    public function edit(User $user): View
    {
        if ($user->id === Auth::id()) {
            abort(404, 'user not found');
        }
        $roles = UserRole::getPossibleRolesForAssining();
        $userRole = $user->roles->first()?->name;

        return view('pages.user.edit-user', compact('user', 'roles', 'userRole'));
    }

    /**
     * Handles the update of a user and returns back a response.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return Redirect::back();
    }

    /**
     * Toggle user active status (activate/deactivate)
     */
    public function toggleStatus(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->input('id'));

        $this->userService->toggleStatus($user);

        return Redirect::back();
    }
}
