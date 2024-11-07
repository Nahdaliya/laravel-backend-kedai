<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;

use Illuminate\Http\Request;
use app\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
    //get users with pagination
    $users = DB::table('users')
    ->when($request->input('name'), function ($query, $name) {
        return $query->where('name', 'like', '%' . $name . '%');
    })
    ->orderBy('created_at', 'desc')
    ->paginate(5);
    return view('pages.auth.user.index', compact('users'));

}
  //create
  public function create()
{
    return view('pages.auth.user.create'); // Render the create form
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'phone' => 'required',
        'roles' => 'required',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'roles' => $request->roles,
    ]);

    return redirect()->route('user.index')->with('success', 'User created successfully');
}

public function edit($id)
{
    $user = User::findOrFail($id);
    return view('pages.auth.user.edit', compact('user')); // Render the edit form
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone' => 'required',
        'roles' => 'required',
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'roles' => $request->roles,
        'password' => $request->password ? Hash::make($request->password) : $user->password,
    ]);

    return redirect()->route('user.index')->with('success', 'User updated successfully');
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('user.index')->with('success', 'User deleted successfully');
}
}
