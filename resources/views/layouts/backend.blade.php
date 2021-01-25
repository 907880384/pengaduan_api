<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Aplikasi Pengaduan</title>
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
    window.globalBroadcast = {
      event: {
        complaint: {
          channelName: 'complaint-channel',
          eventName: "App\\Events\\ComplaintsEvent"
        },
        assignedComplaint: {
          channelName: 'assign-complaint',
          eventName: 'App\\Events\\AssignedComplaintEvent'
        }
      },
      notification:{
        complaint: "App\\Notifications\\NotifikasiComplaint",
        assignedComplaint: "App\\Notifications\\AssignedNotif"
      }
    }

    var notifications = [];

    function loadNotification(user_id) {
      $.get('/notification/get/by/' + user_id, (data) => {
        console.log(data);
        setNotification(data, "#notifications")
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
        const url = "{{  url('/notification/all/') }}/" + data.notifiable_id
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
      var to = 'notification/read';

      if(data.type === globalBroadcast.notification.complaint) {
        to = to + '/complaint/' + data.id + '/user/' + data.notifiable_id          
      }

      if(data.type === globalBroadcast.notification.assignedComplaint) {
        to = to + '/assigned/' + data.id + '/user/' + data.notifiable_id;
      }

      return to;
    }

    function textNotification(data) 
    {
      var text = '';
      text += '<div class="dropdown-item-icon bg-primary text-white">'
        text += '<i class="fas fa-arrow-alt-circle-right"></i>'
      text += '</div>';

      text += '<div class="dropdown-item-desc">';
        text += data.data.message;
        text += '<div class="text-info">';
          text += moment(data.created_at).fromNow()
        text += '</div>';
      text += '</div>';
      return text;
    }

    $(function () { 
      
      let authUser   = @json(auth()->user());
      let socketIP   = "127.0.0.1";
      let socketPORT = "8005";
      let socket = io(socketIP + ":" + socketPORT);

      console.log("AUTH USER", authUser)
      loadNotification(authUser.id);
    

      socket.on('connect', () => {
        socket.emit('sendUserLogin', authUser.id);
      });

      socket.on('sendDataUserActiveLogin', (data) => {
        console.log("Now Data Receive", data);
      });

      socket.on(globalBroadcast.event.complaint.channelName + ":" + globalBroadcast.event.complaint.eventName, (message) => {
        console.log(message);
        const {complaint, receiverId} = message;
        if(authUser.id == receiverId) {
          loadNotification(receiverId);
        }
      });

      socket.on(globalBroadcast.event.assignedComplaint.channelName + ":" + globalBroadcast.event.assignedComplaint.eventName, (message) => {
        console.log(message);
        const {complaint, receiveAssigned} = message;
        console.lo
        if(authUser.id == receiveAssigned) {
          loadNotification(receiveAssigned);
        }
      });

    });
  </script>
  @stack('scripts')
</body>
</html>