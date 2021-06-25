@if(current_user())
    <li class="nav-item dropdown">
        <a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            {!! current_user()->getAvatar(['class'=>'img-avatar']) !!}
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">
                <strong>Account</strong>
            </div>
            @can('access_admin')
                <a class="dropdown-item" href="{{admin_url()}}"><i class="fa fa-dashboard"></i> Admin</a>
            @endcan
            <a class="dropdown-item" href="#"><i class="fa fa-lock"></i> Logout</a>
        </div>
    </li>
@endif