<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\Redirect;
use Exception;
use Illuminate\Support\Str;

class RedirectService
{
    public function createRedirect(String $title, int $customer_id, int $user_id)
    {
        return Redirect::create(['title' => $title, 'customer_id' => $customer_id, 'user_id' => $user_id]);
    }

    public function getRedirect(int $redirect_id)
    {
        return Destination::where('redirect_id', $redirect_id)->get();
    }

    public function deleteRedirect(int $redirect_id)
    {
        $redirect = Redirect::find($redirect_id);

        if (!$redirect) {
            throw new Exception("Redirect não encontrado.", 404);
        }

        if (count($redirect->destinations) > 0) {
            throw new Exception("Delete os destinos deste redirect antes.", 418);
        }

        $redirect_title = $redirect->title;
        $redirect->delete();
        return $redirect_title;
    }

    public function createDestinations(array $destinations, $redirect_id)
    {
        foreach ($destinations as &$destination) {
            $destination['id'] = Str::uuid();
            $destination['redirect_id'] = $redirect_id;
            $destination['created_at'] = now();
        }

        Destination::insert($destinations);
        return $destinations;
    }

    public function deleteDestination(string $destination_id)
    {
        $destination = Destination::find($destination_id);

        if (!$destination) {
            throw new \Exception('Destino não encontrado.');
        }
        
        $old_destination = $destination;
        $destination->delete();

        return $old_destination;
    }
}
