<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    protected $designs;

    public function __construct(DesignInterface $designs)
    {
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(), new IsLive(), new ForUser(1)
        ])->all();

        return DesignResource::collection($designs);
    }

    public function show($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id)
    {
        $design = $this->designs->find($id);
        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'.$id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
        ]);

        $design = $this->designs->update($id,[
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ]);

        $this->designs->applyTags($id,$request->tags);

//        return response()->json($design, Response::HTTP_OK);
        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->designs->find($id);
        $this->authorize('delete', $design);

        //delete the files associated to the record
        foreach (['thumbnail', 'large', 'original'] as $size) {
            $path = "uploads/designs/{$size}/{$design->image}";
            if (Storage::disk($design->disk)->exists($path)) {
                Storage::disk($design->disk)->delete($path);
            }
        }

        $this->designs->delete($id);

        return response()->json(['message' => 'Record deleted'], Response::HTTP_OK);
    }

}
