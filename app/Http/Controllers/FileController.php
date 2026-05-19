<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileShare;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function download($id)
    {
        $file = FileShare::findOrFail($id);
        
        // Ensure user is authorized to download
        if (Auth::user()->role !== 'admin' && Auth::id() !== $file->user_id) {
            abort(403, 'Unauthorized access to this file.');
        }

        $path = Storage::disk('local')->path($file->file_path);
        
        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found on the server.');
        }

        return response()->download($path, $file->file_name);
    }

    public function storeClientFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // max 10MB
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('files', $filename, 'local');

        FileShare::create([
            'user_id' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'uploaded_by' => 'client'
        ]);

        return redirect()->back()->with('success', 'File uploaded securely to Priyam Finserv.');
    }

    public function deleteClientFile($id)
    {
        $file = FileShare::findOrFail($id);

        // Only allow deletion of files the client themselves uploaded
        if (Auth::id() !== $file->user_id) {
            abort(403, 'You cannot delete this file.');
        }

        if ($file->uploaded_by !== 'client') {
            return redirect()->back()->with('error', 'You cannot delete files shared by your advisor.');
        }

        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        return redirect()->back()->with('success', 'File removed successfully.');
    }
}
