@extends('layouts.theme')
@section('title',__('staticwords.watchhistory'))
@section('main-wrapper')
<br>
@php
 $withlogin= App\Config::findOrFail(1)->withlogin;
           $auth=Auth::user();
             $subscribed = null;
           
          
            if (isset($auth)) {

              $current_date = date("d/m/y");
                  
              $auth = Illuminate\Support\Facades\Auth::user();
              if ($auth->is_admin == 1) {
                $subscribed = 1;
              } else if ($auth->stripe_id != null) {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                if(isset($invoices) && $invoices != null && count($invoices->data) > 0)
                
                {
                $user_plan_end_date = date("d/m/y", $invoice->lines->data[0]->period->end);
                $plans = App\Package::all();
                foreach ($plans as $key => $plan) {
                  if ($auth->subscriptions($plan->plan_id)) {
                   
                  if($current_date <= $user_plan_end_date)
                  {
                  
                      $subscribed = 1;
                  }
                      
                  }
                } 
                }
                
                
              } else if (isset($auth->paypal_subscriptions)) {  
                //Check Paypal Subscription of user
                $last_payment = $auth->paypal_subscriptions->last();
                if (isset($last_payment) && $last_payment->status == 1) {
                  //check last date to current date
                  $current_date = Illuminate\Support\Carbon::now();
                  if (date($current_date) <= date($last_payment->subscription_to)) {
                    $subscribed = 1;
                  }
                }
              }
            }
         
@endphp
  @if (isset($pusheditems) && count($pusheditems) > 0 )
          <div class="genre-prime-block view-all-block">
           
            
            <div class="container-fluid">
              <h5 class="section-heading">{{__('staticwords.watchhistory')}} </h5>
              <a href="{{url('account/watchhistory/delete')}}"><button class=" btn btn-danger">{{__('staticwords.clearall')}}</button></a>
              <div class="">
                @if(isset($pusheditems))

            

                  @foreach($pusheditems as $item)
                  
                   @if($auth && $subscribed==1)
                  
                    @php
                     if ($item->type == 'M') {
                      $wishlist_check = \Illuminate\Support\Facades\DB::table('wishlists')->where([
                                                                          ['user_id', '=', $auth->id],
                                                                          ['movie_id', '=', $item->id],
                                                                         ])->first();
                     }

                    if ($item->type == 'S') {
                       $wishlist_check = \Illuminate\Support\Facades\DB::table('wishlists')->where([
                                                                        ['user_id', '=', $auth->id],
                                                                        ['season_id', '=', $item->id],
                                                                      ])->first();
                    }
                    @endphp
                      @endif
                    
                  
                  @if($item->type == "M")
                   @if($item->status == 1)
                  <div class="col-lg-2 col-md-3 col-xs-6 col-sm-4">
                        <div class="cus_img">
                          
                        
                      <div class="genre-slide-image protip" data-pt-placement="outside" data-pt-interactive="false" data-pt-title="#prime-next-item-description-block{{$item->id}}">
                        <a href="{{url('movie/detail',$item->id)}}">
                          @if($item->thumbnail != null || $item->thumbnail != '')
                            <img src="{{url('images/movies/thumbnails/'.$item->thumbnail)}}" class="img-responsive" alt="genre-image">
                          @else

                            <img src="{{url('images/default-thumbnail.jpg')}}" class="img-responsive" alt="genre-image">
                          @endif
                        </a>
                      </div>
                       {!! Form::open(['method' => 'DELETE', 'action' => ['WatchController@moviedestroy', $item->id]]) !!}
                    {!! Form::submit(__('staticwords.remove'), ["class" => "btn btn-danger"]) !!}
                {!! Form::close() !!}
                      <div id="prime-next-item-description-block{{$item->id}}" class="prime-description-block">
                        <div class="prime-description-under-block">
                          <h5 class="description-heading">{{$item->title}}</h5>
                          <div class="item-rating">{{__('staticwords.rating')}} {{$item->rating}}</div>
                          <ul class="description-list">
                            <li>{{$item->duration}} {{__('staticwords.mins')}}</li>
                            <li>{{$item->publish_year}}</li>
                            <li>{{$item->maturity_rating}}</li>
                            @if($item->subtitle == 1)
                              <li>
                               {{__('staticwords.subtitles')}}
                              </li>
                            @endif
                          </ul>
                          <div class="main-des">
                            <p>{{$item->detail}}</p>
                            <a href="{{url('movie/detail',$item->id)}}">{{__('staticwords.readmore')}}</a>
                          </div>
                          @if($subscribed==1 && $auth)
                          <div class="des-btn-block">
                            @if($item->maturity_rating == 'all age' || $age>=str_replace('+', '', $item->maturity_rating))
                            @if($item->video_link['iframeurl'] != null)
                          
                            <a onclick="playoniframe('{{ $item->video_link['iframeurl'] }}','{{ $item->id }}','movie')" class="btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                            </a>

                            @else 
                              <a href="{{route('watchmovie',$item->id)}}" class="iframe btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span></a>
                            @endif
                            @else
                              <a onclick="myage({{$age}})" class="btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                              </a>
                            @endif
                           
                            @if($item->trailer_url != null || $item->trailer_url != '')
                
                            <a class="iframe btn btn-default" href="{{ route('watchTrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>

                            @endif
                            @if (isset($wishlist_check->added))
                              <a onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{$wishlist_check->added == 1 ? __('staticwords.removefromwatchlist') : __('staticwords.addtowatchlist')}}</a>
                            @else
                              <a onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{__('staticwords.addtowatchlist')}}</a>
                            @endif
                          </div>
                          @else
                          @if($item->trailer_url != null || $item->trailer_url != '')
                          <div class="des-btn-block"> 
                            <a class="iframe btn btn-default" href="{{ route('guestwatchtrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                          </div>
                           

                            @endif
                          @endif
                        </div>
                      </div>
                      </div>
                       
                    </div>
                    @endif
                    @elseif($item->type == "S")
                    @if($item->tvseries->status == 1)
                    <div class="col-lg-2 col-md-3 col-xs-6 col-sm-4">
                      <div class="cus_img">
                        
                      
                  <div class="genre-slide-image protip" data-pt-placement="outside" data-pt-interactive="false" data-pt-title="#prime-next-item-description-block{{$item->id}}{{ $item->type }}">
                      <a href="{{url('show/detail',$item->id)}}">
                        @if($item->tvseries->thumbnail != null || $item->tvseries->thumbnail != '')
                          <img src="{{url('images/tvseries/thumbnails/'.$item->tvseries->thumbnail)}}" class="img-responsive" alt="genre-image">
                        @else

                          <img src="{{url('images/default-thumbnail.jpg')}}" class="img-responsive" alt="genre-image">
                        @endif
                      </a>

                    </div>
                    {!! Form::open(['method' => 'DELETE', 'action' => ['WatchController@showdestroy', $item->tvseries->id]]) !!}
                        {!! Form::submit(__('staticwords.remove'), ["class" => "btn btn-danger"]) !!}
                    {!! Form::close() !!}
                    
                    <div id="prime-next-item-description-block{{$item->id}}{{$item->type}}" class="prime-description-block">
                        <h5 class="description-heading">{{$item->tvseries->title}}</h5>
                        <div class="movie-rating">{{__('staticwords.tmdbrating')}} {{$item->tvseries->rating}}</div>
                        <ul class="description-list">
                          <li>{{__('staticwords.season')}}{{$item->season_no}}</li>
                          <li>{{$item->publish_year}}</li>
                          <li>{{$item->tvseries->age_req}}</li>
                          @if($item->subtitle == 1)
                            <li>
                              {{__('staticwords.subtitles')}}
                            </li>
                          @endif
                        </ul>
                        <div class="main-des">
                          @if ($item->detail != null || $item->detail != '')
                            <p>{{$item->detail}}</p>
                          @else
                            <p>{{$item->tvseries->detail}}</p>
                          @endif
                          <a href="#"></a>
                        </div>
                        @if($subscribed==1 && $auth)
                        <div class="des-btn-block">
                          @if (isset($item->episodes[0]))
                             @if($item->tvseries['age_req'] == 'all age' || $age>=str_replace('+', '', $item->tvseries['age_req']))
                            @if($item->episodes[0]->video_link['iframeurl'] !="")

                            <a href="#" onclick="playoniframe('{{ $item->episodes[0]->video_link['iframeurl'] }}','{{ $item->tvseries->id }}','tv')" class="btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                             </a>

                            @else
                            <a href="{{ route('watchTvShow',$item->id) }}" class="iframe btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span></a>
                            @endif
                            @else
                               <a onclick="myage({{$age}})" class="btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                             </a>
                            @endif
                           
                          @endif
                          @if (isset($wishlist_check->added))
                            <a onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{$wishlist_check->added == 1 ? __('staticwords.removefromwatchlist') : __('staticwords.addtowatchlist')}}</a>
                          @else
                            <a onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{__('staticwords.addtowatchlist')}}
                            </a>
                          @endif
                        </div>
                        
                        @endif
                      </div>
                    </div>
                   
                     
                  </div>
                    @endif
                    @endif
                  @endforeach

                @endif
                
              </div>
             <div class="col-md-12">
                <div align="center">
                   {!! $pusheditems->links() !!}
                </div>
             </div>


            </div>
           
          </div>
          
        @endif
