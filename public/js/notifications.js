

    window.globalBroadcast = {
      eventListener: {
        complaint: {
          channelName: 'complaint-channel',
          eventName: "App\\Events\\ComplaintsEvent"
        },
        assigned: {
          channelName: 'assign-complaint',
          eventName: "App\\Events\\AssignedComplaintEvent"
        }
      },
      notification:{
        complaint: "App\\Notifications\\NotifikasiComplaint",
        assignedComplaint: "App\\Notifications\\AssignedNotif"
      }
    }


    var notifications = [];
function loadNotification(user_id) {
  $.get('/notification/get/by/' + id, (data) => {
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
    const url = "{{  url('/notification/all/') }}/" + data.notifiable.id
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