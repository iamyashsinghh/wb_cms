<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return response()->json(['success' => 'Permission created successfully.']);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->name = $request->name;
        $permission->save();

        return response()->json(['success' => 'Permission updated successfully.']);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['success' => 'Permission deleted successfully.']);
    }
}
