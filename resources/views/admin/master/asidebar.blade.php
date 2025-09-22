        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
                <img src="{{ asset('assets/img/AdminLTELogo.png') }}" alt="Logo"
                    class="brand-image img-circle elevation-3 mr-2"
                    style="opacity: 0.8; width: 35px; height: 35px; flex-shrink: 0;">
                <span class="brand-text font-weight-light text-wrap" style="flex: 1; white-space: normal;">
                    {{ config('app.name', 'Laravel App') }}
                </span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                    <div class="image">
                        <img src="{{ asset('assets/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                            alt="User Image">
                    </div>

                    <div class="info d-flex flex-column">
                        <a href="{{ route('profile.edit') }}" class="d-block text-white fw-bolder">
                            {{ Str::ucfirst(auth()->user()->name) }}
                        </a>

                        <!-- Logout Link -->
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-sm text-white btn btn-xs btn-danger rounded">
                                {{ __('Logout') }}
                            </a>
                        </form>
                    </div>
                </div>


                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                            aria-label="Search">
                        {{-- <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div> --}}
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        {{-- Investment --}}
                        @php
                            $investmentOpen = request()->routeIs('investment*');
                        @endphp
                        <li class="nav-item {{ $investmentOpen ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $investmentOpen ? 'active' : '' }}">
                                <i class="nav-icon fas fa-coins"></i>
                                <p>
                                    Investment
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('investment') }}"
                                        class="nav-link {{ request()->routeIs('investment') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Invest</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('investment.partner') }}"
                                        class="nav-link {{ request()->routeIs('investment.partner') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Investment Partner</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Job --}}
                        <li class="nav-item">
                            <a href="{{ route('job.earning') }}"
                                class="nav-link {{ request()->routeIs('job.earning') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>Job</p>
                            </a>
                        </li>

                        {{-- Company --}}
                        <li class="nav-item">
                            <a href="{{ route('company') }}"
                                class="nav-link {{ request()->routeIs('company') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Company</p>
                            </a>
                        </li>

                    </ul>

                </nav>
            </div>
        </aside>
