<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{asset('storage/' . Auth::user()->image_path)}}" alt="profile">
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{Auth::user()->first_name . ' ' . Auth::user()->last_name}}</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.dashboard')}}">
        <span class="menu-title">Today Leads</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.total.leads.report')}}">
        <span class="menu-title">Total Leads</span>
        <i class="mdi mdi-cube menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.adduser')}}">
        <span class="menu-title">User Master</span>
        <i class="mdi mdi-account-plus menu-icon"></i>
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