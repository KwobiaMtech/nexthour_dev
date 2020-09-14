<?php

namespace App\Http\Middleware;

use App\Package;
use App\Config;
use Session;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Stripe\Customer;
use Stripe\Stripe;
use App\Menu;
use App\MenuSection;
use App\WatchHistory;
use App\HomeBlock;
use App\HomeTranslation;
use App\Actor;
use App\AudioLanguage;
use App\Director;
use App\PricingText;
use App\Genre;
use App\HomeSlider;
use App\LandingPage;
use App\MenuVideo;
use App\PaypalSubscription;
use App\Movie;
use App\User;
use App\Season;
use App\TvSeries;
use Illuminate\Http\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\FrontSliderUpdate;
use Illuminate\Pagination\LengthAwarePaginator;
use App;

class IsSubscription
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */

	public function handle($request, Closure $next)
	{   	  
       App::setLocale(Session::get('changed_language'));		
       Stripe::setApiKey(env('STRIPE_SECRET'));
		$current_date = Carbon::now()->toDateString();
		if (Auth::check()) {			
			$auth = Auth::user();
			$catlog= Config::findOrFail(1)->catlog;
			$withlogin= Config::findOrFail(1)->withlogin;          
			if ($auth->is_admin == 1) {
				return $next($request);                
			}
			elseif($catlog==0){
			  
				if ($auth->stripe_id != null) {
					$customer = Customer::retrieve($auth->stripe_id);
				}
				$paypal = $auth->paypal_subscriptions->sortBy('created_at'); 
				if (isset($customer)) {         
					$alldata = $auth->subscriptions;
					$data = $alldata->last();      
				} 
				if (isset($paypal) && $paypal != null && count($paypal)>0) {
					$last = $paypal->last();
				} 
				$stripedate = isset($data) ? $data->created_at : null;
				$paydate = isset($last) ? $last->created_at : null;
				if($stripedate > $paydate){
					if($auth->subscribed($data->name) && date($current_date) <= date($data->subscription_to)){
						if($data->ends_at == null || $request->is('resumesubscription/*')){
							return $next($request);  
						}
						else{
							return redirect('/')->with('deleted', 'Please resume your subscription!');
						}                       
					} else {
						return redirect('/')->with('deleted', 'Your subscription has been expired!');
					}
				}
				elseif($stripedate < $paydate){
					if (date($current_date) <= date($last->subscription_to)){
						if($last->status == 1) {
							return $next($request);    
						}
						else{
							return redirect('/')->with('deleted', 'Please resume your subscription!');
						}                    
					} else {
						$last->status = 0;
						$last->save();
						return redirect('/')->with('deleted', 'Your subscription has been expired!');
					}
				}
				else{
					return redirect('account/purchaseplan')->with('deleted', 'You have no subscription please subscribe');

				}
			}
			else{
			 $navmenh = $request->route()->parameter('menu');
			 if (isset($navmenh)) {
				 # code...
			   $home_blocks = HomeBlock::where('is_active', 1)->get();
		        $home_slides = HomeSlider::orderBy('position', 'asc')->get();
		        $subscribe = $menu = Menu::whereSlug($navmenh)->first();
		        $withlogin = Config::findOrFail(1)->withlogin;
		        //Slider get limit here and Front Slider order
		        $catlog = Config::findOrFail(1)->catlog;
		        $limit = FrontSliderUpdate::where('id', 1)->first();

		        $watchistory=WatchHistory::where('user_id',$auth->id)->get();
		      
		        
		        $menuh = Menu::all();
		        
		        $age = 0;
		        $config = Config::first();
		        if ($config->age_restriction == 1)
		        {
		            if (Auth::user())
		            {
		                # code...
		                $user_id = Auth::user()->id;
		                $user = User::findOrfail($user_id);
		                $age = $user->age;
		            }
		            else
		            {
		                $age = 100;
		            }
		        }
		        
		      

		        $menu_data = \DB::table('menu_videos')->where('menu_id',$menuh[0]['id'])->get();
		        $recent_data = \DB::table('menu_videos')->where('menu_id',$menuh[0]['id'])->orderBy('id','DESC')->get();
		        $g = Genre::query();
		        $genres = $g->select('id','name')->paginate(5);
		        $lang = AudioLanguage::query();
		        $audiolanguages = $lang->select('id','language')->paginate(5);
		        $section6 =  MenuSection::where('section_id','=',6)->where('menu_id','=',$menuh[0]['id'])->first();
		        $section =  MenuSection::where('section_id','=',2)->where('menu_id','=',$menuh[0]['id'])->first();
		        
		        if(isset($section) || isset($section6)){
		           if ($request->ajax()) {
		                $view = view('data',compact('genres','menu_data','section','menu','subscribed','audiolanguages','section6','menuh'))->render();
		                return response()->json(['html'=>$view]);
		                 
		            }
		        }

				 $menuh=Menu::all();
				 $auth=Auth::user();
				 $subscribed = null;
				   
			if (isset($auth)) {
			   if ($auth->is_admin == 1) {
				$subscribed = 1;
				  
			  }
			  else{
					if ($auth->stripe_id != null) {
		        $customer = Customer::retrieve($auth->stripe_id);
		      }
		      $paypal = $auth->paypal_subscriptions->sortBy('created_at'); 
		      $plans = Package::all();
		      if (isset($customer)) {         
		       //return $alldata = $user->asStripeCustomer()->subscriptions->data;
			       $alldata = $auth->subscriptions;
			       $data = $alldata->last();      
		      } 
		      if (isset($paypal) && $paypal != null && count($paypal)>0) {
		        $last = $paypal->last();
		      } 
		      $stripedate = isset($data) ? $data->created_at : null;
		      $paydate = isset($last) ? $last->created_at : null;
		      if($stripedate > $paydate){
		        if($auth->subscribed($data->name)){
		          $subscribed= 1;
		        }
		      }
		      elseif($stripedate < $paydate){
		        if (date($current_date) <= date($last->subscription_to)) {
		          $subscribed= 1;
		        }
		      } 
			  }
		  }
		     $home_blocks = HomeBlock::where('is_active', 1)->get();
		     $menu_data = \DB::table('menu_videos')->where('menu_id',$menuh[0]['id'])->get();
        	  $recent_data = \DB::table('menu_videos')->where('menu_id',$menuh[0]['id'])->orderBy('id','DESC')->get();
             $watchistory=WatchHistory::where('user_id',$auth->id)->get();
              $lang = AudioLanguage::query();
        	  $audiolanguages = $lang->select('id','language')->paginate(5);
                
                $menu = $menuh[0]; 
                
                return $next($request); 
    
   //      	if($config->prime_genre_slider == 1)
   //      	{
			// 	 return  Response(view('home', compact('home_blocks','watchistory','home_slides', 'recent_added_seasons',
			// 	'movies', 'tvserieses', 'a_languages', 'all_mix', 'sliderview', 'recent_added_movies',
			// 	'genres', 'featured_movies', 'featured_seasons', 'menuh','catlog','withlogin','subscribed','menu','menu_data','recent_data','audiolanguages')));
			// }
			// else
			// {
			// 	 return  Response(view('home2', compact('home_blocks','watchistory','home_slides', 'recent_added_seasons',
			// 	'movies', 'tvserieses', 'a_languages', 'all_mix', 'sliderview', 'recent_added_movies',
			// 	'genres', 'featured_movies', 'featured_seasons', 'menuh','catlog','withlogin','subscribed','menu','menu_data','recent_data','audiolanguages')));
			// }
			
	   }

		}
	   return $next($request);
		   
		}
		else
		{
			 $withlogin= Config::findOrFail(1)->withlogin;

			if ($withlogin==1) {
				 $navmenh = $request->route()->parameter('menu');
 			if (isset($navmenh)) {
	 # code...
 
 			 $home_slides = HomeSlider::orderBy('position', 'asc')->get();
		        $subscribe = $menu = Menu::whereSlug($navmenh)->first();
		        $withlogin = Config::findOrFail(1)->withlogin;
		        //Slider get limit here and Front Slider order
		        $catlog = Config::findOrFail(1)->catlog;
		        $limit = FrontSliderUpdate::where('id', 1)->first();

		        $watchistory=WatchHistory::where('user_id',$auth->id)->get();
		      
		        
		        $menuh = Menu::all();
		        
		        $age = 0;
		        $config = Config::first();
		        if ($config->age_restriction == 1)
		        {
		            if (Auth::user())
		            {
		                # code...
		                $user_id = Auth::user()->id;
		                $user = User::findOrfail($user_id);
		                $age = $user->age;
		            }
		            else
		            {
		                $age = 100;
		            }
		        }

		        $menu_data = \DB::table('menu_videos')->where('menu_id',$menu->id)->get();
		        $recent_data = \DB::table('menu_videos')->where('menu_id',$menu->id)->orderBy('id','DESC')->get();
		        $g = Genre::query();
		        $genres = $g->select('id','name')->paginate(5);
		        $lang = AudioLanguage::query();
		        $audiolanguages = $lang->select('id','language')->paginate(5);
		        $section6 =  MenuSection::where('section_id','=',6)->where('menu_id','=',$menu->id)->first();
		        $section =  MenuSection::where('section_id','=',2)->where('menu_id','=',$menu->id)->first();
		        
		        if(isset($section) || isset($section6)){
		           if ($request->ajax()) {
		                $view = view('data',compact('genres','menu_data','section','menu','subscribed','audiolanguages','section6'))->render();
		                return response()->json(['html'=>$view]);
		                 
		            }
		        }
		 $menuh=Menu::all();
		 $auth=Auth::user();
				 $subscribed = null;
						   
					if (isset($auth)) {
						  
					  $auth = Auth::user();
					   if ($auth->is_admin == 1) {
						$subscribed = 1;
						  
					  }
					  else{
							if ($auth->stripe_id != null) {
				        $customer = Customer::retrieve($auth->stripe_id);
				      }
				      $paypal = $auth->paypal_subscriptions->sortBy('created_at'); 
				      $plans = Package::all();
				      if (isset($customer)) {         
				       //return $alldata = $user->asStripeCustomer()->subscriptions->data;
					       $alldata = $auth->subscriptions;
					       $data = $alldata->last();      
				      } 
				      if (isset($paypal) && $paypal != null && count($paypal)>0) {
				        $last = $paypal->last();
				      } 
				      $stripedate = isset($data) ? $data->created_at : null;
				      $paydate = isset($last) ? $last->created_at : null;
				      if($stripedate > $paydate){
				        if($auth->subscribed($data->name)){
				          $subscribed= 1;
				        }
				      }
				      elseif($stripedate < $paydate){
				        if (date($current_date) <= date($last->subscription_to)) {
				          $subscribed= 1;
				        }
				      } 
					  }
				  }

				  	$watchistory=WatchHistory::where('user_id',$auth->id)->get();
				 //  	if($config->prime_genre_slider == 1)
		   //      	{
					   
					// 	 return  Response(view('home', compact('home_slides', 'watchistory', 'recent_added_seasons',
					// 	'movies', 'tvserieses', 'a_languages', 'all_mix', 'sliderview', 'recent_added_movies',
					// 	'genres', 'featured_movies', 'featured_seasons', 'menuh','catlog','withlogin','subscribed','menu')));
					// }
					// else{
					// 	 return  Response(view('home2', compact('home_slides', 'watchistory', 'recent_added_seasons',
					// 	'movies', 'tvserieses', 'a_languages', 'all_mix', 'sliderview', 'recent_added_movies',
					// 	'genres', 'featured_movies', 'featured_seasons', 'menuh','catlog','withlogin','subscribed','menu')));
					// }
					 return $next($request); 

			   }
			}else{
			   
			   return redirect('login')->with('updated', 'Please login first!');
			}
		  

		}
	}
}
