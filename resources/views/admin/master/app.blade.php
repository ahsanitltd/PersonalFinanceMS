<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="bearer-token" content="{{ getUserToken() }}">
    <meta name="app-locale" content="{{ app()->getLocale() }}">
    <meta name="app-env" content="{{ app()->environment() }}">


    <title>{{ config('app.name', 'Laravel App') }}</title>

    {{-- tab icon --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">


    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('assets/css/ionicons.min.css') }}">

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('assets/css/OverlayScrollbars.min.css') }}">

    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">

    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/css/icheck-bootstrap.min.css') }}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-colorpicker.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap4.min.css') }}">

    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('assets/css/summernote-bs4.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.all.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.min.css') }}">

    <style>
        .table thead th {
            vertical-align: top;
        }

        .highlight-row {
            background-color: #ffff99 !important;
            /* light yellow highlight */
            transition: background-color 2s ease;
        }

        /* Optional: fade out the highlight after a few seconds */
        .highlight-row.fade-out {
            background-color: transparent;
            transition: background-color 2s ease;
        }

        /* Sidebar search suggestion dropdown */
        #sidebar-search-suggestions {
            position: absolute;
            background: #343a40;
            /* dark sidebar bg */
            border: 1px solid #4b545c;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 180px;
            overflow-y: auto;
            width: 100%;
            z-index: 1050;
            color: #c2c7d0;
            font-size: 14px;
            box-shadow: 0 2px 5px rgb(0 0 0 / 0.2);
        }

        #sidebar-search-suggestions li {
            padding: 7px 12px;
            cursor: pointer;
        }

        #sidebar-search-suggestions li:hover {
            background-color: #1d2124;
            color: #fff;
        }

        #sidebar-search-suggestions .no-results {
            padding: 7px 12px;
            color: #6c757d;
            cursor: default;
        }

        mark {
            background: #ffc107;
            color: #212529;
            padding: 0 2px;
            border-radius: 2px;
        }
    </style>

    @yield('custom-css')
</head>

{{-- sidebar-collapse --}}

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="assets/img/AdminLTELogo.png" alt="AdminLTELogo" height="60"
                    width="60">
            </div>

            <div id="loader"
                style="
                    display: none;
                    position: fixed;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(255, 255, 255, 0.7);
                    z-index: 9999;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                ">
                <img src="assets/img/AdminLTELogo.png" alt="Loading..." style="width: 100px; height: 100px;" />
            </div>


            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        @include('admin.master.asidebar')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                {{ ucfirst(strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', ' ', request()->segment(1))))) }}
                                data</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    {{ ucfirst(strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', ' ', request()->segment(1))))) }}

                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @yield('main-content')
            </section>
        </div>
        <footer class="main-footer text-center">
            <strong>
                {{ date('Y') }} Copyright
                {{-- &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.
                All rights reserved. --}}
            </strong>
        </footer>
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>


    {{-- audio  --}}
    <!-- Audio Elements -->
    <audio id="successAudio" src="{{ asset('assets/music/success.mp3') }}"></audio>
    <audio id="errorAudio" src="{{ asset('assets/music/error.mp3') }}"></audio>
    <audio id="warningAudio" src="{{ asset('assets/music/warning.mp3') }}"></audio>
    <audio id="infoAudio" src="{{ asset('assets/music/info.mp3') }}"></audio>

    {{-- audio ends  --}}

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('assets/js/adminlte.min.js') }}"></script>

    <!-- daterangepicker -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>

    <!-- bootstrap color picker -->
    <script src="{{ asset('assets/js/bootstrap-colorpicker.min.js') }}"></script>

    <script src="{{ asset('assets/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script src="{{ asset('assets/js/summernote-bs4.min.js') }}"></script>

    <script src="{{ asset('assets/js/jquery.overlayScrollbars.min.js') }}"></script>


    <!-- SheetJS (Excel, Word) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- jsPDF AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    {{-- Common ajax data for CRUD operation --}}
    <script src="{{ asset('assets/js/ajax-jquery-crud.js') }}"></script>
    <script src="{{ asset('assets/js/ajax-select2.js') }}"></script>


    {{-- menusbar search js  --}}
    <script>
        $(function() {
            const $input = $('.form-control-sidebar');
            let $suggestions;

            function createSuggestionBox() {
                if (!$suggestions) {
                    $suggestions = $('<ul id="sidebar-search-suggestions" class="list-unstyled"></ul>');
                    $input.parent().after($suggestions);
                }
            }

            function removeSuggestionBox() {
                if ($suggestions) {
                    $suggestions.remove();
                    $suggestions = null;
                }
            }

            function getMenuData() {
                const data = [];
                $('.nav-sidebar > li').each(function() {
                    const $parent = $(this);
                    const parentText = $parent.find('> a > p').text().trim();
                    const parentHref = $parent.find('> a').attr('href') || '#';
                    data.push({
                        type: 'parent',
                        text: parentText,
                        href: parentHref
                    });
                    $parent.find('ul li').each(function() {
                        const $child = $(this);
                        const childText = $child.text().trim();
                        const childHref = $child.find('a').attr('href') || '#';
                        data.push({
                            type: 'child',
                            text: childText,
                            href: childHref
                        });
                    });
                });
                return data;
            }

            function highlight(text, query) {
                const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                return text.replace(regex, '<mark>$1</mark>');
            }

            $input.on('input', function() {
                const q = $(this).val().toLowerCase().trim();
                removeSuggestionBox();

                if (!q) return;

                const menuData = getMenuData();
                const matches = menuData.filter(item => item.text.toLowerCase().includes(q));

                createSuggestionBox();
                $suggestions.empty();

                if (matches.length === 0) {
                    $suggestions.append('<li class="no-results">No results found</li>');
                    return;
                }

                matches.forEach(item => {
                    const $li = $('<li>').html(highlight(item.text, q));
                    if (item.href && item.href !== '#') {
                        $li.css('cursor', 'pointer').on('click', () => {
                            window.location.href = item.href;
                        });
                    }
                    $suggestions.append($li);
                });
            });

            // Close suggestions on outside click
            $(document).on('click', function(e) {
                if (!$input.is(e.target) && !$suggestions?.is(e.target) && !$suggestions?.has(e.target)
                    .length) {
                    removeSuggestionBox();
                }
            });
        });
    </script>

    @yield('custom-js')

    @vite([])
</body>

</html>
