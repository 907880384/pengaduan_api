@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  
  <div id="complaintStatistic" class="row">
    @include('pages.dashboard.badge_statistic')
  </div>

</section>
@endsection

@push('scripts')
<script>
  const urlStatistic = "{!! url('/dashboard') !!}"

  function complaintStatistic() {
    $.ajax({
      type: "GET",
      url: urlStatistic,
      success: function (data) {  
        $("#complaintStatistic").html(data);
      }
    });
  }

  $(function () {
    

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
