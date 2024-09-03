<?php

namespace App\Http\Controllers;

use App\Services\RedirectService;
use Illuminate\Support\Facades\Log;

class DestinationController extends Controller
{
    private $redirectService;

    public function __construct(RedirectService $redirectService) {
        $this->redirectService = $redirectService;
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
