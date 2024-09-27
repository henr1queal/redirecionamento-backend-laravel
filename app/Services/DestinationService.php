<?php

namespace App\Services;

use App\Models\Destination;

class DestinationService
{
    public function updateDestination(string $destination_id, string $url){
        $destination = Destination::find($destination_id);

        if(!$destination) {
            return null;
        }

        if($destination->url === $url) {
            return false;
        }

        $old_url = $destination->url;
        $destination->url = $url;
        $destination->updated_at = now();
        $destination->save();

        return $old_url;
    }
}
