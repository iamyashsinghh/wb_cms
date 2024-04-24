<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\C_Number;

class CompanyNumber extends Controller
{
    public function list()
    {
        return view('company_numbers.list');
    }
    public function ajax_list()
    {
        $c_num = C_Number::select(
            'id',
            'tata_numbers',
            'is_next'
        );
        return datatables($c_num)->make(false);
    }

    public function destroy($id)
    {
        $c_num = C_Number::find($id);
        if (!$c_num) {
            return response()->json(['message' => 'Number not found.'], 404);
        }
        if ($c_num->delete()) {
            $msg = 'Number deleted successfully.';
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } else {
            $msg = 'Unable to delete.';
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $msg]);
        }
        return redirect()->route('c_num.list');
    }

    public function manage_process(Request $request)
    {
        $c_num_id = $request->phone_number_id;
        $c_num = ($c_num_id > 0) ? C_Number::find($c_num_id) : new C_Number();

        $c_num->tata_numbers = $request->phone_number;
        $c_num->save();

        $message = ($c_num_id > 0) ? 'Number updated successfully.' : 'Number added successfully.';

        session()->flash('status', [
            'success' => true,
            'alert_type' => 'success',
            'message' => $message,
        ]);

        return redirect()->route('c_num.list');
    }
}

