<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Services\DestinationService;
use App\Services\LogService;
use App\Services\RedirectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DestinationController extends Controller
{
    public function goToRedirect(string $destination_id)
    {
        $destination = Destination::find($destination_id);
        if (!$destination) {
            return redirect('https://agencia.vision');
        }

        if ($destination->needs_count === 1) {
            $destination->count += 1;
            $destination->save();
        }
        return redirect($destination->url);
    }

    public function destroy(RedirectService $redirect_service, string $destination_id)
    {
        try {
            $destination = $redirect_service->deleteDestination($destination_id);
            Log::info('Destino excluído', ['ID' => $destination->id, 'Visualizações' => $destination->count, 'Deletado por:' => Auth()->user()->name]);
            return response()->json(['message' => 'Deletado.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 404);
        }
    }

    public function updateDestination(Request $request, string $destination_id, DestinationService $destination_service, LogService $log_service)
    {
        $request->validate([
            'url' => ['required', 'url']
        ]);

        try {
            DB::beginTransaction();

            $old_url = $destination_service->updateDestination($destination_id, $request->url);

            if ($old_url === null) {
                return response()->json(['error' => 'Destino não existente.'], 404);
            }

            if ($old_url === false) {
                return response()->json(['error' => 'URL deve ser diferente da atual.'], 400);
            }

            $log = $log_service->generateLog($destination_id, $old_url);

            DB::commit();

            return response()->json($log, 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th->getMessage());

            return response()->json(['error' => 'Houve um erro. Tente novamente.']);
        }
    }
}
