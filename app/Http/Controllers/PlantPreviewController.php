<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlantPreviewController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $imageData = $request->input('image');
        // Rimuove il prefisso base64
        $imageData = preg_replace('/^data:image\/png;base64,/', '', $imageData);
        $imageData = base64_decode($imageData);

        $path = "plants/{$id}/preview.png";
        Storage::disk('public')->put($path, $imageData);

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
