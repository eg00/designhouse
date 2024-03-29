<?php

declare(strict_types=1);

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $this->validate($request, [
            'image' => ['required', 'mimes:jpg,gif,png', 'max:2048']]);

        // get the image
        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $request->file('image');
        $image_path = $image->getPathname();

        //get the original file name and replace any spaces with _
        $filename = time().'_'.preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        // move the image to the temporary location
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        // create the database record for the design
        $design = $user->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk'),
        ]);

        // dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, Response::HTTP_OK);
    }
}
