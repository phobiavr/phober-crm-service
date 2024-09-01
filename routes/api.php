<?php

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Shared\Pageable\PageableCollection;
use Shared\Pageable\PageableRequest;

Route::middleware('auth.server')->group(function () {
    Route::get('', function () {
        return Auth::guard('server')->user();
    });

    Route::get('/customers', function (PageableRequest $request) {
        $query = Customer::query();

        if ($trim = $request->get('trim')) {
            $query
                ->orWhere('note', 'LIKE', "%{$trim}%")
                ->orWhere('first_name', 'LIKE', "%{$trim}%")
                ->orWhere('last_name', 'LIKE', "%{$trim}%")
                ->orWhere('note', 'LIKE', "%{$trim}%")
                ->orWhereHas('contacts', function ($query) use ($trim) {
                    $query->where('value', 'LIKE', "%{$trim}%");
                });
        }

        $list = $query->paginateFromRequest($request);

        return Response::json(new PageableCollection($list, CustomerResource::class));
    });

    Route::post('/customers', function (CustomerStoreRequest $request) {
        try {
            $customer = DB::transaction(function () use ($request) {
                $customer = Customer::create($request->validated());

                if (!empty($contacts = $request->get('contacts', []))) {
                    $contactModels = array_map(function ($contact) {
                        return new Contact($contact);
                    }, $contacts);

                    $customer->contacts()->saveMany($contactModels);
                }

                return $customer->load('contacts');
            });

            return Response::json(new CustomerResource($customer));
        } catch (Exception $e) {
            return Response::json([
                'error'   => 'Transaction failed',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    });

    Route::put('/customers/{id}', function (CustomerStoreRequest $request, int $id) {
        try {
            $customer = DB::transaction(function () use ($request, $id) {
                $customer = Customer::findOrFail($id);

                $customer->update($request->validated());

                $contactModels = array_map(function ($contact) {
                    return new Contact($contact);
                }, $request->get('contacts', []));

                $customer->contacts()->delete();
                $customer->contacts()->saveMany($contactModels);

                return $customer->load('contacts');
            });

            return Response::json(new CustomerResource($customer));
        } catch (Exception $e) {
            return Response::json([
                'error'   => 'Update failed',
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    });

    Route::get('/customers/upcoming-birthdays', function () {
        $customers = Customer::all()
            ->sortBy('days_until_birthday')
            ->values()
            ->take(5);

        return Response::json(CustomerResource::collection($customers));
    });
});


Route::middleware('private')->group(function () {
    Route::get('/customers/{id}', function (int $id) {
        return Response::json(CustomerResource::make(Customer::findOrFail($id)));
    });
});
