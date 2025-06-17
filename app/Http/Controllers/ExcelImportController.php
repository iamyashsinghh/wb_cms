<?php

namespace App\Http\Controllers;

use App\Imports\MetaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExcelImportController extends Controller
{
    public function uploadMeta(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    try {
        $import = new \App\Imports\MetaImport();
        Excel::import($import, $request->file('excel_file'));

        return redirect()->back()->with('success', 'File uploaded and data imported successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
    }
}
}
