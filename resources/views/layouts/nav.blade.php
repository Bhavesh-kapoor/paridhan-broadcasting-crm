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
                    <div class="menu-title">Location Management</div>
                </a>
            </li>

            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bxs-megaphone"></i>
                    </div>
                    <div class="menu-title">Campaign Management</div>
                </a>
                <ul>
                    <li> <a href="{{ route('campaigns.index') }}"><i class='bx bx-radio-circle'></i>All Campaigns</a>
                    </li>
                    <li> <a href="{{ route('campaigns.create') }}"><i class='bx bx-radio-circle'></i>Create Campaign</a>
                    </li>
                    <li> <a href="{{ route('templates.index') }}"><i class='bx bx-radio-circle'></i>Templates</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('conversations.index') }}">
                    <div class="parent-icon"><i class="bx bx-conversation"></i>
                    </div>
                    <div class="menu-title">Conversations</div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings.index') }}">
                    <div class="parent-icon"><i class="bx bx-calendar-check"></i>
                    </div>
                    <div class="menu-title">Bookings</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->role == 'employee')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bxs-megaphone"></i>
                    </div>
                    <div class="menu-title">Campaigns</div>
                </a>
                <ul>
                    <li> <a href="{{ route('employee.campaigns.index') }}"><i class='bx bx-radio-circle'></i>All Campaigns</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bx-conversation"></i>
                    </div>
                    <div class="menu-title">Conversations</div>
                </a>
                <ul>
                    <li> <a href="{{ route('conversations.index') }}"><i class='bx bx-radio-circle'></i>All Conversations</a>
                    </li>
                    <li> <a href="{{ route('conversations.index', ['status' => 'interested']) }}"><i class='bx bx-radio-circle'></i>Interested</a>
                    </li>
                    <li> <a href="{{ route('conversations.index', ['status' => 'materialised']) }}"><i class='bx bx-radio-circle'></i>Materialised</a>
                    </li>
                    <li> <a href="{{ route('conversations.index', ['status' => 'busy']) }}"><i class='bx bx-radio-circle'></i>Busy</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('table-availability.index') }}">
                    <div class="parent-icon"><i class="bx bx-table"></i>
                    </div>
                    <div class="menu-title">Table Availability</div>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.bookings.index') }}">
                    <div class="parent-icon"><i class="bx bx-calendar-check"></i>
                    </div>
                    <div class="menu-title">My Bookings</div>
                </a>
            </li>
        @endif

        <hr>
        <li class="menu-label"><strong>Account</strong></li>
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="parent-icon"><i class="bx bx-log-out-circle"></i>
                </div>
                <div class="menu-title">Logout</div>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
