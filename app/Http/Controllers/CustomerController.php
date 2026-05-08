<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\LoyaltyCardRequest;
use App\Http\Requests\Customer\SearchRequest;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;
use Phobiavr\PhoberLaravelCommon\Pageable\PageableCollection;

class CustomerController extends BaseController {
    public function __construct(private readonly CustomerService $service) {
    }

    public function index(SearchRequest $request): JsonResponse {
        $list = $this->service->search($request, $request->trim());

        return Response::json(new PageableCollection($list, CustomerResource::class));
    }

    public function show(int $id): JsonResponse {
        return Response::json(CustomerResource::make($this->service->find($id)));
    }

    public function store(StoreRequest $request): JsonResponse {
        $customer = $this->service->create($request->customerData(), $request->contactModels());

        return Response::json(CustomerResource::make($customer));
    }

    public function update(UpdateRequest $request, int $id): JsonResponse {
        $customer = $this->service->update($id, $request->customerData(), $request->contactModels());

        return Response::json(CustomerResource::make($customer));
    }

    public function setLoyaltyCard(LoyaltyCardRequest $request, int $id): JsonResponse {
        $customer = $this->service->setLoyaltyCard($id, $request->code(), $request->status());

        return Response::json(CustomerResource::make($customer));
    }

    public function upcomingBirthdays(): JsonResponse {
        return Response::json(CustomerResource::collection($this->service->upcomingBirthdays()));
    }
}
