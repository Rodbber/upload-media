<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class MediaFileController extends Controller
{
    public function index()
    {
        return MediaFile::get();
    }

    public function store(MediaFileRequest $request)
    {
        try {
            $files = $request->file('files');
            foreach ($files as $file) {
                $extension = $file->extension();
                $fileName = $file->getClientOriginalName();
                $uuidFilePathName = Uuid::uuid4() . ".$extension";
                $file->storeAs('public/media_files', $uuidFilePathName);
                MediaFile::create(['file_name' => $fileName, 'extension' => $extension, 'full_url' => Storage::url('public/media_files/' . "$uuidFilePathName")]);
            }
            return response()->json(['message' => 'Created success']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }

    public function fileRename(Request $request, $id){
        try {
            $request->validate(['file_name' => 'required|string']);
            MediaFile::find($id)->update(['file_name' => $request->file_name]);
            return response()->json(['message' => 'Updated success']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            MediaFile::find($id)->delete();
            return response()->json(['message' => 'Deleted success']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
