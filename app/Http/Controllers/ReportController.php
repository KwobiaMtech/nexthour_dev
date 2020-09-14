<?php

namespace App\Http\Controllers;

use Charts;
use App\PaypalSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\Subscription;
use \Stripe\Stripe;

class ReportController extends Controller
{
    public function get_report()
    {
      // Set your secret key: remember to change this to your live secret key in production
      Stripe::setApiKey(env('STRIPE_SECRET'));
      $all_reports = Subscription::all();
      $paypal_subscriptions = PaypalSubscription::all();
      $sells=$paypal_subscriptions->sum('price');
      return view('admin.report.index', compact('all_reports', 'paypal_subscriptions','sells'));
    }

    public function get_revenue_report(){
    	
    	return view('admin.report.revenue');
    }

     public function ajaxonLoad(Request $request)
    {
        $stardate =date('Y-m-d',strtotime($request->startDate));
        $enddate = date('Y-m-d',strtotime($request->endDate));
        $date = date('Y-m-d');
        if($stardate == $date &&  $enddate == $date)
        {
             $revenue_report = PaypalSubscription::all();
             $rcchart = PaypalSubscription::orderBy('id','DESC')->get();
             $revenue_chart = Charts::database($rcchart, 'bar', 'highcharts')

                  ->title("Paypal subscription revenue")

                  ->elementLabel("Total paypal subscription revenue")

                  ->dimensions(1000, 500)

                  ->responsive(true)

                  ->groupByMonth(date('Y'), true);

        }
        else{

            $revenue_report = PaypalSubscription::whereBetween('subscription_from',[$stardate, $enddate])->get();
       		 $revenue_chart  = Charts::database($revenue_report, 'bar', 'highcharts')

                  ->title("Paypal subscription revenue")

                  ->elementLabel("Total paypal subscription revenue")

                  ->dimensions(1000, 500)

                  ->responsive(true)

                  ->groupByMonth(date('Y'), true);

        }
         $revenue_report = $revenue_report->flatten();
     
         if(!count($revenue_report)){

            $revenue_report = '<table id="full_detail_table" class="table table-hover">
                            <thead>
                             <tr>
                                <th> # </th>
            						        <th>User Name</th>
            						        <th>Payment Method</th>
            						        <th>Paid Amount</th>
            						        <th>Subscription From</th>
            						        <th>Subscription To</th>
            						        <th>Date</th>
                              </tr>
                            </thead>
                                     <th colspan="6"> <center> <b> No Result Found </b> <center> <th>
                                              
                            </table>';

             return $revenue_report;

         }
          
        return view('admin.report.data',compact('revenue_report','revenue_chart'));

    }
}
