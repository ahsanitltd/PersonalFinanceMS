<?php

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

if (! function_exists('isApiRequestValidator')) {
    function isApiRequestValidator($request)
    {
        try {
            $jsonCheck = $request->wantsJson();
            if (! $jsonCheck) {
                throw new Exception('Invalid Request');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

if (! function_exists('isApiRequest')) {

    function isApiRequest($request)
    {
        try {
            $jsonCheck = $request->wantsJson();
            if (! $jsonCheck) {
                throw new Exception('Invalid Request');
            }
        } catch (Exception $e) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], 401)
            );
        }
    }
}

if (!function_exists('validationData')) {
    function validationData($request)
    {
        try {
            // Assuming this helper already exists
            isApiRequestValidator($request);

            return $request->all();
        } catch (\Exception $e) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], 422)
            );
        }
    }
}

if (!function_exists('failedValidation')) {
    function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $validator->getMessageBag()->toArray(),
                    'errors' => $validator->errors(),
                ], 422)
            );
        }
    }
}
