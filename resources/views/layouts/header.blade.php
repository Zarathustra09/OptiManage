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

            <li class="sidebar-item {{ request()->routeIs('admin.task.*') || request()->routeIs('admin.teamTask.*') ? 'active' : '' }}">
                <a href="#tasks" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="edit"></i>
                    <span class="align-middle">Create Task</span>
                    <i class="align-middle float-end" data-feather="chevron-down"></i>
                </a>

                <ul id="tasks" class="sidebar-dropdown list-unstyled collapse {{ request()->routeIs('admin.task.*') || request()->routeIs('admin.teamTask.*') ? 'show' : '' }}"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item {{ request()->routeIs('admin.task.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.task.index') }}">
                            <i class="align-middle" data-feather="user"></i>
                            <span class="align-middle">Individual Task</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('admin.teamTask.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.teamTask.index') }}">
                            <i class="align-middle" data-feather="users"></i>
                            <span class="align-middle">Team Task</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-header"> Resource Management </li>
            <li class="sidebar-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.category.index') }}">
                    <i class="align-middle" data-feather="layers"></i>
                    <span class="align-middle">Categories</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.inventory.index') }}">
                    <i class="align-middle" data-feather="archive"></i>
                    <span class="align-middle">Inventory</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('admin.defect.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.defect.index') }}">
                    <i class="align-middle" data-feather="corner-down-left"></i>
                    <span class="align-middle">Defects</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.log.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.log.index') }}">
                    <i class="align-middle" data-feather="file-text"></i>
                    <span class="align-middle">Logs</span>
                </a>
            </li>

            <li class="sidebar-header"> Employee Management </li>


            <li class="sidebar-item {{ request()->routeIs('admin.area.*') || request()->routeIs('availabilities.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.area.index') }}">
                    <i class="align-middle" data-feather="map-pin"></i>
                    <span class="align-middle">Areas</span>
                </a>
            </li>


            <li class="sidebar-item {{ request()->routeIs('admin.employee.*') || request()->routeIs('availabilities.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.employee.index') }}">
                    <i class="align-middle" data-feather="user-plus"></i>
                    <span class="align-middle">Employees</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('admin.team.*') || request()->routeIs('availabilities.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.team.index') }}">
                    <i class="align-middle" data-feather="sliders"></i>
                    <span class="align-middle">Team</span>
                </a>
            </li>


        </ul>
    </div>
</nav>

<style>
    .sidebar-dropdown {
        padding-left: 1.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Add event listeners for the Tasks dropdown
        const tasksLink = document.querySelector('a[href="#tasks"]');
        const tasksCollapse = document.getElementById('tasks');

        if (tasksLink && tasksCollapse) {
            const arrow = tasksLink.querySelector('i.float-end[data-feather]');

            // Function to update the arrow icon
            const updateArrowIcon = () => {
                if (arrow) {
                    const isShown = tasksCollapse.classList.contains('show');
                    arrow.setAttribute('data-feather', isShown ? 'chevron-up' : 'chevron-down');
                    feather.replace(); // Refresh icons
                }
            };

            // Set initial arrow state
            updateArrowIcon();

            // Update the arrow icon when the collapse state changes
            tasksCollapse.addEventListener('show.bs.collapse', updateArrowIcon);
            tasksCollapse.addEventListener('hide.bs.collapse', updateArrowIcon);
        } else {
            console.error('Tasks link or collapse element not found!');
        }
    });


</script>
