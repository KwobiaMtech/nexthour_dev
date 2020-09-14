<?php

namespace App\Http\Controllers;
use Charts;
use DB;
use App\CouponCode;
use App\Faq;
use App\Genre;
use App\Movie;
use App\Package;
use App\TvSeries;
use App\User;
use App\PaypalSubscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
    	$users_count = User::count();
    	$movies_count = Movie::count();
    	$tvseries_count = TvSeries::count();
    	$genres_count = Genre::count();	
    	$package_count = Package::where('status',1)->where('delete_status',1)->count();
    	$coupon_count = CouponCode::count();
    	$faq_count = Faq::count();
        $activeusers = PaypalSubscription::join('users','users.id','=','paypal_subscriptions.user_id')->where('paypal_subscriptions.status','=','1')->where('users.is_blocked','=',0)->where('users.status','=',1)->count();
        $totalrevnue = PaypalSubscription::sum('price');
        $users = User::where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y'))->get();
        $activesubsriber = PaypalSubscription::where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y'))->where('status','1')->get();
        $chart = Charts::database($users, 'bar', 'highcharts')

                  ->title("Monthly new Registered Users")

                  ->elementLabel("Total Users")

                  ->dimensions(1000, 500)

                  ->responsive(true)

                  ->groupByMonth(date('Y'), true);

        $chart2 = Charts::database($activesubsriber, 'line', 'highcharts')

                  ->title("Monthly Active Subscribers")

                  ->elementLabel("Total Active Plan Users")

                  ->dimensions(1000, 500)

                  ->responsive(true)

                  ->groupByMonth(date('Y'), true);

       

    	return view('admin.index', compact('genres_count','users_count', 'movies_count', 'tvseries_count', 'package_count', 'coupon_count', 'faq_count','activeusers','totalrevnue','chart','chart2'));
    }

    public function device_history(Request $request)
    {
       $device_history = \DB::table('sessions')->where('user_id','!=',NULL)->get();
        if($request->ajax()){
             return \Datatables::of($device_history)
              
              ->addIndexColumn()
             
             ->addColumn('username',function($row){
                 $username= \DB::table('users')->where('id',$row->user_id)->first()->name;
                   
                   return $username;
                    
                })
             
               ->addColumn('user_agent',function($row){
                 
                    return str_limit($row->user_agent,50);
                 
              })
                ->addColumn('last_activity',function($row){
                  
                    return date('Y-m-d h:i:sa',$row->last_activity);
                 
              })
            
             
              ->rawColumns(['username','user_agent','last_activity'])
              ->make(true);
        }

       return view('admin.device-history',compact('device_history'));
    }
}
