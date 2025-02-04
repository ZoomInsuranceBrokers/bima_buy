<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{asset('storage/' . Auth::user()->image_path)}}">
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{Auth::user()->first_name . ' ' . Auth::user()->last_name}}</span>
          <span class="text-secondary text-small">Retail Team</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('retail.dashboard')}}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{route('retail.cancelLeads')}}">
        <span class="menu-title">Cancel leads</span>
        <i class="mdi mdi-puzzle menu-icon"></i>
      </a>
    </li>
    
    <li class="nav-item">
      <a class="nav-link" href="{{route('retail.completedLeads')}}">
        <span class="menu-title">Completed leads</span>
        <i class="mdi mdi-trophy menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('retail.report')}}">
        <span class="menu-title">Report</span>
        <i class="mdi mdi-chart-bar menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>