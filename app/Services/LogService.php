<?php

namespace App\Services;

use App\Models\Log;

class LogService
{
    public function generateLog(string $destination_id, string $old_url){
        $user = Auth()->user();
        $log = new Log();
        $log->old_url = $old_url;
        $log->user_id = $user->id;
        $log->destination_id = $destination_id;
        $log->save();

        $log->user_name = $user->name;
        return $log;
    }
}
