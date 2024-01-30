<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use League\Flysystem\Util;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Design $design;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $filename = $this->design->image;
        $original_file = Util::normalizePath(storage_path().'/uploads/original/'.$filename);

        try {
            //create the Large Image and save to tmp disk
            Image::make($original_file)
                ->fit(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($large = Util::normalizePath(storage_path('/uploads/large/'.$filename)));

            // create the thumbnail image
            Image::make($original_file)
                ->fit(250, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($thumbnail = Util::normalizePath(storage_path('/uploads/thumbnail/'.$filename)));

            //store images to permanent disk
            //original image
            if (Storage::disk($disk)
                ->put(Util::normalizePath('/uploads/designs/original/'.$filename), fopen($original_file, 'rb+'))) {
                File::delete($original_file);
            }
            //large image
            if (Storage::disk($disk)
                ->put(Util::normalizePath('/uploads/designs/large/'.$filename), fopen($large, 'rb+'))) {
                File::delete($large);
            }
            //thumbnail image
            if (Storage::disk($disk)
                ->put(Util::normalizePath('/uploads/designs/thumbnail/'.$filename), fopen($thumbnail, 'rb+'))) {
                File::delete($thumbnail);
            }

            // Update the database record with success flag
            $this->design->update(['upload_successful' => true]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
