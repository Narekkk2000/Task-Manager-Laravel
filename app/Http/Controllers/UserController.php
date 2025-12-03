<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Product;
use Exception;

class UserController extends Controller
{
    /**
    * Get the authenticated user's profile with their books.
    *
    * Returns the currently authenticated user's data along with all books
    * they are selling as a JSON response.
    *
    * @return JsonResponse JSON response containing user information and their books.
    */
    public function profile(): JsonResponse
    {

        try
        {
            $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'user' => $user,
        ]);
        } catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    
}
