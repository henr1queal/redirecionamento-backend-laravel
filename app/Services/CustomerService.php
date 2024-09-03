<?php

namespace App\Services;

use App\Models\Customer;
use Exception;

class CustomerService
{
    public function createCustomer(String $name)
    {
        return Customer::create(['name' => $name]);
    }

    public function getCustomer(int $customer_id){
        return Customer::find($customer_id);
    }

    public function deleteCustomer(int $customer_id)
    {
        $customer = Customer::find($customer_id);
        
        if (!$customer) {
            throw new Exception("Cliente não encontrado.", 404);
        }

        if (count($customer->redirects) > 0) {
            throw new Exception("Não é possível deletar um cliente que tem redirecionamentos.", 418);
        }

        $customer_name = $customer->name;
        $customer->delete();
        return $customer_name;
    }
}
