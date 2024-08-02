<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function runCommand()
    {
        $targetDirectory = '/var/www/cms/wb_cms';

        $commands = [
            'ls',
            'git pull',
            'php artisan optimize:clear',
            'ls'
        ];

        $command = "cd $targetDirectory && " . implode(' && ', $commands) . " 2>&1";

        $output = shell_exec($command);
        // return $output;
        return response()->json([
            'output' => nl2br($output)
        ]);

    }
}
