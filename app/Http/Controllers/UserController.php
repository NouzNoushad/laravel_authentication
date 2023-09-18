<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function registerStudent(Request $request){
        $students = DB::table('users');
        $rules = array(
            'name' => ['required', 'regex: /^[a-zA-Z0-9\s]*$/'],
            'email' => 'required | email | unique:users,email',
            'password' => 'required | min:6'
        );
        $validator = Validator::make($request->all(),  $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }else{
            $students->insertOrIgnore([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if($students){
                $request->session()->put('email', $request->email);
                return response()->json(['result' => 'Successfully registered'], 200);
            }else{
                return response()->json(['result' => 'Registration failed'], 500);
            }
        }
    }

    public function loginStudent(Request $request){
        $students = DB::table('users');
        $rules = array(
            'email' => 'required | email',
            'password' => 'required | min:6'
        );
        $validator = Validator::make($request->all(),  $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 402);
        }
        else{
            $student = $students->where('email', $request->email)->first();
            if(!$student || !Hash::check($request->password, $student->password)){
                return response()->json(['result' => 'Invalid user'], 500);
            }else{
                $request->session()->put('email', $request->email);
                return response()->json(['result' => 'Successfully Login'], 200);
            }
        }
    }

    public function logoutStudent(){
        if(session()->has('email')){
            session()->pull('email');
            return response()->json(['result' => 'Logout successfully'], 200);
        }else{
            return response()->json(['result' => 'Logout failed'], 500);
        }
    }
}
