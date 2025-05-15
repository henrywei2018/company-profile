<header class="header">
    <div class="top-bar bg-primary py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex contact-info">
                        <div class="me-3 text-white">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <span>{{ $companyProfile->phone ?? '+62 123 456 789' }}</span>
                        </div>
                        <div class="me-3 text-white">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <span>{{ $companyProfile->email ?? 'info@cvupl.com' }}</span>
                        </div>
                        <div class="text-white">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            <span>{{ $companyProfile->address ?? 'Jakarta, Indonesia' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-end">
                        <div class="social-icons">
                            @if(isset($companyProfile->facebook) && $companyProfile->facebook)
                                <a href="{{ $companyProfile->facebook }}" class="me-2 text-white" target="_blank"><i class="bi bi-facebook"></i></a>
                            @endif
                            @if(isset($companyProfile->twitter) && $companyProfile->twitter)
                                <a href="{{ $companyProfile->twitter }}" class="me-2 text-white" target="_blank"><i class="bi bi-twitter"></i></a>
                            @endif
                            @if(isset($companyProfile->instagram) && $companyProfile->instagram)
                                <a href="{{ $companyProfile->instagram }}" class="me-2 text-white" target="_blank"><i class="bi bi-instagram"></i></a>
                            @endif
                            @if(isset($companyProfile->linkedin) && $companyProfile->linkedin)
                                <a href="{{ $companyProfile->linkedin }}" class="me-2 text-white" target="_blank"><i class="bi bi-linkedin"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(isset($companyProfile->logo))
                    <img src="{{ $companyProfile->getLogoUrlAttribute() }}" alt="{{ config('app.name') }}" height="50">
                @else
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" height="50">
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('about*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            About
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('about') }}">About Us</a></li>
                            <li><a class="dropdown-item" href="{{ route('about.team') }}">Our Team</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services*') ? 'active' : '' }}" href="{{ route('services.index') }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portfolio*') ? 'active' : '' }}" href="{{ route('portfolio.index') }}">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact*') ? 'active' : '' }}" href="{{ route('contact.index') }}">Contact</a>
                    </li>
                    <li class="nav-item ms-2">
                        @auth
                            <div class="dropdown">
                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(Auth::user()->isAdmin())
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                                    @endif
                                    @if(Auth::user()->isClient())
                                        <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">Client Dashboard</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endif
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</header>