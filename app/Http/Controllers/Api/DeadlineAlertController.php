<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeadlineAlert;
use Illuminate\Http\JsonResponse;

class DeadlineAlertController extends Controller
{
    public function dismiss(DeadlineAlert $alert): JsonResponse
    {
        $alert->update(['is_dismissed' => true]);

        return response()->json(['success' => true]);
    }
}
