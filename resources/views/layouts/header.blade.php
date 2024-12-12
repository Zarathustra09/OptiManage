<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="#">
            <span class="align-middle">OptiManage</span>
        </a>
        <ul class="sidebar-nav">
            <li class="sidebar-header"> Interface </li>
            <li class="sidebar-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('home') }}">
                    <i class="align-middle" data-feather="monitor"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.task.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.task.index') }}">
                    <i class="align-middle" data-feather="clipboard"></i>
                    <span class="align-middle">Tasks</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('admin.task.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.task.index') }}">
                    <i class="align-middle" data-feather="users"></i>
                    <span class="align-middle">Team Task</span>
                </a>
            </li>
            <li class="sidebar-header"> Resource Management </li>
            <li class="sidebar-item {{ request()->routeIs('admin.category.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.category.index') }}">
                    <i class="align-middle" data-feather="layers"></i>
                    <span class="align-middle">Categories</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.inventory.index') }}">
                    <i class="align-middle" data-feather="archive"></i>
                    <span class="align-middle">Inventory</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#">
                    <i class="align-middle" data-feather="file-text"></i>
                    <span class="align-middle">Logs</span>
                </a>
            </li>
            <li class="sidebar-header"> Employee Management </li>
            <li class="sidebar-item {{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.employee.index') }}">
                    <i class="align-middle" data-feather="user-plus"></i>
                    <span class="align-middle">Employees</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-cta">
            <div class="sidebar-cta-content">
                <strong class="d-inline-block mb-2">Welcome to OptiManage</strong>
                <div class="mb-3 text-sm"> We're glad to have you here! Explore the dashboard and discover all its features. </div>
                <div class="d-grid">
                    <a href="{{ route('home') }}" class="btn btn-primary">Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</nav>
