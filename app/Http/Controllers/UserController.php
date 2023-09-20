<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function registerStudent(Request $request){
        
        $rules = array(
            'name' => ['required', 'regex: /^[a-zA-Z0-9\s]*$/'],
            'email' => 'required | email | unique:users,email',
            'password' => ['required', 'min:6', 'confirmed'],
            'password_confirmation' => ['required'],
        );
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            $data = $validator->errors();
            $status = 402;
        }else{
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::beginTransaction();
            try{
                $students = DB::table('users');
                $students->insertOrIgnore($data);
                DB::commit();
                
            }catch (Exception $e){
                DB::rollBack();
                p($e->getMessage());
                $students = null;
            }
            if($students != null){
                    $request->session()->put('email', $request->email);
                    $data = ['result' => 'Successfully registered'];
                    $status = 200;
                }else{
                    $data = ['result' => 'Registration failed'];
                    $status = 500;
                    
                }
        }
        return response()->json($data, $status);
    }

    public function loginStudent(Request $request){
        $students = DB::table('users');
        $rules = array(
            'email' => 'required | email',
            'password' => 'required | min:6'
        );
        $validator = Validator::make($request->all(),  $rules);
        if($validator->fails()){
            $data = $validator->errors();
            $status = 402;
        }
        else{
            $student = $students->where('email', $request->email)->first();
            if(!$student || !Hash::check($request->password, $student->password)){
                $data = ['result' => 'Invalid user'];
                $status = 500;
            }else{
                $request->session()->put('email', $request->email);
                $data = ['result' => 'Successfully login'];
                $status = 200;
            }
        }
        return response()->json($data, $status);
    }

    public function logoutStudent(){
        if(session()->has('email')){
            session()->pull('email');
            $data = ['result' => 'Logout successfully'];
            $status = 200;
        }else{
            $data = ['result' => 'Logout failed'];
            $status = 500;
        }
        return response()->json($data, $status);
    }
}
