<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users =User::latest()->paginate(10);
        return response()->json(
            [
                'status' => true,
                'message' => 'Users List Retrieved Successfully',
                'data' => $users
            ]
        );
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['nullable', 'string', 'max:255'],
                'profile_picture' => ['nullable', 'file', 'max:255'],
                'status' => ['nullable', 'in:active,inactive'],
            ]);
            if(isset($validatedData['password'])){
                $validatedData['password'] = Hash::make($validatedData['password']);
            }
            $imageName = null;
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('profile_pictures'), $imageName);
                $validatedData['profile_picture'] = $imageName;
            }
            $user = User::create($validatedData);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'User Created Successfully',
                    'data' => $user
                ]
            );

        }catch(\Exception $e){
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User Creation Failed',
                    'error' => $e->getMessage()
                ]
            );
        }
    }
    public function show($id){
        $user = User::find($id);
        if ($user) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'User Retrieved Successfully',
                    'data' => $user
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User Not Found',
                ]
            );
        }
    }
    public function update(Request $request, $id){
        try{
            $user = User::find($id);
            if (!$user) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'User Not Found',
                    ]
                );
            }
            $validatedData = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
                'password' => ['nullable', 'string', 'min:8'],
                'role' => ['nullable', 'string', 'max:255'],
                'profile_picture' => ['nullable', 'file', 'max:255'],
                'status' => ['nullable', 'in:active,inactive'],
            ]);
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('profile_pictures'), $imageName);
                $validatedData['profile_picture'] = $imageName;
            }
            $user->update($validatedData);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'User Updated Successfully',
                    'data' => $user
                ]
            );

        }catch(\Exception $e){
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User Update Failed',
                    'error' => $e->getMessage()
                ]
            );
        }
    }
    public function destroy($id){
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'User Deleted Successfully'
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User Not Found'
                ]
            );
        }
    }
}
