@php
    if (Auth::guard('admin')->check()) {
        $auth_user = Auth::guard('admin')->user();
    }
    $route = Route::currentRouteName();
@endphp
<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <img src="{{ asset('wb-logo2.webp') }}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);">
                    <img src="{{ $auth_user->profile_image }}"
                        onerror="this.src = null; this.src='{{ asset('/images/default-user.png') }}'"
                        class="img-circle elevation-2" alt="User Image" style="width: 43px; height: 43px;">
                </a>
            </div>
            <div class="info text-center py-0">
                <a href="javascript:void(0);" class="d-block">{{ $auth_user->name }}</a>
                <span class="text-xs text-bold" style="color: #c2c7d0;">{{ $auth_user->email ?: 'N/A' }}</span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $route == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @canany(['super power', 'create venue_vendor', 'edit venue_vendor', 'publish venue_vendor', 'create
                    venue_vendor_list', 'edit venue_vendor_list', 'publish venue_vendor_list'])
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link venue_collapse_link venue_category_collapse_link">
                            <i class="nav-icon fas fa-map-marked-alt"></i>
                            <p>Venue
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['create venue_vendor', 'edit venue_vendor', 'publish venue_vendor', 'super power'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center
                                         {{ $route == 'venue.list' || $route == 'venue.add' || $route == 'venue.edit' ? 'active' : '' }}">
                                        <a href="{{ route('venue.list') }}" class="link_prop w-100">
                                            <i class="fas fa-list-alt nav-icon"></i>
                                            <p>Venue List</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                            @canany(['super power'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'venue_category.list' ? 'active' : '' }}">
                                        <a href="{{ route('venue_category.list') }}" class="link_prop w-100">
                                            <i class="fas fa-th-large nav-icon"></i>
                                            <p>Venue Category</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                            @canany(['create venue_vendor_list', 'edit venue_vendor_list', 'publish venue_vendor_list',
                                'super power'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'venue.listing_meta.list' ? 'active' : '' }}">
                                        <a href="{{ route('venue.listing_meta.list') }}" class="link_prop w-100">
                                            <i class="fas fa-info-circle nav-icon"></i>
                                            <p>Venue Listing Meta</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['super power', 'create venue_vendor', 'edit venue_vendor', 'publish venue_vendor', 'create
                    venue_vendor_list', 'edit venue_vendor_list', 'publish venue_vendor_list'])
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link venue_collapse_link vendor_collapse_link">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>Vendor
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            @canany(['super power', 'create venue_vendor', 'edit venue_vendor', 'publish venue_vendor'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center
                                         {{ $route == 'vendor.list' || $route == 'vendor.add' || $route == 'vendor.edit' ? 'active' : '' }}">
                                        <a href="{{ route('vendor.list') }}" class="link_prop w-100">
                                            <i class="fas fa-list nav-icon"></i>
                                            <p>Vendor List</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany

                            @canany(['super power', 'create venue_vendor_list', 'edit venue_vendor_list', 'publish
                                venue_vendor_list'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'vendor.listing_meta.list' ? 'active' : '' }}">
                                        <a href="{{ route('vendor.listing_meta.list') }}" class="link_prop w-100">
                                            <i class="fas fa-info-circle nav-icon"></i>
                                            <p>Vendor Listing Meta</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @canany(['super power', 'create blog', 'edit blog', 'publish blog'])
                    <li class="nav-item">
                        <a href="{{ route('blog.list') }}" class="nav-link {{ $route == 'blog.list' ? 'active' : '' }}">
                            <i class="fas fa-blog nav-icon"></i>
                            <p>Blog</p>
                        </a>
                    </li>
                @endcanany

                @canany(['super power', 'create author', 'edit author'])
                    <li class="nav-item">
                        <a href="{{ route('author.list') }}"
                            class="nav-link {{ $route == 'author.list' ? 'active' : '' }}">
                            <i class="fas fa-user-tie nav-icon"></i>
                            <p>Author</p>
                        </a>
                    </li>
                @endcanany

                @canany(['super power', 'create review', 'edit review', 'publish review'])
                    <li class="nav-item">
                        <a href="{{ route('review.list') }}"
                            class="nav-link {{ $route == 'review.list' ? 'active' : '' }}">
                            <i class="fas fa-star nav-icon"></i>
                            <p>Review</p>
                        </a>
                    </li>
                @endcanany

                @canany(['super power', 'create business users', 'edit business users', 'publish business users'])
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link others_collapse_link">
                            <i class="nav-icon fas fa-ellipsis-h"></i>
                            <p>Others
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['super power'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'c_num.list' ? 'active' : '' }}">
                                        <a href="{{ route('c_num.list') }}" class="link_prop w-100">
                                            <i class="fas fa-list-ol nav-icon"></i>
                                            <p>Numbers</p>
                                        </a>
                                    </span>
                                </li>
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'city.list' ? 'active' : '' }}">
                                        <a href="{{ route('city.list') }}" class="link_prop w-100">
                                            <i class="fas fa-city nav-icon"></i>
                                            <p>City</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                            @canany(['super power', 'location crud'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center
                                         {{ $route == 'location.list' || $route == 'location.add_group' || $route == 'location.edit_group' ? 'active' : '' }}">
                                        <a href="{{ route('location.list') }}" class="link_prop w-100">
                                            <i class="fas fa-map-marker-alt nav-icon"></i>
                                            <p>Location</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                            @canany(['super power'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'meal.list' ? 'active' : '' }}">
                                        <a href="{{ route('meal.list') }}" class="link_prop w-100">
                                            <i class="fas fa-utensils nav-icon"></i>
                                            <p>Meal</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                            @canany(['super power', 'publish business users', 'create business users', 'edit business
                                users'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'business_user.list' ? 'active' : '' }}">
                                        <a href="{{ route('business_user.list') }}" class="link_prop w-100">
                                            <i class="fas fa-users nav-icon"></i>
                                            <p>Business Users</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                            @canany(['super power'])
                                <li class="nav-item">
                                    <span
                                        class="nav-link d-flex justify-content-between align-items-center {{ $route == 'analytics.list' ? 'active' : '' }}">
                                        <a href="{{ route('analytics.list') }}" class="link_prop w-100">
                                            <i class="fas fa-chart-line nav-icon"></i>
                                            <p>Web Analytics</p>
                                        </a>
                                    </span>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @canany(['super power'])
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link others_collapse_link">
                            <i class="nav-icon fas fa-plug"></i>
                            <p>External APIs
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <span
                                    class="nav-link d-flex justify-content-between align-items-center {{ $route == 'api.list' ? 'active' : '' }}">
                                    <a href="{{ route('api.list') }}" class="link_prop w-100">
                                        <i class="fas fa-plug nav-icon"></i>
                                        <p>Review API Request</p>
                                    </a>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span
                                    class="nav-link d-flex justify-content-between align-items-center {{ $route == 'api.maps_review' ? 'active' : '' }}">
                                    <a href="{{ route('api.maps_review') }}" class="link_prop w-100">
                                        <i class="fas fa-map-marked-alt nav-icon"></i>
                                        <p>Review API</p>
                                    </a>
                                </span>
                            </li>
                        </ul>
                    </li>
                @endcanany

                @canany(['super power', 'create page_listing_meta', 'edit page_listing_meta', 'publish
                    page_listing_meta'])
                    <li class="nav-item">
                        <span
                            class="nav-link d-flex justify-content-between align-items-center {{ $route == 'page_listing_meta.listing_meta.list' ? 'active' : '' }}">
                            <a href="{{ route('page_listing_meta.listing_meta.list') }}" class="link_prop w-100">
                                <i class="fas fa-tags nav-icon"></i>
                                <p>Pages Listing Meta</p>
                            </a>
                        </span>
                    </li>
                @endcanany

                @canany(['super power'])
                    <li class="nav-item">
                        <span
                            class="nav-link d-flex justify-content-between align-items-center {{ $route == 'account.list' ? 'active' : '' }}">
                            <a href="{{ route('account.list') }}" class="link_prop w-100">
                                <i class="fas fa-user-cog nav-icon"></i>
                                <p>Account Controller</p>
                            </a>
                        </span>
                    </li>
                @endcanany
                @canany(['super power'])
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link role_permission_collapse_link">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Roles & Permissions <i class="right fas fa-angle-left"></i> </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}"
                                    class="nav-link {{ $route == 'roles.index' ? 'active' : '' }}">
                                    <i class="fas fa-user-tag nav-icon"></i>
                                    <p>Manage Roles</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('permissions.index') }}"
                                    class="nav-link {{ $route == 'permissions.index' ? 'active' : '' }}">
                                    <i class="fas fa-user-lock nav-icon"></i>
                                    <p>Manage Permissions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcanany
            </ul>
        </nav>
    </div>
</aside>

<script>
    function initialize_sidebar_collapse() {
        const sidebar_collapsible_elem = document.getElementById('sidebar_collapsible_elem');
        const localstorage_value = localStorage.getItem('sidebar_collapse');
        if (localstorage_value !== null) {
            if (localstorage_value == "true") {
                sidebar_collapsible_elem.setAttribute('data-collapse', 0); // 0 means: collapse
                console.log(localstorage_value)
                document.body.classList.add('sidebar-collapse');
            }
        }
    }
    initialize_sidebar_collapse();
</script>
