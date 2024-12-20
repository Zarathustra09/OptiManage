<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="#">
            <span class="align-middle">OptiManage</span>
        </a>
        <ul class="sidebar-nav">
            <li class="sidebar-header"> Interface </li>
            <li class="sidebar-item {{ request()->routeIs('employee.home') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{route('employee.home')}}">
                    <i class="align-middle" data-feather="monitor"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('employee.task.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{route('employee.task.index')}}">
                    <i class="align-middle" data-feather="clipboard"></i>
                    <span class="align-middle">Tasks</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('admin.teamTask.*') ? 'active' : '' }} ">
                <a class="sidebar-link" href="#">
                    <i class="align-middle" data-feather="users"></i>
                    <span class="align-middle">Team Task</span>
                </a>
            </li>

            <li class="sidebar-header"> Resource Management </li>
            <li class="sidebar-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="#">
                    <i class="align-middle" data-feather="archive"></i>
                    <span class="align-middle">Inventory</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
