<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" aria-label="Toggle mobile sidebar"  type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" aria-label="Home" href="{{url('')}}"></a>
    <button aria-label="Toggle nav" class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="nav navbar-nav d-md-down-none mr-auto">

        <li class="nav-item px-3">
            <a class="nav-link" href="#">Dashboard</a>
        </li>
        <li class="nav-item px-3">
            <a class="nav-link" href="#">Users</a>
        </li>
        <li class="nav-item px-3">
            <a class="nav-link" href="#">Settings</a>
        </li>
    </ul>
    <ul class="nav navbar-nav ml-auto">
        @include('layouts.app.header.notifications')
        @include('layouts.app.header.tasks')
        @include('layouts.app.header.user')
        <!--
        <button class="navbar-toggler aside-menu-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        -->
    </ul>
</header>