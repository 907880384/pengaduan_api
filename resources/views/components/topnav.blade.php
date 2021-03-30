<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <ul class="navbar-nav mr-3">
      <li>
        <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
          <i class="fas fa-bars"></i>
        </a>
      </li>
      <li>
        <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none">
          <i class="fas fa-search"></i>
        </a>
      </li>
    </ul>
  </form>
  <ul class="navbar-nav navbar-right">
    @if (strtolower(auth()->user()->roles()->first()->slug) != 'developer')
    <li class="dropdown dropdown-list-toggle">
      <a href="{{ url('list/cart/orders') }}" type="button" class="btn btn-primary">
        <i class="fas fa-shopping-cart"></i> 
        <span id="totalOrder" class="badge badge-danger"></span>
      </a>
    </li>
    

    <li class="dropdown dropdown-list-toggle">
      <a id="notifications" class="nav-link nav-link-lg" data-toggle="dropdown">
        <i class="fas fa-bell" aria-hidden="true"></i>
      </a>
      <div class="dropdown-menu dropdown-list dropdown-menu-right">
        <div class="dropdown-header">
          Notifications
        </div>
        <div id="notificationsMenu" class="dropdown-list-content dropdown-list-icons">
          <a href="#" class="dropdown-item dropdown-item-unread">
            <div class="dropdown-item-icon bg-primary text-white">
              <i class="fas fa-code"></i>
            </div>
            <div class="dropdown-item-desc">
              Template update is available now!
              <div class="time text-primary">2 Min Ago</div>
            </div>
          </a>
        </div>
        <div id="notificationsFooter" class="dropdown-footer text-center">
          <a href="#">View All <i class="fas fa-chevron-right"></i></a>
        </div>
      </div>
    </li>
    @endif

    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
        <div class="d-sm-none d-lg-inline-block">
          {{ Auth::user()->name }}
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-title text-info">Active</div>
        <a href="features-profile.html" class="dropdown-item has-icon">
          <i class="far fa-user"></i> Profile
        </a>
        <a href="features-settings.html" class="dropdown-item has-icon">
          <i class="fas fa-cog"></i> Settings
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </li>
  </ul>
</nav>