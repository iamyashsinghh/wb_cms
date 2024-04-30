<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function ajax_list() {
        $data = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.phone',
            'users.id as action',
        )->where('is_admin', '1');
        $data = $data->get();
        return datatables($data)->make(false);
    }

    public function manage($account_id = 0) {
        if ($account_id > 0) {
            $page_heading = "Edit Account";
            $meta = User::find($account_id);
        } else {
            $page_heading = "Add Account";
            $meta = json_decode(json_encode([
                'id' => '',
                'name' => '',
                'email' => '',
                'phone' => '',
                'password' => '',
            ]));
        }
        return view('account_control.manage', compact('meta', 'page_heading'));
    }

    public function manage_process(Request $request, $meta_id = 0) {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $meta_id,
            'phone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed'
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

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Account updated successfully.']);
        } catch (\Exception $e) {
            session()->flash('status', ['success' => false, 'alert_type' => 'danger', 'message' => $e->getMessage()]);
        }
        return redirect()->back();
    }

}
