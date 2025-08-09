<?php

// if (!function_exists('getCompanyData')) {
//     function getCompanyData()
//     {
//         return cache()->remember('company_info', 86400, function () {
//             return \App\Models\Company::first();
//         });
//     }
// }

if (!function_exists('refreshTokenCache')) {
    function refreshTokenCache()
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Delete all tokens with this name
            deleteUserToken();

            // Create new token
            $token = $user->createToken('api-token')->plainTextToken;

            // Cache it manually
            cache()->put('user_token_' . $user->id, $token, 60 * 60 * 24); // 24 hours

            return $token;
        }

        return null;
    }
}

if (!function_exists('getUserToken')) {
    function getUserToken()
    {
        if (auth()->check()) {
            return cache()->get('user_token_' . auth()->id());
        }

        return null;
    }
}

if (!function_exists('deleteUserToken')) {
    function deleteUserToken()
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Delete all tokens with this name
            $user->tokens()->where('name', 'api-token')->delete();

            // Remove token from cache
            cache()->forget('user_token_' . $user->id);
        }
    }
}
