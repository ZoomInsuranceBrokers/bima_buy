<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <a class="navbar-brand brand-logo" href="">
      <h3 style="font-weight: bold;color: white;color:#9a55ff;text-align: center;">Bima Buy</h3>
    </a>
    <a class="navbar-brand brand-logo-mini" href="{{route('login')}}"><img src="{{asset('images/logo-mini.svg')}}"
        alt="logo" /></a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-stretch">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="mdi mdi-menu"></span>
    </button>
    <div class="search-field d-none d-md-block">
      <form class="d-flex align-items-center h-100" action="#">
        <div class="input-group">
          <div class="input-group-prepend bg-transparent">
            <i class="input-group-text border-0 mdi mdi-magnify"></i>
          </div>
          <input type="text" class="form-control bg-transparent border-0" placeholder="Search projects">
        </div>
      </form>
    </div>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <div class="nav-profile-img">
            <img style="width: 37px;height: 37px;" src="{{asset('storage/' . Auth::user()->image_path)}}" alt="image">
            <span class="availability-status online"></span>
          </div>
          <div class="nav-profile-text">
            <p class="mb-1 text-black">{{Auth::user()->first_name . ' ' . Auth::user()->last_name}}</p>
          </div>
        </a>
        <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item" href="{{route('profile')}}">
            <i class="mdi mdi-account me-2 text-success"></i>Profile </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="{{route('logout')}}">
            <i class="mdi mdi-logout me-2 text-primary"></i> Signout </a>
        </div>
      </li>
      <li class="nav-item d-none d-lg-block full-screen-link">
        <a class="nav-link">
          <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
          <i class="mdi mdi-bell-outline"></i>
          <span class="count-symbol" id="unreadCount"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-left navbar-dropdown preview-list"
          aria-labelledby="notificationDropdown" id="notificationsDropdown">
          <h6 class="p-3 mb-0">Notification</h6>
          <div class="dropdown-divider"></div>
          <div id="notificationsList"></div>
          <div class="dropdown-divider"></div>
          <a href="{{ route('notifications.fetch') }}" class="p-3 mb-0 text-center d-block text-decoration-none">
            See all notifications
          </a>

        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
      data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
@push('scripts')
  <script>
    $('#notificationDropdown').on('click', function () {
    $.ajax({
      url: '{{route('notifications.fetch')}}',
      method: 'GET',
      success: function (data) {
      let notificationsHtml = '';
      let unreadCount = 0;

      if (data.notifications.length > 0) {
        // Build notifications list
        data.notifications.forEach(notification => {
        const timeAgo = notification.time_ago;
        const isReadClass = notification.is_read ? '' : 'font-weight-bold';
        unreadCount += notification.is_read ? 0 : 1;

        notificationsHtml += `
            <a class="dropdown-item preview-item ${isReadClass}" href="{{route('notifications.fetch')}}">
            <div class="preview-thumbnail">
              <img src="${notification.image_url}" alt="image" class="profile-pic">
            </div>
            <div class="preview-item-content d-flex align-items-start flex-column justify-content-center" style="padding-right: 15px;">
              <h6 class="preview-subject ellipsis mb-1">${notification.message}</h6>
              <p class="text-gray mb-0">From: ${notification.sender_name} | ${timeAgo} ago</p>
            </div>
      
            <div class="ms-auto">
                ${notification.is_read ? '' : `<button class="btn btn-sm btn-danger mark-as-read" data-id="${notification.id}" style="font-size: 12px; padding: 5px 8px;">Mark as Read</button>`}
            </div>
            </a>
          `;
        });

        // Display unread count
        $('#unreadCount').text(unreadCount > 0 ? unreadCount : '').addClass('bg-danger');
      } else {
        // No notifications
        notificationsHtml = `
      <div class="text-center p-3">
      <p class="mb-0 text-gray">No notifications available</p>
      </div>
      `;
        // Clear unread count
        $('#unreadCount').text('');
      }

      // Update notifications dropdown
      $('#notificationsList').html(notificationsHtml);

      $('.mark-as-read').on('click', function (e) {
        e.stopPropagation(); // Prevent triggering the parent dropdown
        const notificationId = $(this).data('id');
        markNotificationAsRead(notificationId);
      });
      },
      error: function (error) {
      console.error('Error fetching notifications:', error);
      }
    });
    });

    function markNotificationAsRead(notificationId) {
    $.ajax({
      url: '{{route('notifications.markAsRead')}}',
      method: 'POST',
      data: {
      _token: '{{ csrf_token() }}',
      id: notificationId
      },
      success: function () {
      // Refresh notifications after marking as read
      $('#notificationDropdown').trigger('click');
      },
      error: function (error) {
      console.error('Error marking notification as read:', error);
      }
    });
    }

  </script>
@endpush