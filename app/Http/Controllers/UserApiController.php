<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
