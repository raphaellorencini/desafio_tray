<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ComissionSendMail;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use App\Traits\ValidatorTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Mail;

class SalesController extends Controller
{
    use ValidatorTrait;

    public function __construct(
        protected SaleRepository $sales,
        protected UserRepository $user,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $data = [];
        $sellerId = $request->get('seller_id');
        $date = $request->get('date') ? Carbon::createFromFormat('Y-m-d H:i:s', $request->get('date')) : now();
        $paginate = (bool)$request->get('paginate', 1);
        $paginateLimit = $request->get('limit', 15);
        $sales = $this->sales->list(sellerId: $sellerId, date: $date, simplePaginate: $paginate, paginateLimit: $paginateLimit);
        $data['list'] = $sales;
        $data['commission'] = $this->sales->commission(sellerId: $sellerId, date: $date);

        return response()->json($data);
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
            'name' => 'required',
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
            'name' => $data['name'],
            'value' => $data['value'],
        ]);
        $sales->users()->attach($user);

        return response()->json($sales, 201);
    }

    public function commission(Request $request, $seller_id = null): JsonResponse
    {
        $date = $request->get('date') ? Carbon::createFromFormat('Y-m-d H:i:s', $request->get('date')) : now();
        $commission = $this->sales->commission(sellerId: $seller_id, date: $date);
        $user = null;
        if (filled($seller_id)) {
            try {
                $user = $this->user->getById($seller_id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'error' => true,
                    'message' => '404 Not Found'
                ], 404);
            }
        }
        $date = $date->format('d/m/Y');
        $data = [
            'subject' => 'Comissão - Data: '.$date,
            'name' => $user?->name,
            'email' => $user?->email,
            'commission' => number_format($commission, 2, ",", "."),
            'date' => $date,
        ];

        $to = $user?->email ?? env('MAIL_TO');
        Mail::to($to)->send(new ComissionSendMail($data));

        return response()->json(['commission' => $commission, 'mail' => true]);
    }
}
