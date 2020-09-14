
@foreach($audiolanguages as $lang)
                         @php
                            $audiogenreitems = NULL;
                            $audiogenreitems = array();

                            foreach ($menu_data as $key => $item) {
                               
                                $gmovie =  App\Movie::join('videolinks','videolinks.movie_id','=','movies.id')
                                         ->select('movies.id as id','movies.title as title','movies.type as type','movies.status as status','movies.genre_id as genre_id','movies.thumbnail as thumbnail','movies.rating as rating','movies.duration as duration','movies.publish_year as publish_year','movies.maturity_rating as maturity_rating','movies.detail as detail','movies.trailer_url as trailer_url','videolinks.iframeurl as iframeurl')
                                         ->where('movies.a_language', 'LIKE', '%' . $lang->id . '%')->where('movies.id',$item->movie_id)->first();

                               
                                if(isset($gmovie)){
                                  
                                   $audiogenreitems[] = $gmovie;
                                          
                                }

                                 if($section->order == 1){
                                    arsort($audiogenreitems);
                                  }

                                if(count($audiogenreitems) == $section->item_limit){
                                    break;
                                    exit(1);
                                }


                            }

                            $audiogenreitems = array_values(array_filter($audiogenreitems));

                           

                              foreach ($menu_data as $key => $item) {

                                   $gtvs = App\Tvseries::
                                                join('seasons','seasons.tv_series_id','=','tv_series.id')
                                                ->join('episodes','episodes.seasons_id','=','seasons.id')
                                                ->join('videolinks','videolinks.episode_id','=','episodes.id')
                                                ->select('seasons.id as seasonid','tv_series.genre_id as genre_id','tv_series.id as id','tv_series.type as type','tv_series.status as status','tv_series.thumbnail as thumbnail','tv_series.title as title','tv_series.rating as rating','seasons.publish_year as publish_year','tv_series.maturity_rating as age_req','tv_series.detail as detail','seasons.season_no as season_no','videolinks.iframeurl as iframeurl')->where('seasons.a_language', 'LIKE', '%' . $lang->id . '%')
                                          ->where('tv_series.id',$item->tv_series_id)->first();
                                          
                                 
                                  
                                  if(isset($gtvs)){
                                    
                                     array_push($audiogenreitems, $gtvs);
                                           
                                  }
                                    
                                  if($section->order == 1){
                                    arsort($audiogenreitems);
                                  }

                                  if(count($audiogenreitems) == $section->item_limit*2){
                                      break;
                                      exit(1);
                                  }

                              }
                            
                            $audiogenreitems = array_values(array_filter($audiogenreitems));

                            
                        @endphp
                              
    @if($audiogenreitems != NULL && count($audiogenreitems)>0)
     <h5 class="section-heading">{{  $lang->language }} in {{ $menu->name }}</h5>
      
     {{--  @if($auth && $subscribed==1)
      
        <a href="#" class="see-more"> <b>{{__('staticwords.viewall')}}</b></a>
     
      @else
      
        <a href="#" class="see-more"> <b>{{__('staticwords.viewall')}}</b></a>
       
      @endif --}}
    @endif   
                           
   @if($section->view == 1)
   
      
        <div class="genre-prime-slider owl-carousel">
          @foreach($audiogenreitems as $item)
        
         <!-- List view language movies and tv shows -->
             
                 @if($item->status == 1)
                    @if($item->type == 'M')
                     @php
                           $image = 'images/movies/thumbnails/'.$item->thumbnail;
                          // Read image path, convert to base64 encoding
                          
                          $imageData = base64_encode(@file_get_contents($image));
                          if($imageData){
                          // Format the image SRC:  data:{mime};base64,{data};
                          $src = 'data: '.mime_content_type($image).';base64,'.$imageData;
                          }else{
                              $src = url('images/default-thumbnail.jpg');
                          }
                      @endphp
                       <div class="genre-prime-slide">
                          <div class="genre-slide-image protip" data-pt-placement="outside" data-pt-title="#prime-mix-description-block{{$item->id}}">
                            @if($auth && $subscribed==1)
                            <a href="{{url('movie/detail',$item->id)}}">
                              @if($src)
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              @else
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              @endif
                            </a>
                            @else
                              <a href="{{url('movie/guest/detail',$item->id)}}">
                              @if($src)
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              @else
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              @endif
                            </a>
                            @endif
                          </div>
                          <div id="prime-mix-description-block{{$item->id}}" class="prime-description-block">
                                <h5 class="description-heading">{{$item->title}}</h5>
                                <div class="movie-rating">{{__('staticwords.tmdbrating')}} {{$item->rating}}</div>
                                <ul class="description-list">
                                  <li>{{$item->duration}} {{__('staticwords.mins')}}</li>
                                  <li>{{$item->publish_year}}</li>
                                  <li>{{$item->maturity_rating}}</li>
                                 
                                </ul>
                                <div class="main-des">
                                  <p>{{$item->detail}}</p>
                                  <a href="#"></a>
                                </div>
                                @if($catlog==1 && is_null($subscribed))
                                @if($withlogin==0 && $auth)
                                  @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('watchTrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @else
                                   @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('guestwatchtrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @endif

                                  @endif

                                  @if($auth && $subscribed==1)
                                <div class="des-btn-block">
                                   @if($item->maturity_rating == 'all age' || $age>=str_replace('+', '', $item->maturity_rating) )
                                  @if($item->video_link['iframeurl'] != null)
                                  
                                  <a onclick="playoniframe('{{ $item->video_link['iframeurl'] }}','{{ $item->id }}','movie')" class="btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                  </a>

                                  @else
                                    <a href="{{route('watchmovie',$item->id)}}" class="iframe btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                    </a>
                                  @endif
                                  @else
                                    <a onclick="myage({{$age}})" class=" btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                    </a>
                                  @endif
                                  
                                  @if($withlogin==0 && $auth)
                                  @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('watchTrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @else
                                   @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('guestwatchtrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @endif

                                  @if (isset($wishlist_check->added))
                                    <button onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{$wishlist_check->added == 1 ? __('staticwords.removefromwatchlist') : __('staticwords.addtowatchlist')}}</button>
                                  @else
                                 
                                    <button onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{__('staticwords.addtowatchlist')}}</button>
                                  @endif
                                  
                                </div>
                                @endif
                              </div>
                        </div>
                    @endif

                    @if($item->type == 'T')
                      @php
                           $image = 'images/tvseries/thumbnails/'.$item->thumbnail;
                          // Read image path, convert to base64 encoding
                          
                          $imageData = base64_encode(@file_get_contents($image));
                          if($imageData){
                          // Format the image SRC:  data:{mime};base64,{data};
                          $src = 'data: '.mime_content_type($image).';base64,'.$imageData;
                          }else{
                              $src = url('images/default-thumbnail.jpg');
                          }
                      @endphp
                     <div class="genre-prime-slide">
                        <div class="genre-slide-image protip" data-pt-placement="outside" data-pt-title="#prime-mix-description-block{{$item->id}}{{$item->type}}">
                            @if($auth && $subscribed==1)
                            <a @if(isset($gets1)) href="{{url('show/detail',$item->seasonid)}}" @endif>
                              @if($item->thumbnail != null)
                                
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              
                              @else
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              @endif
                            </a>
                            @else
                             <a @if(isset($gets1)) href="{{url('show/guest/detail',$item->seasonid)}}" @endif>
                              @if($item->thumbnail != null)
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                            
                              @else
                                <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                              @endif
                            </a>
                            @endif 
                        </div>
                        <div id="prime-mix-description-block{{$item->id}}{{$item->type}}" class="prime-description-block">
                          <h5 class="description-heading">{{$item->title}}</h5>
                          <div class="movie-rating">{{__('staticwords.tmdbrating')}} {{$item->rating}}</div>
                          <ul class="description-list">
                            <li>{{__('staticwords.season')}} {{$item->season_no}}</li>
                            <li>{{$item->publish_year}}</li>
                            <li>{{$item->age_req}}</li>
                            
                          </ul>
                          <div class="main-des">
                            @if ($item->detail != null || $item->detail != '')
                              <p>{{$item->detail}}</p>
                            @else
                              <p>{{$item->detail}}</p>
                            @endif
                            <a href="#"></a>
                          </div>
                          @if($auth && $subscribed==1)
                          <div class="des-btn-block">
                            @if (isset($gets1->episodes[0]))
                              @if($item->age_req == 'all age' || $age>=str_replace('+', '', $item->age_req) )

                              @if($gets1->episodes[0]->video_link['iframeurl'] !="")
                               
                              <a href="#" onclick="playoniframe('{{ $gets1->episodes[0]->video_link['iframeurl'] }}','{{ $gets1->episodes[0]->seasons->tvseries->id }}','tv')" class="btn btn-play"><span class= "play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                               </a>

                              @else
                              <a href="{{ route('watchTvShow',$item->seasonid) }}" class="iframe btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span></a>
                              @endif
                              @else
                               <a onclick="myage({{$age}})" class=" btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span></a>
                              @endif
                             
                            @endif
                             @if(isset($gets1))
                            @if (isset($wishlist_check->added))
                              <a onclick="addWish({{$item->seasonid}},'{{$gets1->type}}')" class="addwishlistbtn{{$item->seasonid}}{{$gets1->type}} btn-default">{{$wishlist_check->added == 1 ? __('staticwords.removefromwatchlist') : __('staticwords.addtowatchlist')}}</a>
                            @else
                            @if($gets1)
                              <a onclick="addWish({{$item->seasonid}},'{{$gets1->type}}')" class="addwishlistbtn{{$item->seasonid}}{{$gets1->type}} btn-default">{{__('staticwords.addtowatchlist')}}
                              </a>
                              @endif
                              @endif
                            @endif
                          </div>
                          @endif
                        </div>
                     </div>
                    @endif
                 @endif
            
            <!-- end -->

          @endforeach
        </div>
     
   <!-- List view movies by language END -->
   @endif
   

                        
  @if($section->view == 0)
    
    <!-- Grid view language by movies -->
      <div class="genre-prime-block">
              
                @foreach($audiogenreitems as $item)
                   @php
                     

                     if(isset($auth)){
                        if ($item->type == 'M') {
                          $wishlist_check = \Illuminate\Support\Facades\DB::table('wishlists')->where([
                                                                            ['user_id', '=', $auth->id],
                                                                            ['movie_id', '=', $item->id],
                                                                          ])->first();
                        }
                      }

       

                      $gets1 = App\Season::where('tv_series_id','=',$item->id)->first();

                      if (isset($gets1)) {


                        $wishlist_check = \Illuminate\Support\Facades\DB::table('wishlists')->where([
                                                                    ['user_id', '=', $auth->id],
                                                                    ['season_id', '=', $gets1->id],
                          ])->first();


                        }

          

                       
                    @endphp
                    @if($item->status == 1)
                      @if($item->type == 'M')
                      
                        @php
                           $image = 'images/movies/thumbnails/'.$item->thumbnail;
                          // Read image path, convert to base64 encoding
                          
                          $imageData = base64_encode(@file_get_contents($image));
                          if($imageData){
                          // Format the image SRC:  data:{mime};base64,{data};
                          $src = 'data: '.mime_content_type($image).';base64,'.$imageData;
                          }else{
                              $src = url('images/default-thumbnail.jpg');
                          }
                        @endphp
                        <div class="col-lg-2 col-md-3 col-xs-6 col-sm-4">
                          <div class="cus_img">
                            <div class="genre-slide-image protip" data-pt-placement="outside" data-pt-interactive="false" data-pt-title="#prime-mix-description-block{{$item->id}}">
                                @if($auth && $subscribed==1)
                                  <a href="{{url('movie/detail',$item->id)}}">
                                  @if($src)
                                    <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                  @else
                                    <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                  @endif
                                 </a>
                                @else
                                   <a href="{{url('movie/guest/detail',$item->id)}}">
                                    @if($src)
                                      <img src="{{$src}}" class="img-responsive" alt="genre-image">
                                    @else
                                      <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                    @endif
                                  </a>

                                  @endif
                            
                             </div>
                             <div id="prime-mix-description-block{{$item->id}}" class="prime-description-block">
                                <h5 class="description-heading">{{$item->title}}</h5>
                                <div class="movie-rating">{{__('staticwords.tmdbrating')}} {{$item->rating}}</div>
                                <ul class="description-list">
                                  <li>{{$item->duration}} {{__('staticwords.mins')}}</li>
                                  <li>{{$item->publish_year}}</li>
                                  <li>{{$item->maturity_rating}}</li>
                                 
                                </ul>
                                <div class="main-des">
                                  <p>{{$item->detail}}</p>
                                  <a href="#"></a>
                                </div>
                                @if($catlog==1 && is_null($subscribed))
                                @if($withlogin==0 && $auth)
                                  @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('watchTrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @else
                                   @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('guestwatchtrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @endif

                                  @endif

                                  @if($auth && $subscribed==1)
                                <div class="des-btn-block">
                                   @if($item->maturity_rating == 'all age' || $age>=str_replace('+', '', $item->maturity_rating) )
                                  @if($item->video_link['iframeurl'] != null)
                                  
                                  <a onclick="playoniframe('{{ $item->video_link['iframeurl'] }}','{{ $item->id }}','movie')" class="btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                  </a>

                                  @else
                                    <a href="{{route('watchmovie',$item->id)}}" class="iframe btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                    </a>
                                  @endif
                                  @else
                                    <a onclick="myage({{$age}})" class=" btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                    </a>
                                  @endif
                                  
                                  @if($withlogin==0 && $auth)
                                  @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('watchTrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @else
                                   @if($item->trailer_url != null || $item->trailer_url != '')
                                     <a class="iframe btn btn-default" href="{{ route('guestwatchtrailer',$item->id) }}">{{__('staticwords.watchtrailer')}}</a>
                                  @endif
                                  @endif

                                  @if (isset($wishlist_check->added))
                                    <button onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{$wishlist_check->added == 1 ? __('staticwords.removefromwatchlist') : __('staticwords.addtowatchlist')}}</button>
                                  @else
                                 
                                    <button onclick="addWish({{$item->id}},'{{$item->type}}')" class="addwishlistbtn{{$item->id}}{{$item->type}} btn-default">{{__('staticwords.addtowatchlist')}}</button>
                                  @endif
                                  
                                </div>
                                @endif
                              </div>
                            </div>
                        </div>
                      @endif

                      @if($item->type == 'T')
                          @php
                             $image = 'images/tvseries/thumbnails/'.$item->thumbnail;
                            // Read image path, convert to base64 encoding
                            
                            $imageData = base64_encode(@file_get_contents($image));
                            if($imageData){
                            // Format the image SRC:  data:{mime};base64,{data};
                            $src = 'data: '.mime_content_type($image).';base64,'.$imageData;
                            }else{
                                $src = url('images/default-thumbnail.jpg');
                            }
                          @endphp
                        <div class="col-lg-4 col-md-9 col-xs-6 col-sm-6">
                          <div class="genre-slide-image protip">
                             @if($auth && $subscribed==1)
                              <a @if(isset($gets1)) href="{{url('show/detail',$item->seasonid)}}" @endif>
                                @if($src)
                                  
                                  <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                
                                @else
                                  <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                @endif
                              </a>
                              @else
                               <a @if(isset($gets1)) href="{{url('show/guest/detail',$item->seasonid)}}" @endif>
                                @if($src)
                                  <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                
                                @else
                                  <img src="{{ $src }}" class="img-responsive" alt="genre-image">
                                @endif
                              </a>
                              @endif
                         
                          </div>
                         <div id="prime-mix-description-block{{$item->id}}{{$item->type}}" class="prime-description-block">
                            <h5 class="description-heading">{{$item->title}}</h5>
                            <div class="movie-rating">{{__('staticwords.tmdbrating')}} {{$item->rating}}</div>
                            <ul class="description-list">
                              <li>{{__('staticwords.season')}} {{$item->season_no}}</li>
                              <li>{{$item->publish_year}}</li>
                              <li>{{$item->age_req}}</li>
                             
                            </ul>
                            <div class="main-des">
                              @if ($item->detail != null || $item->detail != '')
                                <p>{{$item->detail}}</p>
                              @else
                                <p>{{$item->detail}}</p>
                              @endif
                              <a href="#"></a>
                            </div>
                            @if($auth && $subscribed==1)
                            <div class="des-btn-block">
                              @if (isset($gets1->episodes[0]))
                                @if($item->age_req == 'all age' || $age>=str_replace('+', '', $item->age_req) )

                                @if($gets1->episodes[0]->video_link['iframeurl'] !="")
                                 
                                <a href="#" onclick="playoniframe('{{ $gets1->episodes[0]->video_link['iframeurl'] }}','{{ $gets1->episodes[0]->seasons->tvseries->id }}','tv')" class="btn btn-play"><span class= "play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span>
                                 </a>

                                @else
                                <a href="{{ route('watchTvShow',$item->seasonid) }}" class="iframe btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span></a>
                                @endif
                                @else
                                 <a onclick="myage({{$age}})" class=" btn btn-play"><span class="play-btn-icon"><i class="fa fa-play"></i></span> <span class="play-text">{{__('staticwords.playnow')}}</span></a>
                                @endif
                               
                              @endif
                               @if(isset($gets1))
                              @if (isset($wishlist_check->added))
                                <a onclick="addWish({{$item->seasonid}},'{{$gets1->type}}')" class="addwishlistbtn{{$item->seasonid}}{{$gets1->type}} btn-default">{{$wishlist_check->added == 1 ? __('staticwords.removefromwatchlist') : __('staticwords.addtowatchlist')}}</a>
                              @else
                              @if($gets1)
                                <a onclick="addWish({{$item->seasonid}},'{{$gets1->type}}')" class="addwishlistbtn{{$item->seasonid}}{{$gets1->type}} btn-default">{{__('staticwords.addtowatchlist')}}
                                </a>
                                @endif
                                @endif
                              @endif
                            </div>
                            @endif
                          </div>
                      </div>
                      @endif
                    @endif
                @endforeach

        </div>
   
  <!--end grid view by language-->
  @endif
  <br/>
  @endforeach
  @section('custom-script')

   <script>

      function myage(age){
        if (age==0) {
        $('#ageModal').modal('show'); 
      }else{
          $('#ageWarningModal').modal('show');
      }
    }
</script>
<script type="text/javascript">


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

    function addWish(id, type) {
      app.addToWishList(id, type);
      setTimeout(function() {
        $('.addwishlistbtn'+id+type).text(function(i, text){
          return text == "{{__('staticwords.addtowatchlist')}}" ? "{{ __('staticwords.removefromwatchlist') }}" : "{{__('staticwords.addtowatchlist')}}";
        });
      }, 100);
    }
  </script>
  @endsection