<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{

  public function index()
  {
    // Check if users are in cache
    $users = Cache::remember('users', 60, function () {
      // Cache the users for 60 minutes
      return User::paginate(3000);
    });

    return response()->json(['users' => $users]);
  }


  public function store(Request $request)
  {
    // Validate the request data
    $validator = Validator::make($request->all(), [
      'first_name' => 'required|string',
      'last_name' => 'required|string',
      'email' => 'required|email',
      'age' => 'required|integer',
    ]);

    // If validation fails, return error response
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Create the user
    $user = new User;
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->age = $request->age;
    $user->save();

    return response()->json(['message' => 'User created successfully', 'user' => $user]);
  }

  public function show($id)
  {
    // Retrieve user by id
    $user = User::find($id);

    // Check if user exists
    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    // Return the user as JSON
    return response()->json(['user' => $user]);
  }

  public function update(Request $request, $id)
  {
    // Find the user
    $user = User::find($id);

    // Check if user exists
    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
      'first_name' => 'sometimes|required|string',
      'last_name' => 'sometimes|required|string',
      'email' => 'sometimes|required|email',
      'age' => 'sometimes|required|integer',
    ]);

    // If validation fails, return error response
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update the user
    $user->update($request->all());

    return response()->json(['message' => 'User updated successfully', 'user' => $user]);
  }

  public function destroy($id)
  {
    // Find the user
    $user = User::find($id);

    // Check if user exists
    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    // Delete the user
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
  }
}
