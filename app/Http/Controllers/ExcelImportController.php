<?php

namespace App\Http\Controllers;

use App\Imports\MetaImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportController extends Controller
{
    public function importMeta()
    {
        $import = new MetaImport();

        // Excel file should be in: storage/app/meta.xlsx
        Excel::import($import, storage_path('app/meta.xlsx'));

        return response()->json([
            'message' => 'Data imported successfully!',
            'rows' => $import->data,
        ]);
    }
}
