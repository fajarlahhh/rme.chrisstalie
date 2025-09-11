@php
    $appHeaderAttr = !empty($appHeaderInverse) ? ' data-bs-theme=dark' : '';
    $appHeaderMenu = !empty($appHeaderMenu) ? $appHeaderMenu : '';
    $appHeaderMegaMenu = !empty($appHeaderMegaMenu) ? $appHeaderMegaMenu : '';
    $appHeaderTopMenu = !empty($appHeaderTopMenu) ? $appHeaderTopMenu : '';
@endphp

<!-- BEGIN #header -->
<div id="header" class="app-header" data-bs-theme="dark">
    <!-- BEGIN navbar-header -->
    <div class="navbar-header">
        <button type="button" class="navbar-desktop-toggler" data-toggle="app-sidebar-minify">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <button type="button" class="navbar-mobile-toggler" data-toggle="app-sidebar-mobile">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="/" class="navbar-brand">
            <span class="navbar-logo"></span>
            <b>{{ strtoupper(config('app.name')) }}</b>&nbsp;RME
        </a>
    </div>

    <!-- BEGIN header-nav -->
    <div class="navbar-nav">
        <div class="navbar-item navbar-user dropdown">
            <a href="#" class="navbar-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                <img src="/assets/img/favicon.png" alt="" />
                <span>
                    <span class="d-none d-md-inline">&nbsp;
                        {{ auth()->user()->pegawai ? auth()->user()->pegawai->nama : auth()->user()->nama }}
                    </span>
                    <b class="caret"></b>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end me-1">
                <a href="/gantipassword" class="dropdown-item">Ganti Password</a>
                <div class="dropdown-divider"></div>
                <a href="javascript:;" class="dropdown-item btn-logout"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log
                    Out</a>
                <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

    </div>
    <!-- END header-nav -->
</div>
<!-- END #header -->
