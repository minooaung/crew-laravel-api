<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::select(['id', 'name', 'email', 'role', 'created_at'])
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('id', intval($search))
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->orderBy("id", "desc")
            ->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     */    
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        try {
            $user = $this->userService->create($request->validated());
            return new UserResource($user);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }       

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        try {
            $updatedUser = $this->userService->update($user, $request->validated());
            return new UserResource($updatedUser);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        try {
            $this->userService->delete($user);
            return response()->json(['message' => 'User deleted successfully']);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
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