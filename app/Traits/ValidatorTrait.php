<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

trait ValidatorTrait
{
    public function validator(array $data, array $rules): JsonResponse|null
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ], 422);
        }
        return null;
    }
}
