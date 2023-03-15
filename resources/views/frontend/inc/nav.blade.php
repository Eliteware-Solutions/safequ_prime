@if (isset($header_show) && $header_show)
    @if (get_setting('topbar_banner') != null)
        <div class="position-relative top-banner removable-session z-1035 d-none" data-key="top-banner"
            data-value="removed">
            <a href="{{ get_setting('topbar_banner_link') }}" class="d-block text-reset">
                <img src="{{ uploaded_asset(get_setting('topbar_banner')) }}"
                    class="w-100 mw-100 h-50px h-lg-auto img-fit">
            </a>
            <button class="btn text-white absolute-top-right set-session" data-key="top-banner" data-value="removed"
                data-toggle="remove-parent" data-parent=".top-banner">
                <i class="la la-close la-2x"></i>
            </button>
        </div>
    @endif

    <header class="header {{ $header2 ? 'header2' : '' }}">
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" alt="SafeQu Logo">
                </a>
                <div class="flex-acenter-jbtw">
                    @auth
                        <div class="cart-icon mr-3 crt-sm">
                            <a href="{{ route('all-notifications') }}">
                                <i class="fad fa-bell-on fsize20 mr-2"></i>
                                @if (count(Auth::user()->unreadNotifications) > 0)
                                    <span>{{ count(Auth::user()->unreadNotifications) }}</span>
                                @endif
                            </a>
                        </div>
                    @endauth
                    <div class="nav-icons d-flex align-items-center pr-4 view-sm">
                        <a href="{{ route('cart') }}" aria-label="Cart" title="Cart"
                            class="cart-icon-org position-relative">
                            <img src="{{ static_asset('assets/img/new-design/btn-cart-primary.svg') }}"
                                class="injectable rounded-circle trnsn-300ms" alt="Cart Icon">
                            <span class="rounded-circle trnsn-300ms cart-item-count" style="display: none;"></span>
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse disp-none-lg" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{ route('shop.visit', session()->get('shop_slug')) }}">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile') }}">Account</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('purchase_history.index') }}">My Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('wallet.index') }}">Wallet</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                            </li>
                        @else
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.login') }}">Login</a>
                            </li>
                        @endauth
                    </ul>
                    @auth
                        <div class="cart-icon pl-4 pr-3">
                            <a href="{{ route('all-notifications') }}">
                                <i class="fad fa-bell-on fsize20"></i>
                                @if (count(Auth::user()->unreadNotifications) > 0)
                                    <span>{{ count(Auth::user()->unreadNotifications) }}</span>
                                @endif
                            </a>
                        </div>
                    @endauth
                    <div class="nav-icons d-flex align-items-center px-2">
                        <a href="{{ route('cart') }}" aria-label="Cart" title="Cart"
                            class="cart-icon-org position-relative">
                            <img src="{{ static_asset('assets/img/new-design/btn-cart-primary.svg') }}"
                                class="injectable rounded-circle trnsn-300ms" alt="Cart Icon">
                            <span class="rounded-circle trnsn-300ms cart-item-count" style="display: none;"></span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="pwaPopup" id="pwaPopup" style="display: none;">
            <div class="container flex-acenter-jbtw">
                <i class="far fa-times text-white fsize20 lh-1 p-2"
                    onclick="hideInstallPromotion(); localStorage.setItem('lastDismiss', new Date().getDate());"></i>
                <p class="mb-0 fw600 text-white px-2">SafeQu<br>
                    <span class="text-white fsize12" id="msg">Get our free app. It won't takeup space on your
                        phone.</span>
                </p>
                <button class="btn primary-btn btn-small btn-round py-1 px-3 fsize13" id="installPWA">Install</button>
            </div>
        </div>
    </header>

    <div class="sideNav flex-astart-jbtw">
        <div class="backdropDiv"></div>

        <div class="nav-menu">
            <div class="text-right close-sidenav">
                <i class="fad fa-times fsize18 px-3 py-1"></i>
            </div>

            <div class="user-profile flex-acenter-jstart pt-3 pb-4">

                @if (Auth::user())
                    <a href="{{ route('profile') }}">
                        <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';">
                    </a>
                @else
                    <img src="{{ static_asset('assets/img/avatar-default.webp') }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';">
                @endif

                @auth
                    <div class="pl-3">
                        <p class="fw500 title-txt mb-1">
                            {{ Auth::user()->name == 'Guest User' ? Auth::user()->phone : Auth::user()->name }}</p>
                        <!-- <p class="fsize13 body-txt mb-0">Community: Lodha Park</p> -->
                    </div>
                @else
                    <a href="{{ route('user.login') }}" class="w-100">
                        <div class="pl-3 p-2 w-100">
                            <p class="fw500 title-txt mb-0"><i class="fad fa-sign-in"></i> &nbsp; Login</p>
                        </div>
                    </a>
                @endauth
            </div>

            <ul class="side-nav-links bt-1 mb-0 py-2">
                @auth
                    <a href="{{ route('shop.visit', session()->get('shop_slug')) }}">
                        <li class="p-2 mb-2"><i class="fad fa-box-full"></i> Products</li>
                    </a>
                    <a href="{{ route('purchase_history.index') }}">
                        <li class="p-2 mb-2"><i class="fad fa-bags-shopping"></i> My Orders</li>
                    </a>
                    <a href="{{ route('wallet.index') }}">
                        <li class="p-2 mb-2"><i class="fad fa-wallet"></i> Wallet</li>
                    </a>
                    <a href="{{ route('profile') }}">
                        <li class="p-2 mb-2"><i class="fad fa-user"></i> Account</li>
                    </a>
                    <a href="{{ route('all-notifications') }}">
                        <li class="p-2 mb-2">
                            <div class="cart-icon">
                                <i class="fad fa-bell-on"></i>
                                @if (count(Auth::user()->unreadNotifications) > 0)
                                    <span>{{ count(Auth::user()->unreadNotifications) }}</span>
                                @endif Notifications
                            </div>
                        </li>
                    </a>
                    <a href="{{ route('cart') }}">
                        <li class="p-2 mb-2">
                            <div class="cart-icon">
                                <i class="fad fa-shopping-cart mr-2"></i> <span class="cart-item-count"
                                    style="display: none;"></span>
                                Cart
                            </div>
                        </li>
                    </a>
                    @if (auth()->user()->joined_community_id > 0)
                        <a href="javascript:void(0)"
                            onclick="referFriend('{{ route('referral.registration', auth()->user()->referral_key) }}', '{{ auth()->user()->name }}','{{ single_price(env('REFERRAL_BONUS_AMOUNT')) }}')">
                            <li class="p-2 mb-2"><i class="fad fa-user-plus"></i> Refer & Earn</li>
                        </a>
                    @endif
                @else
                    <a href="{{ route('home') }}">
                        <li class="p-2 mb-2"><i class="fad fa-home"></i> Home</li>
                    </a>
                @endauth
            </ul>

            @auth
                <div class="bottom bt-1 pt-2">
                    <ul class="mb-0">
                        <a href="{{ route('logout') }}">
                            <li class="p-2 mb-1"><i class="fad fa-sign-out"></i> Logout</li>
                        </a>
                    </ul>
                </div>
            @endauth
            <br />
            <div class="pwaPopup" style="display: none;">
                <div class="container flex-acenter-jbtw">
                    <p class="mb-0 fw600 text-white px-2">SafeQu<br>
                        <span class="text-white fsize12" id="msg">Install our free app</span>
                    </p>
                    <button class="btn primary-btn btn-small btn-round py-1 px-3 fsize13" id="installPWAMenu">Install
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
