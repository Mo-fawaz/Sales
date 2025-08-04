<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{

    public function verifyAndStoreImage(Request $request, $inputname, $foldername, $disk, $imageable_id, $imageable_type)
    {
        if ($request->hasFile($inputname)) {
            // Check img
            if (!$request->file($inputname)->isValid()) {
                flash('Invalid Image!')->error()->important();
                return redirect()->back()->withInput();
            }

            $photo = $request->file($inputname);
            $name = \Str::slug($request->input('name'));
            //  $filename = $name . '.' . $photo->getClientOriginalExtension();
            $filename =  $name . '-' . time() . '.' . $request->file($inputname)->extension();
            // insert Image
            $Image = new Image();
            $Image->filename = $filename;
            $Image->imageable_id = $imageable_id;
            $Image->imageable_type = $imageable_type;
            $Image->save();
            return $request->file($inputname)->storeAs($foldername, $filename, $disk);
        }

        return null;
    }

    public function verifyAndStoreImageForeach($imageFile, $foldername, $disk, $imageable_id, $imageable_type)
    {
        if (!$imageFile->isValid()) {
            \Log::error('Invalid image file uploaded.');
            return null;
        }

        $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = \Str::slug($originalName);
        $filename = $slug . '-' . time() . '.' . $imageFile->extension();

        $imageFile->storeAs($foldername, $filename, $disk);

        $image = new \App\Models\Image();
        $image->filename = $filename;
        $image->imageable_id = $imageable_id;
        $image->imageable_type = $imageable_type;
        $image->save();

        return $filename;
    }





    public function Delete_attachment($disk, $path, $id)
    {
        Storage::disk($disk)->delete($path);
        image::where('imageable_id', $id)->delete();
    }
}
