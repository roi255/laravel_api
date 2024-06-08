<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CustomerCollection;
use App\Models\Customer;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerResource;
use App\Filters\V1\CustomersFilter;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new CustomersFilter();
        $filterItems = $filter->transform($request);

        $includeInvoices = $request->query('includeInvoices');

        $customers = Customer::where($filterItems);


        if ($includeInvoices) {

            $customers = Customer::with('invoices');
        }

        return new CustomerCollection($customers->paginate()->appends($request->query()));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {

        return new CustomerResource(Customer::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer, Request $request)
    {

        $includeInvoices = $request->query('includeInvoices');
        if ($includeInvoices) {

            $customer->loadMissing('invoices');
        }
        return new CustomerResource($customer);
        // return $customer;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
