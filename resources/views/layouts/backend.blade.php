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
        startWorkingComplaint: {
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
        cartOrder: {
          channelName: {
            agree: 'agree-order-channel',
            disagree: 'disagree-order-channel',
            add: 'add-order-channel'
          },
          eventName: 'CartOrderEvent',
        }
      },
    }

    function initNotification(user_id) {
      $.get(`${urlNotif}/find/${user_id}/limit`, (data) => {
        localStorage.setItem('notifications', JSON.stringify(data.results));
        setNotification(data.results, "#notifications")
      });
    }

    function loadNotification(data) {
      setNotification(data, "#notifications")
    }

    function setNotification(data, target)
    {
      let localNotif = data;
      localNotif.slice(0, 5);
      showNotification(localNotif, target);
    }

    function showNotification(data, target)
    {
      console.log("ARRAY NOTIFICATION", data);
      if(data.length > 0) {
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

    function cartCount() {
      const urlCart = "{{ url('count/new/orders') }}"
      axios.get(urlCart).then((response) => {
        const {data, status} = response;
        if(status == 200) {
          localStorage.setItem('totalOrder', data.totalOrder.toString());
        }
      }).catch((error) => {
        console.log(error.response);
      });
    }

    function readCartCount() {
      const total = localStorage.getItem('totalOrder')
      $("#totalOrder").text(total)
    }

    $(function () {
      let authUser  = @json(auth()->user());
      let userRole  = @json(auth()->user()->roles()->first());
      let socket = io(CLIENT_SOCKET_HOST + ":" + CLIENT_SOCKET_PORT);

      initNotification(authUser.id)
      cartCount();
      readCartCount();

      socket.on('connect', () => {
        socket.emit('sendUserLogin', authUser.id);
      });

      socket.on('sendDataUserActiveLogin', (data) => {
        console.log("Now Data Receive", data);
      });

      /** EVENT CREATE COMPLAINT */
      socket.on(globalBroadcast.event.complaint.channelName + ":" + globalBroadcast.event.complaint.eventName, (message) => {
        const {roleName, mobileNotif: notifs} = message;
        
        if(userRole.slug == roleName) {
          const filterNotif = notifs.filter(item => item.receiver_id == authUser.id, [])
          if(filterNotif.length > 0) {
            let resultNotif = JSON.parse(localStorage.getItem('notifications'));
            resultNotif = [...resultNotif, ...filterNotif];
            loadNotification(resultNotif);
          }
        }
      });

      /** EVENT ASSIGNED COMPLAINT */
      socket.on(globalBroadcast.event.assignedComplaint.channelName + ":" + globalBroadcast.event.assignedComplaint.eventName, (message) => {
        const {receiveData: receivers, mobileNotif: notifs} = message;
        const filters = receivers.filter((item) => item == authUser.id, []);

        if(filters.length > 0) {
          const filterNotif = notifs.filter(item => item.receiver_id == authUser.id, [])
          let resultNotif = JSON.parse(localStorage.getItem('notifications'));
            resultNotif = [...resultNotif, ...filterNotif];

          loadNotification(resultNotif);
        }
      });

      /** START WORKING */
      socket.on(globalBroadcast.event.startWorkingComplaint.channelName + ":" + globalBroadcast.event.startWorkingComplaint.eventName, (message) => {

        const {messageNotif: notifs,  receiveData: receivers} = message;
        const filters = receivers.filter((item) => item == authUser.id, []);

        if(filters.length > 0) {
          const filterNotif = notifs.filter(item => item.receiver_id == authUser.id, [])
          let resultNotif = JSON.parse(localStorage.getItem('notifications'));
            resultNotif = [...resultNotif, ...filterNotif];

          loadNotification(resultNotif);
        }
      });

      /** FINISH WORKING */
      socket.on(globalBroadcast.event.finishWorkComplaint.channelName + ":" + globalBroadcast.event.finishWorkComplaint.eventName, (message) => {
        
        const {receiveData: receivers, mobileNotif: notifs} = message;
        const filters = receivers.filter(item => item == authUser.id);
        
        if(filters.length > 0) {
          const filterNotif = notifs.filter(item => item.receiver_id == authUser.id, [])
          let resultNotif = JSON.parse(localStorage.getItem('notifications'));
            resultNotif = [...resultNotif, ...filterNotif];
          loadNotification(resultNotif);
        }
      });


      /** Order Socket Channel */
      socket.on(globalBroadcast.event.cartOrder.channelName.add + ":" +  globalBroadcast.event.cartOrder.eventName, (message) => {
        console.log(`${globalBroadcast.event.cartOrder.channelName.add}`, message);
        const {receivers} = message;
        let totalOrder = localStorage.getItem('totalOrder') ? parseInt(localStorage.getItem('totalOrder')) : 0;

        if(userRole.slug == receivers) {
          totalOrder += 1
          localStorage.setItem('totalOrder', totalOrder);
          readCartCount()
        }
      });

      /** Agree Order Socket Channel */
      socket.on(globalBroadcast.event.cartOrder.channelName.agree + ":" + globalBroadcast.event.cartOrder.eventName, (message) => {
        console.log(`${globalBroadcast.event.cartOrder.channelName.agree}`, message);
        const {receivers} = message;
        let totalOrder = localStorage.getItem('totalOrder') ? parseInt(localStorage.getItem('totalOrder')) : 0;

        if(receivers == authUser.id) {
          totalOrder -= 1;
          localStorage.setItem('totalOrder', totalOrder);
          readCartCount()
        }
      });

      /** Disagree Order Socket Channel */
      socket.on(globalBroadcast.event.cartOrder.channelName.disagree + ":" + globalBroadcast.event.cartOrder.eventName, (message) => {
        console.log("MESSAGE", message);
        const {receivers} = message;
        let totalOrder = localStorage.getItem('totalOrder') ? parseInt(localStorage.getItem('totalOrder')) : 0;
        

        if(receivers == authUser.id) {
          totalOrder -= 1;
          localStorage.setItem('totalOrder', totalOrder);
          readCartCount()
        }
      });

    });
  </script>
  @stack('scripts')
</body>
</html>
