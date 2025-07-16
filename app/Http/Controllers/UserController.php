<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            // Validate pagination parameters
            $validator = Validator::make([
                'limit' => $limit,
                'page' => $page
            ], [
                'limit' => 'integer|min:1|max:100',
                'page' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Call stored procedure
            $results = DB::select('CALL get_all_users(?, ?)', [$limit, $offset]);
            
            // Get total count from second result set
            $totalResult = DB::select('SELECT COUNT(*) as total_count FROM users');
            $total = $totalResult[0]->total_count;

            return response()->json([
                'success' => true,
                'data' => $results,
                'pagination' => [
                    'current_page' => (int)$page,
                    'per_page' => (int)$limit,
                    'total' => (int)$total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:150|unique:users,email',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Hash password
            $hashedPassword = Hash::make($request->password);

            // Call stored procedure
            $result = DB::select('CALL create_user(?, ?, ?, ?)', [
                $request->first_name,
                $request->last_name,
                $request->email,
                $hashedPassword
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $result[0]
            ], 201);

        } catch (Exception $e) {
            // Handle duplicate email error
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists',
                    'error' => 'The email address is already in use'
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ], 400);
            }

            // Call stored procedure
            $result = DB::select('CALL get_user_by_id(?)', [$id]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result[0]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ], 400);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:150|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Hash password if provided
            $hashedPassword = $request->password ? Hash::make($request->password) : null;

            // Call stored procedure
            $result = DB::select('CALL update_user(?, ?, ?, ?, ?)', [
                $id,
                $request->first_name,
                $request->last_name,
                $request->email,
                $hashedPassword
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $result[0]
            ]);

        } catch (Exception $e) {
            // Handle user not found error
            if (str_contains($e->getMessage(), 'User not found')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Handle duplicate email error
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists',
                    'error' => 'The email address is already in use'
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ], 400);
            }

            // Call stored procedure
            $result = DB::select('CALL delete_user(?)', [$id]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => ['deleted_rows' => $result[0]->deleted_rows]
            ]);

        } catch (Exception $e) {
            // Handle user not found error
            if (str_contains($e->getMessage(), 'User not found')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 