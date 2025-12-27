<?php

// Author: Ivan Goh Shern Rune

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;

class AttachmentController extends Controller
{
    public function show(Attachment $attachment)
    {
        return response()->file(storage_path('app/' . $attachment->file_path));
    }
}
