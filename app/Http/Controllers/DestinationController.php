<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Services\RedirectService;
use Illuminate\Support\Facades\Log;

class DestinationController extends Controller
{
    private $redirectService;

    public function __construct(RedirectService $redirectService) {
        $this->redirectService = $redirectService;
    }
    
    public function goToRedirect(string $destination_id) {
        $destination = Destination::find($destination_id);
        if(!$destination) {
            return redirect('https://agencia.vision');
        }

        if($destination->needs_count === 1) {
            $destination->count += 1;
            $destination->save();
        }
        return redirect($destination->url);
    }
    
    public function destroy(string $destination_id){
        try {
            $destination = $this->redirectService->deleteDestination($destination_id);
            Log::info('Destino excluído', ['ID' => $destination->id, 'Visualizações' => $destination->count, 'Deletado por:' => Auth()->user()->name]);
            return response()->json(['message' => 'Deletado.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 404);
        }
    }
}
