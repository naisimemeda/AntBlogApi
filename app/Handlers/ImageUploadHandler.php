<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Storage;
use  Illuminate\Support\Str;

class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

    public function save($file, $folder)
    {
        $folder_name = "images/$folder/";

        $upload_path = storage_path() . '/' . $folder_name;

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $filename = $file->getClientOriginalName();

        if ( ! in_array($extension, $this->allowed_ext)) {
            return false;
        }

        $file->move($upload_path, $filename);

        $path        = $upload_path . $filename;
        $oss_upload  = $folder_name . $filename;

        $disk = Storage::disk('qiniu');
        $disk->put($oss_upload, fopen($path, 'r'));
        unlink($path);

        return [
            'path' => 'http://' . env('QINIU_CDN') . '/'. $oss_upload
        ];
    }
}
