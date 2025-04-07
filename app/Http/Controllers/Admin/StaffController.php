<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\StaffStoreRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Models\Role;
use App\Models\Traits\Status;
use App\Models\User;
use App\Traits\sendApiResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaffController extends AdminBaseController
{
    use sendApiResponse;

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $userData = session('user_data');
        return view('panel.staffs.index', ['userData' => $userData]);
    }

    public function getStafflist(): JsonResponse
    {
        $roles = Role::all();
        $staffs = User::query()->with('roles')->where('role', User::STAFF)->paginate($this->limit());

        if ($staffs->isEmpty()) {
            return $this->sendApiResponse('', 'No data available', 'NotAvailable');
        }

        return $this->sendApiResponse($staffs, '', '', ['roles' => $roles]);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $userData = session('user_data');
        $roles = Role::all();
        return view('panel.staffs.create', compact('roles'), ['userData' => $userData]);
    }

    /**
     * @param StaffStoreRequest $request
     * @return RedirectResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function store(StaffStoreRequest $request): RedirectResponse
    {

        $data = $request->validated();
        $data['role'] = User::STAFF;
        $user = User::query()->create($data);
        $user->roles()->attach($request->input('role'));

        return redirect()->route('admin.staffs');
    }

    /**
     * Using User as staff
     * @param User $user
     * @return Application|Factory|View
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        $userData = session('user_data');
        return view('panel.staffs.edit', compact('user', 'roles'), ['userData' => $userData]);
    }

    /**
     * Uses User model as staff
     * @param StaffUpdateRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(StaffUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = User::STAFF;
        $user->update($data);
        $user->roles()->detach();
        $user->roles()->attach($request->input('role'));

        return redirect()->route('admin.staffs');
    }

    /**
     * Deleting staff and detached roles
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.staffs');
    }

    public function updateStatus(Request $request)
    {
        $user = User::query()->findOrFail($request->input('id'));

        if(!$user) {
            return 0;
        }

        if($request->input('status') === Status::STATUS_ACTIVE) {
            $user->status = Status::STATUS_ACTIVE;
        }

        if($request->input('status') === Status::STATUS_INACTIVE) {
            $user->status = Status::STATUS_INACTIVE;
        }

        if($request->input('status') === Status::STATUS_BLOCKED) {
            $user->status = Status::STATUS_BLOCKED;
        }
        $user->save();

        return 1;

    }
}
