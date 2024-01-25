<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function deleteUser($id=0){
        User::findOrFail($id)->delete();
        $message='User deleted successfully';
        return response()->json(['message'=>$message],200);
    }


    public function deleteUserJson(Request $request){
        $data=$request->all();
        User::findOrFail($data['id'])->delete();
        $message='User deleted successfully';
        return response()->json(['message'=>$message],200);
    }


public function deleteMultiUsers(Request $request,$ids){


    $header= $request->header('Authorization');

    if($header==''){
        $message='Authorization header is required';
        return response()->json(['message'=>$message],401);
    }
    else{
        if($header=='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6ImFoc2FuIiwiaWF0IjoxNTE2MjM5MDIyfQ.MpUc6kUXVJpYXa26KZNqUkIKQGNJqoFqcf2o4yr_wVs'){

            User::whereIn('id',explode(",",$ids))->delete();
            $message='User deleted successfully';
            return response()->json(['message'=>$message],200);

        }
        else{
            $message='Authorization does not match';
            return response()->json(['message'=>$message],401);
        }
    }


}

    public function registerApiUser(Request $request){

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
            $user->password=bcrypt($data['password']);
            $user->save();

            if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){

                $user=User::where('email',$data['email'])->first();
                $access_token=$user->createtoken($data['email'])->accessToken;
                User::where('email',$data['email'])->update(['access_token'=>$access_token]);

                $message='User created successfully';
                return response()->json(['message'=>$message,'access_token'=>$access_token],200);

            }
            else{
                $message='Opps! Something went wrong';
                return response()->json(['message'=>$message],422);
            }


        }
    }



    public function loginApiUser(Request $request){

        if($request->isMethod('post')){
            $data=$request->all();

            $rules=[
                'email'=>'required|email|exists:users,email',
                'password'=>'required',
            ];

            $customMessage=[
                'email.required'=>'Email is required',
                'email.email'=>'Email must be valid',
                'email.exists'=>'Email does not exists',
                'password.required'=>'Password is required',
            ];

            $validator= Validator::make($data,$rules,$customMessage);
            if($validator->fails()){
                return response()->json(['error'=>$validator->errors()],422);
            }


            if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){

                $user=User::where('email',$data['email'])->first();
                $access_token=$user->createtoken($data['email'])->accessToken;
                User::where('email',$data['email'])->update(['access_token'=>$access_token]);
                $message='User logged in successfully';
                return response()->json(['message'=>$message,'access_token'=>$access_token],200);
            }

            else {
                $message='Opps! Something went wrong';
                return response()->json(['message'=>$message],422);
            }
         }
     }



}
