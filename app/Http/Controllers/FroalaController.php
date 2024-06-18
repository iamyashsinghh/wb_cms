<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FroalaController extends Controller
{
    public function uploadImage(Request $request)
    {
        $image = $request->file('file');
        $path = $image->store('images/flora', 'public');

        return response()->json(['link' => asset('storage/'.$path)], 200);
    }

    public function uploadVideo(Request $request)
    {
        $video = $request->file('file');
        $path = $video->store('videos', 'public');

        return response()->json(['link' => asset('storage/'.$path)], 200);
    }

    public function loadImages()
    {
        $images = Storage::files('public/images/flora');
        $imageData = array_map(function ($image) {
            $url = asset('storage/'.str_replace('public/', '', $image));

            return [
                'url' => $url,
                'thumb' => $url,
            ];
        }, $images);

        return response()->json($imageData);
    }

    public function deleteImage(Request $request)
    {
        $src = $request->input('src');
        $filename = str_replace(asset('storage/'), '', $src);
        $path = 'public/'.$filename;

        if (Storage::exists($path)) {
            Storage::delete($path);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'File not found.'], 404);
    }
}
