<?php

namespace App\Http\Controllers;

use App\Models\Redirect;
use App\Services\RedirectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RedirectController extends Controller
{
    private $redirectService;

    public function __construct(RedirectService $redirectService)
    {
        $this->redirectService = $redirectService;
    }

    public function index(){
        $redirects = Redirect::with(['user:id,name', 'customer'])->paginate(10)->through(function ($redirect) {
            return [
                'id' => $redirect->id,
                'title' => $redirect->title,
                'created_by' => $redirect->user->name,
                'created_at' => date('d/m/Y, H:i', strtotime($redirect->created_at)),
                'customer' => $redirect->customer ? $redirect->customer->name : 'Deletado',
            ];
        });
        return response()->json($redirects, 200);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => ['required'],
            'customer_id' => [
            'required',
            'integer',
            Rule::exists('customers', 'id')->whereNull('deleted_at')
        ],
            'destinations' => ['required', 'array'],
            'destinations.*.url' => ['required', 'url'],
            'destinations.*.needs_count' => ['required', 'boolean']
        ]);

        $user_id = Auth()->user()->id;
        
        try {
            $redirect = $this->redirectService->createRedirect($validate['title'], $validate['customer_id'], $user_id);
            $destinations = $this->redirectService->createDestinations($validate['destinations'], $redirect->id);
            
            return response()->json([
                'message' => 'Cliente adicionado!',
                'data' => ['redirect' => $redirect, 'destinations' => $destinations],
            ], 200);
        } catch (\Throwable $th) {
            Log::error(['description' => 'Criar novo redirecionamento', 'message' => $th->getMessage()]);
            return response()->json(['error' => 'Houve um erro. Contate a T.I.'], 500);
        }
    }
    
    public function storeDestination(Request $request, int $redirect_id){
        $validate = $request->validate([
            'destinations' => ['required', 'array'],
            'destinations.*.url' => ['required', 'url'],
            'destinations.*.needs_count' => ['required', 'boolean']
        ]);

        if(!Redirect::select('id')->find($redirect_id)) {
            return response()->json([
                'error' => 'Selecione um redirecionamento existente.'
            ], 404);
        }

        $destinations = $this->redirectService->createDestinations($validate['destinations'], $redirect_id);

        return response()->json($destinations, 200);
    }

    public function show(int $redirect_id)
    {
        $redirect = $this->redirectService->getRedirect($redirect_id);
        if (!$redirect) {
            return response()->json(['error' => 'Selecione um redirecionamento válido.'], 404);
        }
        return response()->json($redirect   , 200);
    }

    public function destroy(int $redirect_id)
    {
        try {
            $redirect = $this->redirectService->deleteRedirect($redirect_id);
            Log::info('Redirect excluído', ['Redirect' => $redirect, 'Deletado por:' => Auth()->user()->name]);
            return response()->json(['message' => 'Redirect deletado com sucesso.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], $th->getCode());
        }
    }
}
