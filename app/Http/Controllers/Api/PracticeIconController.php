<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PracticeIconController extends Controller
{
    public function upload(Request $request, Practice $practice): JsonResponse
    {
        $request->validate([
            'icon' => 'required|file|mimes:svg|max:100',
        ]);

        if ($practice->icon_path) {
            Storage::disk('public')->delete($practice->icon_path);
        }

        $path = $request->file('icon')->store('practice-icons', 'public');
        $practice->update(['icon_path' => $path]);

        return response()->json(['success' => true, 'icon_url' => Storage::url($path)]);
    }

    public function delete(Practice $practice): JsonResponse
    {
        if ($practice->icon_path) {
            Storage::disk('public')->delete($practice->icon_path);
            $practice->update(['icon_path' => null]);
        }

        return response()->json(['success' => true]);
    }
}
