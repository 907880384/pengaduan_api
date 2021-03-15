<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Pengaduan App') }}</title>
  <link rel="shortcut icon" href="{{ asset('assets/img/complaint.png') }}" type="image/x-icon">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  
  @yield('baseStyles')
</head>
<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>

      <x-topnav />
      <x-sidenav />

      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
      </div>

      <x-footer />
    </div>
  </div>


  <script src="{{ asset('js/app.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

  <script>
    var urlNotif = "{{ url('mobile_notification') }}"

    window.CLIENT_SOCKET_HOST = SOCKET_HOST;
    window.CLIENT_SOCKET_PORT = SOCKET_PORT;

    window.globalBroadcast = {
      event: {
        complaint: {
          channelName: 'complaint-channel',
          eventName: "ComplaintEvent"
        },
        assignedComplaint: {
          channelName: 'assign-complaint-channel',
          eventName: 'AssignedComplaintEvent'
        },
        assignedWorkingComplaint: {
          channelName: 'start-work-complaint-channel',
          eventName: 'StartWorkComplaintEvent'
        },
        finishWorkComplaint: {
          channelName: 'finished-work-complaint-channel',
          eventName: 'FinishWorkComplaintEvent',
        },
        notification: {
          channelName: 'notification-channel',
          eventName: 'NotificationEvent',
        },
      },
    }

    var notifications = [];

    function loadNotification(user_id) {
      $.get(`${urlNotif}/find/${user_id}/limit`, (data) => {
        console.log("Result Get Notification Data", data);
        setNotification(data.result, "#notifications")
      });
    }

    function setNotification(data, target)
    {
      notifications = _.concat(notifications, data);
      notifications.slice(0, 5);
      showNotification(notifications, target);
    }

    function showNotification(data, target)
    {
      if(data.length) {
        const url = urlNotif + '/show';

        var htmlElements = data.map(function (notification) {
          return makeNotification(notification);
        });

        $(target).addClass('beep');
        $(target + 'Menu').html(htmlElements.join(''));
        $(target + 'Footer').html('<a href="' + url + '">View All<i class="fas fa-chevron-right"></i></a>')
      }
      else {
        $(target).removeClass('beep');
        $(target + 'Menu').html('<li class="dropdown-header">No notifications</li>');
        $(target).removeClass('has-notifications');
        $(target + 'Footer').html("<span>You don't have notification message</span>")
      }
    }

    function makeNotification(data)
    {
      const url = routeNotification(data);
      const msg = textNotification(data);

      var str = '<a href="' + url + '" class="dropdown-item dropdown-item-unread">' + msg + '</a>';
      console.log(str)
      return str
    }

    function routeNotification(data) {
      var to = ''
      to += urlNotif + '/read/' + data.id;
      return to;
    }

    function textNotification(data)
    {
      var text = '';
      text += '<div class="dropdown-item-icon bg-primary text-white">'
        text += '<i class="fas fa-arrow-alt-circle-right"></i>'
      text += '</div>';

      text += '<div class="dropdown-item-desc">';
        text += data.messages;
        text += '<div class="text-info">';
          text += moment(data.created_at).fromNow()
        text += '</div>';
      text += '</div>';
      return text;
    }

    $(function () {

      let authUser   = @json(auth()->user());
      let socket = io(CLIENT_SOCKET_HOST + ":" + CLIENT_SOCKET_PORT);

      console.log("AUTH USER", authUser)
      loadNotification(authUser.id);


      socket.on('connect', () => {
        socket.emit('sendUserLogin', authUser.id);
      });

      socket.on('sendDataUserActiveLogin', (data) => {
        console.log("Now Data Receive", data);
      });


      socket.on(globalBroadcast.event.complaint.channelName + ":" + globalBroadcast.event.complaint.eventName, (message) => {
        console.log(`${globalBroadcast.event.complaint.channelName}`, message);
        if(authUser.id == message.receiveData) {
          loadNotification(message.receiveData);
        }
      });

      socket.on(globalBroadcast.event.assignedComplaint.channelName + ":" + globalBroadcast.event.assignedComplaint.eventName, (message) => {
        console.log(message);
        if(authUser.id == message.receiveData) {
          loadNotification(message.receiveData);
        }
      });

      socket.on(globalBroadcast.event.assignedWorkingComplaint.channelName + ":" + globalBroadcast.event.assignedWorkingComplaint.eventName, (message) => {
        console.log(message);
        const filters = message.receiveData.filter((item) => item == authUser.id);
        if(filters.length > 0) {
          loadNotification(filters[0]);
        }
      });


      socket.on(globalBroadcast.event.finishWorkComplaint.channelName + ":" + globalBroadcast.event.finishWorkComplaint.eventName, (message) => {
        console.log(message);
        const filters = message.receiveData.filter((item) => item == authUser.id);
        if(filters.length > 0) {
          loadNotification(filters[0]);
        }
      });
    });
  </script>
  @stack('scripts')
</body>
</html>
