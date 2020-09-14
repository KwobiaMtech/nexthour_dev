<div>
   {!! $revenue_chart->render() !!}
</div>
<br/>
 <table id="full_detail_table" class="table table-hover db">
    <thead>
      <tr class="table-heading-row">
        <th>
          #
        </th>
        <th>User Name</th>
        <th>Payment Method</th>
        <th>Paid Amount</th>
        <th>Subscription From</th>
        <th>Subscription To</th>
        <th>Date</th>
      </tr>
    </thead>
    @if ($revenue_report)
      <tbody>
        @foreach ($revenue_report as $key => $report)
          <tr id="item-{{$report->id}}">
            <td>
              {{$key+1}}
            </td>
            <td>{{$report->user_name}}</td>
            <td>{{$report->method}}</td>
            <td><i class="{{ $currency_symbol }}" aria-hidden="true"></i>{{$report->price}}</td>
            <td>{{$report->subscription_from}}</td>
            <td>{{$report->subscription_to}}</td>
            <td>{{$report->created_at}}</td>
          </tr>
      
        @endforeach
      </tbody>
    @endif
  </table>