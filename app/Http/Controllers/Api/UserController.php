<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use App\Http\Resources\UserResource;
use Auth;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::select(['id', 'name', 'email', 'role', 'created_at']) // Fetch only needed columns
            ->when(!empty($search), function ($query) use ($search) {  // Ensure search is not null
            return $query->where('id', intval($search))
                            ->orWhere('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
        })
        ->orderBy("id","desc")
        ->paginate(10); // Use pagination to avoid loading too many users at once

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     */    
    public function show($id)
    {
        $user = User::findOrFail($id); // Ensures Laravel handles the 404 exception automatically
    
        return new UserResource($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'EMPLOYEE';

        if ($data['role'] === 'ADMIN' && User::where('role', 'ADMIN')->count() >= 5) {
            return response()->json(['error' => 'Cannot create ADMIN user. System already has Maximum limit of 5 Admin Users.'], 403);
        }

        $user = User::create($data);
        return new UserResource($user);
    }       

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        \Log::info("Updating a user with ID: " . $id);

        // Retrieve user or return 404 if not found
        $user = User::findOrFail($id);

        // Check authorization (prevent unauthorized updates)
        $this->authorize('update', $user);

        // Validate the input data
        $data = $request->validated();

        // Handle password updates securely
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Attempt user update
        $user->update($data);

        \Log::info("User updated successfully: " . $user->name);

        return new UserResource($user);
        // return response()->json([
        //     'message' => 'User updated successfully',
        //     'user' => new UserResource($user),
        // ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        \Log::info("Attempting to delete user ID: " . $id);

        // Retrieve user or return 404 if not found
        $user = User::findOrFail($id);

        // Enforce RBAC policy-based authorization
        $this->authorize('delete', $user);

        // Check for potential foreign key constraints
        if ($user->organisationUsers()->count() > 0) {
            \Log::warning("User ID: {$id} has associated records and cannot be deleted.");
            return response()->json([
                'error' => 'User cannot be deleted due to existing relationships.'
            ], 409); // Use 409 Conflict when deletion is blocked by DB constraints
        }

        // Attempt user deletion
        $user->delete();

        \Log::info("User ID {$id} successfully deleted.");

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }    

    /**
     * Get selected users by their IDs
     */
    public function selected(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        
        if (empty($ids)) {
            return response()->json([]);
        }

        $users = User::whereIn('id', $ids)->get();
        return UserResource::collection($users);
    }
}
