<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function update(Request $request, $id)
    {
        $design = Design::query()->findOrFail($id);

        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'.$id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
        ]);

        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ]);

//        return response()->json($design, Response::HTTP_OK);
        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = Design::query()->findOrFail($id);
        $this->authorize('delete', $design);

        //delete the files associated to the record
        foreach (['thumbnail', 'large', 'original'] as $size) {
            $path = "uploads/designs/{$size}/{$design->image}";
            if (Storage::disk($design->disk)->exists($path)) {
                Storage::disk($design->disk)->delete($path);
            }
        }

        $design->delete();

        return response()->json(['message' => 'Record deleted'], Response::HTTP_OK);
    }

}
