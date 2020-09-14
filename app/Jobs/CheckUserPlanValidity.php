<?php

namespace App\Jobs;

use App\User;
use Mail;
use App\Mail\SendReminderEmail;
use Illuminate\Console\Command;
use DateTime;
use App\PaypalSubscription;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckUserPlanValidity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $all = PaypalSubscription::all();
        foreach ($all as $key => $value) {
            /*get before*/
            $cur_date = date('Y-m-d');
            $plan_end_date = $value->subscription_to;
            $datetime1 = new DateTime($cur_date);
            $datetime2 = new DateTime($plan_end_date);
            $interval = $datetime1->diff($datetime2);
            $interval2 = $datetime2->diff($datetime1);
            $beforedays = $interval->format('%a');
            $afterdays = $interval2->format('%a');
            $url = url('account/purchaseplan');
            if($beforedays == 7 && $value->status == 1){
                /*fire a mail*/
                 $msg = 'Your subscription will expire in 3 days';
                 try{
                    Mail::to($value->user->email)->send(new SendReminderEmail($msg,$url));
                }catch(\Swift_TransportException $e){
                }
            }
            if($afterdays == 7 && $value->status == 0){
                /*fire a mail*/
                 $msg = 'Your subscription is expiring today';
                 try{
                    Mail::to($value->user->email)->send(new SendReminderEmail($msg,$url));
                }catch(\Swift_TransportException $e){
                }
            }
            if($beforedays == 0 && $value->status == 1){
                /*fire a mail*/
                 $msg = 'Your Plan is expiring today';
                 try{
                   Mail::to($value->user->email)->send(new SendReminderEmail($msg,$url)); 
                 }catch(\Swift_TransportException $e){
                }
            }
        }
    }
}
