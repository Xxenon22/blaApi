<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Http\Resources\FolderResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\ResponsePrepared;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Folder::with('children', 'files')->whereNull('parent_id'); // Load subfolders dan files

        // Pencarian folder berdasarkan folder_name atau description
        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(folder_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        // Ambil folder yang sudah difilter
        $folders = $query->get();
        return response()->json($folders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $folder = Folder::create($request->only('folder_name', 'description'));
        return response()->json($folder, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $folder = Folder::with(['children', 'files'])->findOrFail($id);
        return new FolderResource($folder);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function getChildren(Request $request, $id)
    {
        $folder = Folder::with('children')->find($id);

        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        // Tangkap parameter search
        $search = $request->input('search');

        // Filter berdasarkan parameter search
        $children = $folder->children()->where(function ($query) use ($search) {
            if ($search) {
                $query->whereRaw('LOWER(folder_name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
            }
        })->get();

        return response()->json(['data' => $children], 200);
    }



    public function createSubFolder(Request $request, $id)
    {
        // Cari folder induk berdasarkan ID
        $parentFolder = Folder::with('children')->findOrFail($id);

        if (!$parentFolder) {
            return response()->json(['message' => 'Parent folder not found'], 404);
        }

        // Validasi input
        $request->validate([
            'folder_name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Buat sub-folder
        $childFolder = Folder::create([
            'folder_name' => $request->folder_name,
            'description' => $request->description,
            'parent_id' => $id, // Menyimpan ID folder induk
        ]);

        return response()->json(['data' => $childFolder, 'message' => 'Child folder created successfully'], 201);
    }

    // // Fungsi untuk mengambil folder yang ada di dalam folder tertentu
    // public function getNestedFolders($id)
    // {
    //     // Ambil folder berdasarkan ID
    //     $folder = Folder::with('subfolders')->findOrFail($id);

    //     // Return data subfolder yang ada di dalam folder
    //     return response()->json([
    //         'data' => $folder->subfolders
    //     ], 200);
    // }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Cari folder yang akan di-update
        $folder = Folder::findOrFail($id);


        $request->validate([
            'folder_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $folder->update([
            'folder_name' => $request->folder_name,
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Data Updated Successfully!',
            'data' => new FolderResource($folder)
        ], 200);

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();
        return response()->json(['message' => 'Folder deleted successfully']);
    }
}
