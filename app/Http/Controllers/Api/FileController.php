<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Folder;
use App\Http\Resources\FileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $folderId)
    {

        $query = File::where('folder_id', $folderId);

        if ($request->has('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(product_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(contact_person) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(vendor) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(type_id) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(material_position) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(material_description) LIKE ?', ["%{$search}%"]);
            });
        }
        // Menggunakan pagination dengan resource
        $fileDatas = $query->paginate(10);
        return response()->json($fileDatas);


    }

    /**
     * Display the specified resource.
     */
    public function show($folderId, $fileId)
    {
        // $file = File::findOrFail($id);
        // return new FileResource($file);

        \Log::info("Fetching file with ID: $fileId from folder with ID: $folderId");

        // Cek apakah folder dan file ada
        $folder = Folder::find($folderId);
        $file = File::where('folder_id', $folderId)->find($fileId);

        if (!$folder || !$file) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json(['data' => $file]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $folderId)
    {
        // try {
        //     // Log incoming request
        //     \Log::info('Incoming Request:', $request->all());

        //     $validated = $request->validate([
        //         'type_id' => 'required|string|max:255',
        //         'product_name' => 'required|string|max:255',
        //         'contact_person' => 'required|string|max:255',
        //         'vendor' => 'required|string|max:255',
        //         'website' => 'nullable|url',
        //         'material_position' => 'required|string|max:255',
        //         'material_description' => 'nullable|string',
        //         'image' => 'nullable|image|max:2048',
        //         'folder_id' => 'nullable|exists:folders,id',
        //     ]);

        //     \Log::info('Validated Data:', $validated);

        //     $file = new File($validated);
        //     $file->folder_id = $request->input('folder_id', null);

        //     if ($request->hasFile('image')) {
        //         $imagePath = $request->file('image')->store('images', 'public');
        //         $file->image = $imagePath;
        //     }

        //     $file->save();

        //     \Log::info('File Saved Successfully:', $file->toArray());

        //     return response()->json([
        //         'message' => 'File created successfully',
        //         'file' => $file,
        //     ], 201);

        // } catch (\Exception $e) {
        //     \Log::error('Error storing file:', ['error' => $e->getMessage()]);
        //     return response()->json([
        //         'message' => 'Error creating file',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }

        \Log::info('Data permintaan masuk untuk menyimpan file:', $request->all());

        $validator = Validator::make($request->all(), [
            'type_id' => 'required|string|max:100',
            'product_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
            'material_position' => 'required|string|max:255',
            'material_description' => 'nullable',
            'website' => 'nullable',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'error' => $validator->errors(),
            ], 422);
        }

        $filedata = new File($request->only('type_id', 'product_name', 'contact_person', 'vendor', 'website', 'material_position', 'material_description'));
        $filedata->folder_id = $folderId;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $filedata->image = $path;
        }

        $filedata->save();

        return response()->json([
            'message' => 'File berhasil dibuat!',
            'data' => new FileResource($filedata),
        ], 201);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    // public function getFilesByFolder($folderId)
    // {
    //     $files = File::where('folder_id', $folderId)->get();
    //     return response()->json(['data' => $files], 200);
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $folderId, $fileId)
    {
        // $request->validate([
        //     'type_id' => 'required|string',
        //     'product_name' => 'required|string',
        //     'contact_person' => 'required|string',
        //     'vendor' => 'required|string',
        //     'website' => 'nullable|string',
        //     'material_position' => 'required|string',
        //     'material_description' => 'nullable|string',
        //     'image' => 'nullable|image'
        // ]);

        // $file = File::findOrFail($id);

        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('images');
        //     $file->image = $imagePath;
        // }

        // $file->update($request->only(
        //     'type_id',
        //     'product_name',
        //     'contact_person',
        //     'vendor',
        //     'website',
        //     'material_position',
        //     'material_description',
        //     'image'
        // ));

        // return response()->json($file);

        $file = File::where('folder_id', $folderId)->findOrFail($fileId);

        $validator = Validator::make($request->all(), [
            'type_id' => 'required|string|max:100',
            'product_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
            'material_position' => 'required|string|max:255',
            'material_description' => 'nullable',
            'website' => 'nullable',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('image')) {
            if ($file->image) {
                Storage::disk('public')->delete($file->image);
            }
            $path = $request->file('image')->store('images', 'public');
            $file->image = $path;
        }

        $file->update([
            'type_id' => $request->type_id,
            'product_name' => $request->product_name,
            'contact_person' => $request->contact_person,
            'material_position' => $request->material_position,
            'material_description' => $request->material_description,
            'vendor' => $request->vendor,
            'website' => $request->website
        ]);

        return response()->json([
            'message' => 'Data Updated Successfully!',
            'data' => new FileResource($file)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($folderId, $fileId)
    {
        // $file = File::findOrFail($id);
        // $file->delete();
        // return response()->json(['message' => 'File deleted successfully']);

        $file = File::where('folder_id', $folderId)->findOrFail($fileId);

        if ($file->image) {
            Storage::disk('public')->delete($file->image);
        }

        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully!',
            'data' => new FileResource($file),
        ], 200);
    }
}
