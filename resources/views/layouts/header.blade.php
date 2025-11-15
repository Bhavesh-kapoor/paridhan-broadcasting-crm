<!--start header -->
<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>

            <div class="search-bar d-lg-block d-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                <a href="avascript:;" class="btn d-flex align-items-center"><i class='bx bx-search'></i>Search</a>
            </div>

            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                        data-bs-target="#SearchModal">
                        <a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
                        </a>
                    </li>

                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            data-bs-toggle="dropdown"><span class="alert-count">7</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>
                                    <p class="msg-header-badge">8 New</p>
                                </div>
                            </a>
                            <div class="header-notifications-list">
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="user-online">
                                            <img src="{{ asset('/assets/images/avatars/avatar-1.png') }}"
                                                class="msg-avatar" alt="user avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Daisy Anderson<span class="msg-time float-end">5 sec
                                                    ago</span></h6>
                                            <p class="msg-info">The standard chunk of lorem</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="notify bg-light-danger text-danger">dc
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">New Orders <span class="msg-time float-end">2 min
                                                    ago</span></h6>
                                            <p class="msg-info">You have recived new orders</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="user-online">
                                            <img src="{{ asset('/assets/images/avatars/avatar-2.png') }}"
                                                class="msg-avatar" alt="user avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Althea Cabardo <span class="msg-time float-end">14
                                                    sec ago</span></h6>
                                            <p class="msg-info">Many desktop publishing packages</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="notify bg-light-success text-success">
                                            <img src="{{ asset('/assets/images/app/outlook.png') }}" width="25"
                                                alt="user avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Account Created<span class="msg-time float-end">28
                                                    min
                                                    ago</span></h6>
                                            <p class="msg-info">Successfully created new email</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="notify bg-light-info text-info">Ss
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">New Product Approved <span class="msg-time float-end">2
                                                    hrs ago</span></h6>
                                            <p class="msg-info">Your new product has approved</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="user-online">
                                            <img src="{{ asset('/assets/images/avatars/avatar-4.png') }}"
                                                class="msg-avatar" alt="user avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Katherine Pechon <span class="msg-time float-end">15
                                                    min ago</span></h6>
                                            <p class="msg-info">Making this the first true generator</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="notify bg-light-success text-success"><i
                                                class='bx bx-check-square'></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Your item is shipped <span
                                                    class="msg-time float-end">5 hrs
                                                    ago</span></h6>
                                            <p class="msg-info">Successfully shipped your item</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="notify bg-light-primary">
                                            <img src="{{ asset('/assets/images/app/github.png') }}" width="25"
                                                alt="user avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">New 24 authors<span class="msg-time float-end">1 day
                                                    ago</span></h6>
                                            <p class="msg-info">24 new authors joined last week</p>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="user-online">
                                            <img src="{{ asset('/assets/images/avatars/avatar-8.png') }}"
                                                class="msg-avatar" alt="user avatar">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Peter Costanzo <span class="msg-time float-end">6 hrs
                                                    ago</span></h6>
                                            <p class="msg-info">It was popularised in the 1960s</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <a href="javascript:;">
                                <div class="text-center msg-footer">
                                    <button class="btn btn-primary w-100">View All Notifications</button>
                                </div>
                            </a>
                        </div>
                    </li>

                </ul>
            </div>
            {{-- <div class="user-box  px-3">
                <img src="{{ asset('/assets/images/avatars/avatar-2.png') }}" class="user-img mx-2"
                    alt="user avatar">
                <div class="user-info">
                    <p class="user-name mb-0">{{ Auth::user()->name }}</p>
                    <p class="designattion mb-0">{{ 'ADMIN' }}</p>
                </div>
            </div> --}}
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('/assets/images/avatars/avatar-2.png') }}" class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="user-name mb-0">{{ Auth::user()->name }}</p>
                        <p class="designattion mb-0">{{ Str::upper(Auth::user()->role) }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasProfileSetup" aria-controls="offcanvasProfileSetup"><i
                                class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center"
                            href="{{ route('admin.change-password') }}"><i class="bx bx-lock fs-5"></i><span>Change
                                Password</span></a>
                    </li>

                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"><i
                                class="bx bx-log-out-circle"></i><span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>

<div class="offcanvas offcanvas-end " tabindex="-1" id="offcanvasProfileSetup"
    aria-labelledby="offcanvasProfileSetupLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasProfileSetupLabel">Profile Setup</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                    type="button" role="tab" aria-controls="home" aria-selected="true">Profile
                    Info</button>
            </li>

            @if (auth()->user()->role_code != 'CENTRE')
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                        type="button" role="tab" aria-controls="profile" aria-selected="false">Edit
                        Profile</button>
                </li>
            @endif

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                    type="button" role="tab" aria-controls="contact" aria-selected="false">Change
                    Password</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active profileInfoTab" id="home" role="tabpanel"
                aria-labelledby="home-tab">
                <div class="text-center mt-2">
                    <img src="{{ asset("/assets/images/avatars/avatar-2.png") }}" class="user-img " alt="user avatar">
                </div>
                <div class="user-info text-center">
                    <p class="user-name mb-0"><i class='bx bxs-check-circle'></i>
                        {{ auth()->user()->name }}
                    </p>
                </div>

                <div class="user-info text-center mb-1">
                    <span class="badge bg-warning">
                        {{ Auth::user()->role }}
                    </span>
                </div>

                <div class="row py-3 border-top border-bottom">
                    <div class="col-4"><strong>User ID:</strong></div>
                    <div class="col-8">{{ Auth::user()->id }}</div>
                </div>
                <div class="row py-3  border-bottom">
                    <div class="col-4"><strong>Email Id:</strong></div>
                    <div class="col-8 text-uppercase">{{ Auth::user()->email }}</div>
                </div>
                <div class="row py-3 ">
                    <div class="col-4"><strong>Mobile No:</strong></div>
                    <div class="col-8">{{ Auth::user()->phone }}</div>
                </div>
                <div class="row py-3 border-top">
                    <div class="col-4"><strong>Address:</strong></div>
                    <div class="col-8">{{ Auth::user()->address }}</div>
                </div>
            </div>

            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="card shadow-none">
                    <div class="card-body ">
                        <form class="row g-3" class="updateProfileForm" id="updateProfileForm" autocomplete="off">
                            @csrf
                            <div class="col-md-12">
                                <label for="profile_f_name" class="form-label mb-1"><strong
                                        class="text-danger">*</strong> First Name</label>
                                <input type="text" class="form-control" name="profile_f_name" id="profile_f_name"
                                    value="{{ auth()->user()->name }}">
                            </div>


                            <div class="col-md-12">
                                <label for="profile_email" class="form-label mb-1"><strong
                                        class="text-danger">*</strong> Email Id</label>
                                <input type="email" class="form-control" name="profile_email" id="profile_email"
                                    value="{{ auth()->user()->email }}">
                            </div>
                            <div class="col-md-12">
                                <label for="profile_phone" class="form-label mb-1"><strong
                                        class="text-danger">*</strong> Phone Number</label>
                                <input type="text" class="form-control" name="profile_phone" id="profile_phone"
                                    value="{{ auth()->user()->phone }}" maxlength="10">
                            </div>

                            <div class="col-md-12">
                                <label for="profile_image" class="form-label mb-1">Profile Image</label>
                                <input type="file" class="form-control" name="profile_image" id="profile_image">
                            </div>

                            <div class="col-md-12">
                                <label for="address" class="form-label mb-1"><strong class="text-danger">*</strong>
                                    Address</label>
                                <textarea class="form-control" name="address" id="address">{{ auth()->user()->address }}</textarea>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="text-center">
                                    <button type="submit" class="btn submit-btn btn-sm w-100 radius-10 py-2"
                                        id="profileFormSubmitBtn"><span class="bx bx-edit"></span>
                                        Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class="card shadow-none">
                    <div class="card-body">
                        <form action="" class="changePasswordForm row g-3" id="changePasswordForm">
                            @csrf
                            <div class="col-md-12">
                                <label for="old_password" class="form-label mb-1"><strong
                                        class="text-danger">*</strong> Old Password</label>
                                <input type="password" type="password" class="form-control" name="old_password"
                                    id="old_password">
                            </div>

                            <div class="col-md-12">
                                <label for="new_password" class="form-label mb-1"><strong
                                        class="text-danger">*</strong> New Password</label>
                                <input type="password" class="form-control" name="password" id="password">
                            </div>

                            <div class="col-md-12">
                                <label for="password_confirmation" class="form-label mb-1"><strong
                                        class="text-danger">*</strong> Confirm
                                    Password</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    id="password_confirmation">
                            </div>

                            <div class="form-group text-center my-3">
                                <button type="submit" class="btn submit-btn  btn-sm w-100 radius-10 py-2"><span
                                        class="bx bx-edit"></span>
                                    Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end header -->
