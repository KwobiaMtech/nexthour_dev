<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Multiplescreen;
use Auth;

class MultipleScreenController extends Controller
{

    public function manageprofile(){
        $user = Auth::user();
        $screen = null;
        $screen = Multiplescreen::where('user_id',$user->id)->orderBy('created_at','Desc')->first();
        return response()->json(array('screen' =>$screen), 200);
    }
    public function changescreen(Request $request)
    {
        $user = Auth::user();
        $query = Multiplescreen::where('user_id',$user->id)->update(['activescreen' => $request->screen]);

        if($query){
            return response()->json(array('ok'), 200);
        }else {
            return response()->json(array('error'), 401);
        }
    }

    public function screenprofile(Request $request)
    {

        $user = Auth::user();
        $result = Multiplescreen::where('user_id',$user->id)->first();
        $screentype = $request->type;

        if($request->value != null || $request->value != '')
        {
            $result->{$screentype} = $request->value;
        }
        
        $result->save();

        return response()->json(1, 200);
    }
}
