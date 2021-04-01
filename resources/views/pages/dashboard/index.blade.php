@extends('layouts.backend')

@section('baseStyles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/gijgo/1.9.13/combined/css/gijgo.min.css" integrity="sha512-oCuecFHHGu/Y4zKF8IoSoj5hQq1dLNIiUCwN08ChNW1VoMcjIIirAJT2JmKlYde6DeLN6JRSgntz6EDYDdFhCg==" crossorigin="anonymous" />
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  
  <div class="row">
    <div class="col-lg-12 col-md-12 col-12">
      <div class="form-group row">
        <label for="filterStatus" class="col-md-2 m-2">Filter Tanggal</label>
        <div class="col-md-4">
          <input id="changeDate"  class="form-control" />
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-primary" onclick="filterDate()">
            Filter
          </button>
      
          <button type="button" class="btn btn-success" onclick="refreshDate()">
            Refresh
          </button>
        </div>
      </div>
    </div>
  </div>

  <div id="complaintStatistic" class="row">
    @include('pages.dashboard.badge_statistic')
  </div>

</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gijgo/1.9.13/combined/js/gijgo.min.js" integrity="sha512-T62eI76S3z2X8q+QaoTTn7FdKOVGjzKPjKNHw+vdAGQdcDMbxZUAKwRcGCPt0vtSbRuxNWr/BccUKYJo634ygQ==" crossorigin="anonymous"></script>
<script>
  const urlStatistic = "{!! url('/dashboard') !!}"

  function complaintStatistic(dates = null) {

    const timeElapsed = Date.now();
    const today = new Date(timeElapsed);

    if(dates == null) {
      dates = today.toLocaleDateString();
    }


    $.ajax({
      type: "GET",
      url: urlStatistic,
      data: {
        dates: dates 
      },
      success: function (data) {  
        $("#complaintStatistic").html(data);
      }
    });
  }

  function filterDate() {
    const date = $("#changeDate").val();
    complaintStatistic(date);
  }

  $(function () {
    
    const timeElapsed = Date.now();
    const today = new Date(timeElapsed);

    $('#changeDate').datepicker({
      uiLibrary: 'bootstrap4',
      value: today.toLocaleDateString(),
    });


    const user   = @json(auth()->user());
    const ipSocket = CLIENT_SOCKET_HOST;
    const ipPort = CLIENT_SOCKET_PORT;

    let socket = io(ipSocket + ":" + ipPort);

    socket.on(globalBroadcast.event.complaint.channelName + ":" + globalBroadcast.event.complaint.eventName, (message) => {
      console.log(`Run on Dashboard ${globalBroadcast.event.complaint.channelName}`, message);
      console.log('[user, message]', {user, message});
      complaintStatistic();
    });


  });
</script>
@endpush
