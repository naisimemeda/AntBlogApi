<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request, ImageUploadHandler $uploader)
    {
        $file = $request->file('image');
        $file = $uploader->save($file, 'avatars');
        return $this->success([
            'path' => $file['path']
        ]);
    }
}
