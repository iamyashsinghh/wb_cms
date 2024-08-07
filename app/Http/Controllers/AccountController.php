<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccountController extends Controller
{
    public function ajax_list()
    {
        $data = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.login_start_time',
            'users.login_end_time',
            'users.is_all_time_login',
            'users.status',
            'users.id as action',
        )->where('is_admin', '1');
        $data = $data->get();
        return datatables($data)->make(false);
    }

    public function manage($account_id = 0)
    {
        if ($account_id > 0) {
            $page_heading = "Edit Account";
            $meta = User::find($account_id);
            if ($meta) {
                $meta->role_names = $meta->getRoleNames()->toArray();
            } else {
                $meta = $this->initializeMeta();
            }
        } else {
            $page_heading = "Add Account";
            $meta = $this->initializeMeta();
        }

        $roles = Role::all();
        return view('account_control.manage', compact('meta', 'page_heading', 'roles'));
    }

    public function manage_process(Request $request, $meta_id = 0)
    {
        $user = Auth::user();
        if(!$user->hasRole('admin')){    // this is the working function but vs code is showing error
            abort('403');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $meta_id,
            'phone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $user = User::find($meta_id) ?? new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->is_admin = '1';
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Sync roles using Spatie methods
            $user->syncRoles($request->roles);

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Account updated successfully.']);
        } catch (\Exception $e) {
            session()->flash('status', ['success' => false, 'alert_type' => 'danger', 'message' => $e->getMessage()]);
        }

        return redirect()->back();
    }

    public function delete($user_id){
        try{
            $user = User::find($user_id)->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Account deleted successfully.']);
        }catch (\Exception $e){
            session()->flash('status', ['success' => false, 'alert_type' => 'danger', 'message' => $e->getMessage()]);
        }
        return redirect()->back();
    }

    public function updateLoginTime(Request $request)
    {
        $user = User::find($request->user_id);

        if ($request->type == 'start') {
            $user->login_start_time = $request->value;
        } else if ($request->type == 'end') {
            $user->login_end_time = $request->value;
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Login time updated successfully.']);
    }


    public function updateIsAllTimeLogin($user_id, $value) {
        $user = User::find($user_id);
        if (!$user) {
            return abort(404);
        }

        $user->is_all_time_login = $value;
        $user->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Is all time login updated."]);
        return redirect()->back();
    }

    public function updateStatus($user_id, $value) {
        $user = User::find($user_id);
        if (!$user) {
            return abort(404);
        }

        $user->status = $value;
        $user->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => "Account Status Updated."]);
        return redirect()->back();
    }

    private function initializeMeta()
    {
        return json_decode(json_encode([
            'id' => '',
            'name' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
            'roles' => []
        ]));
    }

    public function validatePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['valid' => false, 'message' => 'Invalid phone number format.']);
        }

        $phoneExists = User::where('phone', $request->phone)->exists();

        if ($phoneExists) {
            return response()->json(['valid' => false, 'message' => 'Phone number already exists.']);
        }

        return response()->json(['valid' => true, 'message' => 'Phone number is valid.']);
    }


}