@endsection
@section('custom-script')


<script>

  function playoniframe(url,id,type){
      
 
   $(document).ready(function(){
    var SITEURL = '{{URL::to('')}}';
       $.ajax({
            type: "get",
            url: SITEURL + "/user/watchhistory/"+id+'/'+type,
            success: function (data) {
             console.log(data);
            },
            error: function (data) {
               console.log(data)
            }
        });
       
   
         
  
  });       
    $.colorbox({ href: url, width: '100%', height: '100%', iframe: true });
  }
  
</script>
 <script>

   

    var app = new Vue({
      el: '.des-btn-block',
      data: {
        result: {
          id: '',
          type: '',
        },
      },
      methods: {
        addToWishList(id, type) {
          this.result.id = id;
          this.result.type = type;
          this.$http.post('{{route('addtowishlist')}}', this.result).then((response) => {
          }).catch((e) => {
            console.log(e);
          });
          this.result.item_id = '';
          this.result.item_type = '';
        }
      }
    });

</script>

 <script>
     function addWish(id, type) {
      app.addToWishList(id, type);
      setTimeout(function() {
        $('.addwishlistbtn'+id+type).text(function(i, text){
          return text == "{{__('staticwords.addtowatchlist')}}" ? "{{__('staticwords.removefromwatchlist')}}" : "{{__('staticwords.addtowatchlist')}}";
        });
      }, 100);
    }

  </script>
  <script>

      function myage(age){
        if (age==0) {
        $('#ageModal').modal('show'); 
      }else{
          $('#ageWarningModal').modal('show');
      }
    }
      
    </script>
@endsection