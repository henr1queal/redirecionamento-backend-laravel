<?php

namespace App\Http\Controllers;

use App\Models\Log;

class LogController extends Controller
{
    public function show($destination_id)
    {
        $logs = Log::select('id', 'old_url', 'created_at', 'user_id')
            ->where('destination_id', $destination_id)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->formatted_created_at = $log->created_at->format('d/m/Y H:i');
                return $log;
            });

        if (!$logs) {
            return null;
        }

        return $logs;
    }
}
