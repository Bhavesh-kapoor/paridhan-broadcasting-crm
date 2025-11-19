<!--==================== Preloader Start ====================-->
<div class="preloader">
    <div class="loader"></div>
</div>
<!--==================== Preloader End ====================-->

<!--==================== Sidebar Overlay End ====================-->
<div class="side-overlay"></div>
<!--==================== Sidebar Overlay End ====================-->

<!-- ============================ Sidebar Start ============================ -->

<aside class="sidebar">
    <!-- sidebar close btn -->
    <button type="button"
        class="sidebar-close-btn text-gray-500 hover-text-white hover-bg-main-600 text-md w-24 h-24 border border-gray-100 hover-border-main-600 d-xl-none d-flex flex-center rounded-circle position-absolute"><i
            class="ph ph-x"></i></button>
    <!-- sidebar close btn -->

    <a href="index-2.html"
        class="sidebar__logo text-center p-20 position-sticky inset-block-start-0 bg-white w-100 z-1 pb-10">
        <img src="{{ url(config('contants.logo')) }}" alt="Logo">
    </a>

    <div class="sidebar-menu-wrapper overflow-y-auto scroll-sm">
        <div class="p-20 pt-10">
            <ul class="sidebar-menu">

                <li class="sidebar-menu__item">
                    <a href="{{ route(Auth::user()->role . '.dashboard') }}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-squares-four"></i></span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu__item has-dropdown">
                    <a href="javascript:void(0)" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-users"></i></span>
                        <span class="text">Employees</span>
                    </a>
                    <!-- Submenu start -->
                    <ul class="sidebar-submenu">
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('employees.index') }}" class="sidebar-submenu__link"> All Employees </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('employees.index', ['status' => 'active']) }}"
                                class="sidebar-submenu__link"> Active Employees </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('employees.index', ['status' => 'inactive']) }}"
                                class="sidebar-submenu__link"> Inactive Employees </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('employees.create') }}" class="sidebar-submenu__link"> Add Employee </a>
                        </li>
                    </ul>
                    <!-- Submenu End -->
                </li>

                <!-- Exhibitors Section -->
                <li class="sidebar-menu__item has-dropdown">
                    <a href="javascript:void(0)" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-storefront"></i></span>
                        <span class="text">Exhibitors</span>
                    </a>
                    <!-- Submenu start -->
                    <ul class="sidebar-submenu">
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('contacts.index', ['type' => 'exhibitor']) }}"
                                class="sidebar-submenu__link"> All Exhibitors </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('contacts.create', ['type' => 'exhibitor']) }}"
                                class="sidebar-submenu__link"> Add Exhibitor </a>
                        </li>
                    </ul>
                    <!-- Submenu End -->
                </li>

                <!-- Visitors Section -->
                <li class="sidebar-menu__item has-dropdown">
                    <a href="javascript:void(0)" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-user-circle"></i></span>
                        <span class="text">Visitors</span>
                    </a>
                    <!-- Submenu start -->
                    <ul class="sidebar-submenu">
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('contacts.index', ['type' => 'visitor']) }}"
                                class="sidebar-submenu__link"> All Visitors </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('contacts.create', ['type' => 'visitor']) }}"
                                class="sidebar-submenu__link"> Add Visitor </a>
                        </li>
                    </ul>
                    <!-- Submenu End -->
                </li>

                <!-- Campaign Management Section -->
                <li class="sidebar-menu__item has-dropdown">
                    <a href="javascript:void(0)" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-megaphone"></i></span>
                        <span class="text">Campaign Management</span>
                    </a>
                    <!-- Submenu start -->
                    <ul class="sidebar-submenu">
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('campaigns.index') }}" class="sidebar-submenu__link"> All Campaigns </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('campaigns.create') }}" class="sidebar-submenu__link"> Create Campaign
                            </a>
                        </li>
                    </ul>
                    <!-- Submenu End -->
                </li>

                <!-- location Management Section -->
                <li class="sidebar-menu__item has-dropdown">
                    <a href="javascript:void(0)" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-map-pin"></i></span>
                        <span class="text">location Management</span>
                    </a>
                    <!-- Submenu start -->
                    <ul class="sidebar-submenu">
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('locations.index') }}" class="sidebar-submenu__link"> All locations </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('locations.create') }}" class="sidebar-submenu__link"> Create location
                            </a>
                        </li>
                    </ul>
                    <!-- Submenu End -->
                </li>

                <!-- lead Management Section -->
                <li class="sidebar-menu__item has-dropdown">
                    <a href="javascript:void(0)" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-crown"></i></span>
                        <span class="text">lead Management</span>
                    </a>
                    <!-- Submenu start -->
                    <ul class="sidebar-submenu">
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('leads.index') }}" class="sidebar-submenu__link"> All leads </a>
                        </li>
                        <li class="sidebar-submenu__item">
                            <a href="{{ route('leads.create') }}" class="sidebar-submenu__link"> Create lead
                            </a>
                        </li>
                    </ul>
                    <!-- Submenu End -->
                </li>



                <li class="sidebar-menu__item">
                    <span
                        class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Settings</span>
                </li>
                <li class="sidebar-menu__item">
                    <a href="{{ route('admin.change-password') }}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-lock-key"></i></span>
                        <span class="text">Change Password</span>
                    </a>
                </li>


                <li class="sidebar-menu__item">
                    <a href="{{ route('logout') }}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-sign-out"></i></span>
                        <span class="text">Logout</span>
                    </a>
                </li>


            </ul>
        </div>

    </div>

</aside>
<!-- ============================ Sidebar End  ============================ -->
