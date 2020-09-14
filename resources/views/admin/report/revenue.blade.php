@extends('layouts.admin')
@section('title','All Revenue Report')
@section('stylesheet')
   {!! Charts::assets() !!}
@endsection
@section('content')
  <div class="content-main-block mrg-t-40">
    <div class="admin-create-btn-block">
      <h4 class="admin-form-text">All Revenue Reports</h4>
    </div>
   
    <div class="content-block box-body">
       <div class="col-sm-3 pull-right form-group{{ $errors->has('date') ? ' has-error' : '' }}">
          <label>Date range:</label>
          <div class="input-group ">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control pull-right" id="mydate" name="date">
          </div>
       </div>
      
       <div id="maindata">
         
       </div>
     
    </div>
  </div>
@endsection
@section('custom-script')
 {{--  <script>
    $(function(){
      $('#checkboxAll').on('change', function(){
        if($(this).prop("checked") == true){
          $('.material-checkbox-input').attr('checked', true);
        }
        else if($(this).prop("checked") == false){
          $('.material-checkbox-input').attr('checked', false);
        }
      });
    });
   
  </script> --}}
   <script>
     $(document).ready(function()
     {
       $('#mydate').daterangepicker();
        var date = $('#mydate').val();
        var startDate = date.split(' - ')[0];  // return 2018-10-21
        var endDate = date.split(' - ')[1]; 
        //alert(startDate);
        $.ajax({
          type : 'GET',
          data: {startDate : startDate,
                endDate : endDate
                },
          url  : '{{ route("ajaxdatefilter") }}',
          dataType : 'html',
          success : function(data){
             $('#maindata').html('');
             $('#maindata').append(data);
          }
        });
     });
  </script>
  <script type="text/javascript">
    $('#mydate').on('change',function(){
      var k = $(this).val();
      var startDate = k.split('-')[0];
       //alert(startDate);  // return 2018-10-21
      var endDate = k.split('-')[1]; 
      //alert(endDate);
      $.ajax({
          type : 'GET',
          data : {startDate : startDate,
                endDate : endDate
                },
          url  : '{{ route("ajaxdatefilter") }}',
          dataType : 'html',
          success : function(data){
            console. log(data);
             $('#maindata').html('');
             $('#maindata').append(data);
          }
      });

    });
  </script>
@endsection