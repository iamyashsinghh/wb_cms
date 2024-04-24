@php
if(Auth::guard('admin')->check()){
$auth_user = Auth::guard('admin')->user();
}

$route = Route::currentRouteName();
@endphp
<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{route('dashboard')}}" class="brand-link text-center">
        <img src="{{asset('wb-logo2.webp')}}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);">
                    <img src="{{$auth_user->profile_image}}" onerror="this.src = null; this.src='{{asset('/images/default-user.png')}}'" class="img-circle elevation-2" alt="User Image" style="width: 43px; height: 43px;">
                </a>
            </div>
            <div class="info text-center py-0">
                <a href="javascript:void(0);" class="d-block">{{$auth_user->name}}</a>
                <span class="text-xs text-bold" style="color: #c2c7d0;">{{$auth_user->email ?: 'N/A'}}</span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link {{$route == "dashboard" ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link venue_collapse_link venue_category_collapse_link">
                        <i class="nav-icon fab fa-app-store"></i>
                        <p>Venue
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center
                            {{$route == "venue.list" || $route == "venue.add" || $route == "venue.edit" ? 'active': ''}}">
                                <a href="{{route('venue.list')}}" class="link_prop w-100">
                                    <i class="fas fa-window-restore nav-icon"></i>
                                    <p>Venue List</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "venue_category.list" ? 'active': ''}}">
                                <a href="{{route('venue_category.list')}}" class="link_prop w-100">
                                    <i class="fas fa-layer-group nav-icon"></i>
                                    <p>Venue Category</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "venue.listing_meta.list" ? 'active': ''}}">
                                <a href="{{route('venue.listing_meta.list')}}" class="link_prop w-100">
                                    <i class="fas fa-ranking-star nav-icon"></i>
                                    <p>Venue Listing Meta</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link venue_collapse_link vendor_collapse_link">
                        <i class="nav-icon fa fa-business-time"></i>
                        <p>Vendor
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center
                            {{$route == "vendor.list" || $route == "vendor.add" || $route == "vendor.edit" ? 'active': ''}}">
                                <a href="{{route('vendor.list')}}" class="link_prop w-100">
                                    <i class="fas fa-icons nav-icon"></i>
                                    <p>Vendor List</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "vendor.listing_meta.list" ? 'active': ''}}">
                                <a href="{{route('vendor.listing_meta.list')}}" class="link_prop w-100">
                                    <i class="fas fa-ranking-star nav-icon"></i>
                                    <p>Vendor Listing Meta</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{route('review.list')}}" class="nav-link {{$route == "review.list" ? 'active' : ''}}">
                        <i class="fas fa-icons nav-icon"></i>
                        <p>Review</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link others_collapse_link">
                        <i class="nav-icon fa fa-circle-info"></i>
                        <p>Others
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "c_num.list" ? 'active': ''}}">
                                <a href="{{route('c_num.list')}}" class="link_prop w-100">
                                    <i class="fas fa-city nav-icon"></i>
                                    <p>Numbers</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "city.list" ? 'active': ''}}">
                                <a href="{{route('city.list')}}" class="link_prop w-100">
                                    <i class="fas fa-city nav-icon"></i>
                                    <p>City</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center
                            {{$route == "location.list" || $route == "location.add_group" || $route == "location.edit_group" ? 'active': ''}}">
                                <a href="{{route('location.list')}}" class="link_prop w-100">
                                    <i class="fas fa-location-dot nav-icon"></i>
                                    <p>Location</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "meal.list" ? 'active': ''}}">
                                <a href="{{route('meal.list')}}" class="link_prop w-100">
                                    <i class="fas fa-bowl-food nav-icon"></i>
                                    <p>Meal</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "business_user.list" ? 'active': ''}}">
                                <a href="{{route('business_user.list')}}" class="link_prop w-100">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Business Users</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "analytics.list" ? 'active': ''}}">
                                <a href="{{route('analytics.list')}}" class="link_prop w-100">
                                    <i class="fas fa-chart-pie nav-icon"></i>
                                    <p>Web Analytics</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link others_collapse_link">
                        <i class="nav-icon fa fa-circle-info"></i>
                        <p>External Apis
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "api.list" ? 'active': ''}}">
                                <a href="{{route('api.list')}}" class="link_prop w-100">
                                    <i class="fas fa-city nav-icon"></i>
                                    <p>Review Api Reqests</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{$route == "api.maps_review" ? 'active': ''}}">
                                <a href="{{route('api.maps_review')}}" class="link_prop w-100">
                                    <i class="fas fa-city nav-icon"></i>
                                    <p>Review Api</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <span class="nav-link d-flex justify-content-between align-items-center {{$route == "page_listing_meta.listing_meta.list" ? 'active': ''}}">
                        <a href="{{route('page_listing_meta.listing_meta.list')}}" class="link_prop w-100">
                            <i class="fas fa-ranking-star nav-icon"></i>
                            <p>Pages Listing Meta</p>
                        </a>
                    </span>
                </li>
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
