<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(){
        $customers = Customer::select('id', 'name', 'created_at')->orderBy('created_at', 'DESC')->paginate(10)->through(function ($redirect) {
            return [
                'id' => $redirect->id,
                'name' => $redirect->name,
                'created_at' => date('d/m/Y, H:i', strtotime($redirect->created_at)),
            ];
        });;
        return response()->json($customers, 200);
    }

    public function show($customer_id)
    {
        $customer = $this->customerService->getCustomer($customer_id);
        if (!$customer) {
            return response()->json(['error' => 'Selecione um cliente válido.'], 404);
        }
        return response()->json(['data' => $customer], 200);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'min:3']
        ]);

        try {
            $customer = $this->customerService->createCustomer($validate['name']);

            return response()->json([
                'message' => 'Cliente adicionado!',
                'data' => $customer
            ], 200);
        } catch (\Throwable $th) {
            Log::error(['description' => 'Criar novo cliente', 'message' => $th->getMessage()]);
            return response()->json(['error' => 'Houve um erro. Contate a T.I.'], 500);
        }
    }

    public function destroy(int $customer_id)
    {
        try {
            $customer = $this->customerService->deleteCustomer($customer_id);
            Log::info('Cliente excluído', ['Cliente' => $customer, 'Deletado por:' => Auth()->user()->name]);
            return response()->json(['message' => 'Cliente deletado com sucesso.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], $th->getCode());
        }
    }
}
