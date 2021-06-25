@if(isset($messages))
    <li class="nav-item dropdown d-md-down-none">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="icon-envelope-letter"></i><span class="badge badge-pill badge-info">7</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
            <div class="dropdown-header text-center">
                <strong>You have 4 messages</strong>
            </div>
            <a href="#" class="dropdown-item">
                <div class="message">
                    <div class="py-3 mr-3 float-left">
                        <div class="avatar">
                            <img src="img/avatars/7.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                            <span class="avatar-status badge-success"></span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">John Doe</small>
                        <small class="text-muted float-right mt-1">Just now</small>
                    </div>
                    <div class="text-truncate font-weight-bold">
                        <span class="fa fa-exclamation text-danger"></span> Important message</div>
                    <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
                </div>
            </a>
            <a href="#" class="dropdown-item">
                <div class="message">
                    <div class="py-3 mr-3 float-left">
                        <div class="avatar">
                            <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                            <span class="avatar-status badge-warning"></span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">John Doe</small>
                        <small class="text-muted float-right mt-1">5 minutes ago</small>
                    </div>
                    <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
                    <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
                </div>
            </a>
            <a href="#" class="dropdown-item">
                <div class="message">
                    <div class="py-3 mr-3 float-left">
                        <div class="avatar">
                            <img src="img/avatars/5.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                            <span class="avatar-status badge-danger"></span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">John Doe</small>
                        <small class="text-muted float-right mt-1">1:52 PM</small>
                    </div>
                    <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
                    <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
                </div>
            </a>
            <a href="#" class="dropdown-item">
                <div class="message">
                    <div class="py-3 mr-3 float-left">
                        <div class="avatar">
                            <img src="img/avatars/4.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                            <span class="avatar-status badge-info"></span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">John Doe</small>
                        <small class="text-muted float-right mt-1">4:03 PM</small>
                    </div>
                    <div class="text-truncate font-weight-bold">Lorem ipsum dolor sit amet</div>
                    <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt...</div>
                </div>
            </a>
            <a href="#" class="dropdown-item text-center">
                <strong>View all messages</strong>
            </a>
        </div>
    </li>
@endif