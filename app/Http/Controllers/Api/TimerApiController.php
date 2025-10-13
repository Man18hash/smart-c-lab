<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\JsonResponse;

class TimerApiController extends Controller
{
    /**
     * Get the current timer command for the most recently approved borrowing.
     * 
     * Returns the latest "checked_out" borrowing so the Python timer app can:
     * - Start automatically when a new borrowing is approved
     * - Display the correct duration based on approved time
     */
    public function current(): JsonResponse
    {
        // Get the most recently checked out (approved) borrowing
        $borrowing = Borrowing::query()
            ->where('status', 'checked_out')
            ->whereNotNull('approved_at')
            ->whereNotNull('due_at')
            ->with(['student', 'laptop', 'ipAsset'])
            ->orderByDesc('approved_at')
            ->first();

        // If no active borrowing found
        if (!$borrowing) {
            return response()->json([
                'command' => 'stop',
                'duration_seconds' => 0,
                'issued_at' => null,
                'message' => 'No active borrowing found'
            ]);
        }

        // Calculate remaining time in seconds
        $now = now();
        $dueAt = $borrowing->due_at;
        $approvedAt = $borrowing->approved_at;
        
        // Total duration from approval to due time (in seconds)
        $totalDurationSeconds = $approvedAt->diffInSeconds($dueAt, false);
        
        // Remaining duration from now to due time (in seconds)
        $remainingSeconds = $now->diffInSeconds($dueAt, false);
        
        // If time has expired, remaining is 0
        if ($remainingSeconds < 0) {
            $remainingSeconds = 0;
        }

        return response()->json([
            'command' => 'start',
            'duration_seconds' => max(0, $remainingSeconds), // Send remaining time
            'total_duration_seconds' => max(0, $totalDurationSeconds), // Total approved duration
            'issued_at' => $approvedAt->toIso8601String(), // Use approved_at as unique identifier
            'borrowing_id' => $borrowing->id,
            'student_name' => $borrowing->student->full_name ?? 'Unknown',
            'laptop_name' => $borrowing->laptop->device_name ?? 'Unknown',
            'ip_address' => $borrowing->ipAsset->name ?? null,
            'approved_at' => $approvedAt->toIso8601String(),
            'due_at' => $dueAt->toIso8601String(),
        ]);
    }
}

