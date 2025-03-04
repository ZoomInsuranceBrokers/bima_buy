<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('storage/' . Auth::user()->image_path)}}" alt="profile">
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{Auth::user()->first_name.' '.Auth::user()->last_name}}</span>
          <span class="text-secondary text-small">Regional Coordinator</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('user.dashboard')}}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <span class="menu-title">Leads</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-crosshairs-gps menu-icon"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{route('user.createLead')}}">Create a new lead</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{route('user.completedLead')}}">Completed leads</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{route('user.cancelLeads')}}">Cancel leads</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('user.policyCopy')}}">
        <span class="menu-title">Policy Copy</span>
        <i class="mdi mdi-newspaper menu-icon"></i>
      </a>
    </li>
    <!-- <li class="nav-item">
      <a class="nav-link" href="{{route('user.wallet')}}">
        <span class="menu-title">Wallet</span>
        <i class="mdi mdi-wallet menu-icon"></i>
      </a>
    </li> -->
  </ul>
</nav>