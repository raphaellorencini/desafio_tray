<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use App\Traits\ValidatorTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Closure;

class SalesController extends Controller
{
    use ValidatorTrait;

    public function __construct(
        protected SaleRepository $sales,
        protected UserRepository $user,
    ) {}

    public function index(Request $request)
    {
        $sellerId = $request->get('seller_id');
        return $this->sales->paginate($sellerId);
    }

    public function comissionList(Request $request)
    {
        $sellerId = $request->get('seller_id');
        return $this->sales->comissionList($sellerId);
    }

    public function create(): JsonResponse
    {
        return response()->json([]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['role'] = null;
        try {
            $user = $this->user->getById($data['seller_id']);
            $data['role'] = $user->roles->toArray()[0]['name'];
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => '404 Not Found'
            ], 404);
        }

        $validatorRules = [
            'value' => [
                'required',
                'numeric',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($value <= 0) {
                        $fail("The {$attribute} greater than 0.");
                    }
                },
            ],
            'role' => [
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($value !== 'seller') {
                        $fail("The {$attribute} of user must be seller.");
                    }
                },
            ]
        ];
        $validator = $this->validator($data, $validatorRules);
        if($validator) {
            return $validator;
        }

        $sales = $this->sales->create([
            'value' => $data['value'],
        ]);
        $sales->users()->attach($user);

        return response()->json($sales);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([]);
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json([]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([]);
    }

    public function destroy(string $id): JsonResponse
    {
        return response()->json([]);
    }
}
