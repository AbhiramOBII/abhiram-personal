<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Services\PracticeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function __construct(protected PracticeService $service) {}

    public function complete(Request $request, Practice $practice): JsonResponse
    {
        $twoMinute = (bool) $request->input('two_minute', false);

        $log = $this->service->completePractice($practice, $twoMinute);

        return response()->json([
            'log' => $log->toArray(),
            'current_streak' => $practice->currentStreak(),
        ]);
    }

    public function uncomplete(Practice $practice): JsonResponse
    {
        $this->service->uncompletePractice($practice);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:practices,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->input('items') as $item) {
            Practice::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    public function updateNote(Request $request, PracticeLog $log): JsonResponse
    {
        $request->validate([
            'note' => 'nullable|string',
        ]);

        $log->update(['note' => $request->input('note')]);

        return response()->json([
            'success' => true,
            'note' => $log->note,
        ]);
    }
}
