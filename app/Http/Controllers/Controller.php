<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use function PHPUnit\Framework\fileExists;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getPage($path) {
        // Reconstruct the path based on the URL.
        $fullPath = public_path('heritages/' . $path);

        // Check for both .html and .txt extensions
        $htmlPath = $fullPath . '.html';
        $txtPath = $fullPath . '.txt';

        if (file_exists($htmlPath)) {
            $content = file_get_contents($htmlPath);
            return response($content)->header('Content-Type', 'text/html');
        } elseif (file_exists($txtPath)) {
            $content = file_get_contents($txtPath);
            return response($content)->header('Content-Type', 'text/plain');
        } else {
            abort(404, 'File not found');
        }
    }
}


