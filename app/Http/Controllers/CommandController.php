<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function runCommand()
    {
        $targetDirectory = '/var/www/cms/wb_cms';

        $commands = [
            'ls -l',
            'pwd',
            'whoami',
        ];

        $command = "cd $targetDirectory && " . implode(' && ', $commands) . " 2>&1";

        $output = shell_exec($command);

        return response()->json([
            'output' => nl2br($output)
        ]);
    }
}
