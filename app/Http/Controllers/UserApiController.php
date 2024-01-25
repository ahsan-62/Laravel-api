<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    public function showUsers($id=null)
    {
        if($id==null){
            $users=User::get();
            return response()->json(['users'=>$users],200);
        }else{
            $users=User::find($id);
           return response()->json(['users'=>$users],200);
        }
    }


    public function createUser(Request $request) {


        if($request->isMethod('post')){

            $data=$request->all();

            $rules=[

                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required',
            ];

            $customMessage=[
                'name.required'=>'Name is required',
                'email.required'=>'Email is required',
                'email.email'=>'Email must be valid',
                'email.unique'=>'Email already exists',
                'password.required'=>'Password is required',
            ];

            $validator= Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json(['error'=>$validator->errors()],422);
            }


            $user= new User();
            $user->name=$data['name'];
            $user->email=$data['email'];
            $user->password=$data['password'];
            $user->save();

            $message='User created successfully';
            return response()->json(['message'=>$message],200);

        }

    }

    public function createMultiUser(Request $request) {


        if($request->isMethod('post')){

            $data=$request->all();

            $rules=[
                'users.*.name'=>'required',
                'users.*.email'=>'required|email|unique:users',
                'users.*.password'=>'required',
            ];

            $customMessage=[
              'users.*.name.required'=>'Name is required',
              'users.*.email.required'=>'Email is required',
              'users.*.email.email'=>'Email must be valid',
              'users.*.email.unique'=>'Email already exists',
              'users.*.password.required'=>'Password is required',

            ];

            $validator=Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json(['error'=>$validator->errors()],422);
            }


            foreach ($data['users'] as $key => $value) {
                $user=new User();
                $user->name=$value['name'];
                $user->email=$value['email'];
                $user->password=$value['password'];
                $user->save();
                $message='User created successfully';

            }
            return response()->json(['message'=>$message],200);

        }
     }

     public function updateUser($id,Request $request) {
       if($request->isMethod('put')){
           $data=$request->all();

           $rules=[
               'name'=>'required',
               'password'=>'required',
           ];

           $customMessage=[
               'name.required'=>'Name is required',
               'password.required'=>'Password is required',
           ];

           $validator=Validator::make($data,$rules,$customMessage);
           if($validator->fails()){
               return response()->json(['error'=>$validator->errors()],422);
           }

           $user=User::find($id);

          if(empty($user)){
              $message='User not found';
              return response()->json(['message'=>$message],404);
          }

           else if($id==$user->id){
           $user->name=$data['name'];
           $user->password=$data['password'];
           $user->save();
           $message='User updated successfully';
           return response()->json(['message'=>$message],200);
           }
       }
     }
}
