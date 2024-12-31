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
          <span class="font-weight-bold mb-2">{{Auth::user()->first_name . ' ' . Auth::user()->last_name}}</span>
          <span class="text-secondary text-small">Zonal Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('zm.dashboard')}}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('zm.policyCopy')}}">
        <span class="menu-title">Policy Copy</span>
        <i class="mdi mdi-newspaper menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('zm.completedLeads')}}">
        <span class="menu-title">Completed leads</span>
        <i class="mdi mdi-trophy menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('zm.wallet')}}">
        <span class="menu-title">Wallet</span>
        <i class="mdi mdi-wallet menu-icon"></i>
      </a>
    </li>

  </ul>
</nav>