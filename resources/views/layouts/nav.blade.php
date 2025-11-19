<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ url(config('contants.logo')) }}" style="width: 100%;
              padding: 16px;"
                alt="logo icon">
        </div>
        {{-- <div>
            <h4 class="logo-text">{{ config('app.name') }}</h4>
        </div> --}}
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        <li>

            <a href="{{ route(Auth::user()->role . '.dashboard') }}">
                <div class="parent-icon"><i class="bx bx-home-alt"></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        @if (auth()->user()->role == 'admin')
            <li>
                <a href="{{ route('employees.index') }}">
                    <div class="parent-icon"><i class="lni lni-users"></i>
                    </div>
                    <div class="menu-title">Employees</div>
                </a>
            </li>
            <li>
                <a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}">
                    <div class="parent-icon"><i class="bx bx-store-alt"></i>
                    </div>
                    <div class="menu-title">Exhibitors</div>
                </a>
            </li>
            <li>
                <a href="{{ route('contacts.index', ['type' => 'visitor']) }}">
                    <div class="parent-icon"><i class="bx bx-user"></i>
                    </div>
                    <div class="menu-title">Visitors</div>
                </a>
            </li>
            <li>
                <a href="{{ route('locations.index') }}">
                    <div class="parent-icon"><i class="bx bx-location-plus"></i>
                    </div>
                    <div class="menu-title">Locations</div>
                </a>
            </li>

            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bxs-megaphone"></i>
                    </div>
                    <div class="menu-title">Campaign Management</div>
                </a>
                <ul>
                    <li> <a href="{{ route('campaigns.index') }}"><i class='bx bx-radio-circle'></i> All Campaigns</a>
                    </li>
                    <li> <a href="{{ route('campaigns.create') }}"><i class='bx bx-radio-circle'></i>Create Campaign</a>
                </ul>
            </li>
        @endif
        @if (auth()->user()->role == 'employee')
            <li>
                <a href="{{ route('leads.index') }}">
                    <div class="parent-icon"><i class="bx bx-location-plus"></i>
                    </div>
                    <div class="menu-title">Leads</div>
                </a>
            </li>
        @endif


        {{-- <hr>
        <li class="menu-label"><strong>Settings</strong></li>
        <li>
            <a href="{{ route('admin.change-password') }}">
                <div class="parent-icon"><i class="bx bx-lock"></i>
                </div>
                <div class="menu-title">Change Password</div>
            </a>
        </li>
        <li>
            <a href="{{ route('logout') }}">
                <div class="parent-icon"><i class="bx bx-log-out-circle"></i>
                </div>
                <div class="menu-title">Logout</div>
            </a>
        </li> --}}

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
