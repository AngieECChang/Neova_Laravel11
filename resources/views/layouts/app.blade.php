<!DOCTYPE html>
<html lang="en">
<head>
  <title>諾歐科技</title>
  <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
  @include('layouts.meta')
  @include('layouts.css')
</head>
<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('hc-openlist') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            
        </div>
        <div class="sidebar-brand-text mx-3" style="color:#ff9f43 !important;font-size:14pt !important;font-weight: 600 !important;"><font color="white"><i class="fas fa-laugh-wink"></i></font> {{ session('client_name')}} {{ session('DB')}}</div>
      </a>
      <hr class="sidebar-divider my-0">
      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a href="#" class="sidebar-heading toggle-category nav-link" data-toggle="collapse" data-target="#categorylist" aria-expanded="false" aria-controls="categorylist"> 
          <i class="fas fa-fw fa-users" style="font-size:14pt !important;color:#faaacf !important;"></i>
          <span style="font-size:16pt !important;font-weight: 600 !important;color:#faaacf !important;">個案列表</span>
        </a>
        <div id="categorylist" class="collapse" style="line-height:10px !important; padding:0rem 0rem !important;" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <!-- <a class="collapse-item d-inline-block" href="{{route('hc-openlist')}}" style="font-size:11pt !important;font-weight: 600 !important;color:#6f42c1 !important;"><i class="bi bi-person-plus"></i> &nbsp;<span style="font-size:11pt !important;font-weight: 600 !important;color:#6f42c1 !important;">預收案</span>
          </a> -->
          <a class="collapse-item d-inline-block" href="{{route('hc-openlist')}}" style="font-size:11pt !important;font-weight: 600 !important;color:#6f42c1 !important;"><i class="bi bi-person-check"></i> &nbsp;<span style="font-size:12pt !important;font-weight: 600 !important;color:#6f42c1 !important;">收案</span>
          </a>
          <a class="collapse-item d-inline-block" href="{{route('hc-closelist')}}" style="font-size:11pt !important;font-weight: 600 !important;color:#6f42c1 !important;"><i class="bi bi-person-x"></i> &nbsp;<span style="font-size:12pt !important;font-weight: 600 !important;color:#6f42c1 !important;">結案</span>
          </a>
        </div>
      </div>
      </li>
      <div id="accordionCategories">
      @foreach ($allow_permission as $cateID => $permission)
        <hr class="sidebar-divider">
        <a href="#" class="sidebar-heading toggle-category" data-toggle="collapse" data-target="#category{{$cateID}}" aria-expanded="false" aria-controls="category{{$cateID}}" style="color:#f6c23e;font-size:16pt !important;font-weight: 600 !important; text-decoration: none; padding-bottom:10px;">
        <span style="font-size:16pt !important;font-weight: 600 !important;color:#f6c23e !important;">  {{ $permission->first()->cate_shortname }}</span>
        </a>
        <div id="category{{$cateID}}" class="collapse" data-parent="#accordionCategories">
          @php
            $subCategories = collect($permission)->groupBy('subcate_name');
          @endphp
          @foreach ($subCategories as $subcate_name => $items)
            @if (empty($subcate_name))
              @foreach ($items as $item)
                <li class="nav-item">
                  <a class="nav-link collapsed" href="{{ $item->link }}">
                    <i class="{{ $item->subcate_icon }}" style="font-size:13pt !important;font-weight: 600 !important; padding-bottom:3px !important;padding-top:0px !important;"></i>
                    <span style="font-size:13pt !important;font-weight: 600 !important; padding-bottom:3px !important;padding-top:0px !important;">{{ $item->name }}</span>
                  </a>
                </li>
              @endforeach
            @else
              @php
                $a_id = $items->first()->subcateID . '-' . $items->first()->itemID;
              @endphp
              <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse{{$a_id}}" aria-expanded="false" aria-controls="collapse{{$a_id}}" style="font-size:13pt !important;font-weight: 600 !important;">
                  <i class="{{$items->first()->subcate_icon}}" style="font-size:13pt !important;font-weight: 600 !important;"></i>
                  <span style="font-size:13pt !important;font-weight: 600 !important; padding-bottom:3px !important;padding-top:0px !important;">{{$subcate_name}}</span>
                </a>
                <div id="collapse{{$a_id}}" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                  <div class="bg-white py-2 collapse-inner rounded">
                    @foreach ($items as $item)
                      <a class="collapse-item d-inline-block" href="{{ $item->link }}">
                        <i class="bi bi-plus-circle"></i> &nbsp;<span style="font-size:12pt !important;font-weight: 600 !important;">{{ $item->name }}</span>
                      </a>
                    @endforeach
                  </div>
                </div>
              </li>
            @endif
          @endforeach
        </div>
      @endforeach
      </div>
      <hr class="sidebar-divider d-none d-md-block">
      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
          <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>
        <!-- Topbar Search -->
        {{-- <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
          <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                  aria-label="Search" aria-describedby="basic-addon2">
              <div class="input-group-append">
                  <button class="btn btn-primary" type="button">
                      <i class="fas fa-search fa-sm"></i>
                  </button>
              </div>
          </div>
        </form> --}}
        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">
          <!-- Nav Item - Search Dropdown (Visible Only XS) -->
          {{-- <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
              aria-labelledby="searchDropdown">
              <form class="form-inline mr-auto w-100 navbar-search">
                <div class="input-group">
                  <input type="text" class="form-control bg-light border-0 small"
                      placeholder="Search for..." aria-label="Search"
                      aria-describedby="basic-addon2">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </li> --}}
          <!-- Nav Item - Alerts -->
          <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-bell fa-fw"></i>
              <!-- Counter - Alerts -->
              <span class="badge badge-danger badge-counter">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
              aria-labelledby="alertsDropdown">
              <h6 class="dropdown-header">
                Alerts Center
              </h6>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="mr-3">
                  <div class="icon-circle bg-primary">
                    <i class="fas fa-file-alt text-white"></i>
                  </div>
                </div>
                <div>
                  <div class="small text-gray-500">December 12, 2019</div>
                  <span class="font-weight-bold">A new monthly report is ready to download!</span>
                </div>
              </a>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="mr-3">
                  <div class="icon-circle bg-success">
                    <i class="fas fa-donate text-white"></i>
                  </div>
                </div>
                <div>
                  <div class="small text-gray-500">December 7, 2019</div>
                  $290.29 has been deposited into your account!
                </div>
              </a>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="mr-3">
                    <div class="icon-circle bg-warning">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">December 2, 2019</div>
                    Spending Alert: We've noticed unusually high spending for your account.
                </div>
              </a>
              <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
            </div>
          </li>
            <!-- Nav Item - Messages -->
          <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-envelope fa-fw"></i>
              <!-- Counter - Messages -->
              <span class="badge badge-danger badge-counter">7</span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
              aria-labelledby="messagesDropdown">
              <h6 class="dropdown-header">
                  Message Center
              </h6>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                  <img class="rounded-circle" src="img/undraw_profile_1.svg"
                      alt="...">
                  <div class="status-indicator bg-success"></div>
                </div>
                <div class="font-weight-bold">
                  <div class="text-truncate">Hi there! I am wondering if you can help me with a
                      problem I've been having.</div>
                  <div class="small text-gray-500">Emily Fowler · 58m</div>
                </div>
              </a>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                  <img class="rounded-circle" src="img/undraw_profile_2.svg"
                      alt="...">
                  <div class="status-indicator"></div>
                </div>
                <div>
                  <div class="text-truncate">I have the photos that you ordered last month, how
                      would you like them sent to you?</div>
                  <div class="small text-gray-500">Jae Chun · 1d</div>
                </div>
              </a>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                  <img class="rounded-circle" src="img/undraw_profile_3.svg"
                      alt="...">
                  <div class="status-indicator bg-warning"></div>
                </div>
                <div>
                  <div class="text-truncate">Last month's report looks great, I am very happy with
                      the progress so far, keep up the good work!</div>
                  <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                </div>
              </a>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <div class="dropdown-list-image mr-3">
                  <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                      alt="...">
                  <div class="status-indicator bg-success"></div>
                </div>
                <div>
                  <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                      told me that people say this to all dogs, even if they aren't good...</div>
                  <div class="small text-gray-500">Chicken the Dog · 2w</div>
                </div>
              </a>
              <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
            </div>
          </li>
          <div class="topbar-divider d-none d-sm-block"></div>
          <!-- Nav Item - User Information -->
          <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex flex-column align-items-start mt-4" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
              <span>
                <i class="bi bi-person-circle" style="color: #a17571 !important;"></i>
                &nbsp;<span style="font-size:12pt;color: #a17571 !important;">{{ session('name')}}</span>
              </span>
              <span style="font-size:8pt;color: #4F4F4F;">自動登出時間：<span id="timer-countdown" style="font-size:8pt;color: red;"></span>秒</span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
              <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
              </a>
              <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
              </a>
              <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  登出
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <!-- End of Topbar -->
      @include('layouts.js') 
      @include('layouts.countdown') 
      <!-- Begin Page Content -->
      <div class="container-fluid">
        @yield('content')
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->    
    @include('layouts.footer')
  </div>
  <!-- End of Page Wrapper -->
  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
  </a>
  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">確定登出?</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
              </div>

              <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">取消</button>
                  <a class="btn btn-primary" href="{{ route('logout') }}">登出</a>
              </div>
          </div>
      </div>
  </div>
</body>
</html>