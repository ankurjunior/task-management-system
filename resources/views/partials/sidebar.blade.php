<style>
    /* ===================================
   COLLAPSIBLE SIDEBAR
    ================================== */

    .sidebar {
        width: 280px;
        transition: all .3s ease;
        display: flex;
        flex-direction: column;
    }

    .main-wrapper {
        margin-left: 280px;
        width: calc(100% - 280px);
        transition: all .3s ease;
    }

    /* collapsed */

    .sidebar.collapsed {
        width: 80px;
        overflow: visible;
    }

    .sidebar.collapsed .logo-text {
        display: none;
    }

    .sidebar.collapsed .menu-heading {
        display: none;
    }

    .sidebar.collapsed .sidebar-menu a span {
        display: none;
    }

    .sidebar.collapsed .sidebar-logout button span {
        display: none;
    }

    .sidebar.collapsed .sidebar-menu a {
        justify-content: center;
    }

    .sidebar.collapsed .sidebar-logout button {
        justify-content: center;
    }

    .sidebar.collapsed .sidebar-menu a i {
        margin-right: 0;
        font-size: 18px;
    }

    .sidebar.collapsed .sidebar-logout button i {
        margin-right: 0;
        font-size: 18px;
    }

    .sidebar.collapsed .sidebar-menu a {
        position: relative;
    }

    .sidebar.collapsed .sidebar-logout button {
        position: relative;
    }

    .sidebar.collapsed .sidebar-menu a::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(100% + 12px);
        top: 50%;
        z-index: 1050;
        padding: 7px 10px;
        border-radius: 6px;
        background: #111827;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-50%) translateX(-4px);
        transition: opacity .2s ease, transform .2s ease;
    }

    .sidebar.collapsed .sidebar-menu a:hover::after,
    .sidebar.collapsed .sidebar-menu a:focus-visible::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
    }

    .sidebar.collapsed .sidebar-logout button::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(100% + 12px);
        top: 50%;
        z-index: 1050;
        padding: 7px 10px;
        border-radius: 6px;
        background: #111827;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-50%) translateX(-4px);
        transition: opacity .2s ease, transform .2s ease;
    }

    .sidebar.collapsed .sidebar-logout button:hover::after,
    .sidebar.collapsed .sidebar-logout button:focus-visible::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
    }

    .sidebar-menu {
        flex: 1 1 auto;
    }

    .sidebar-logout {
        margin-top: auto;
        padding: 15px;
        border-top: 1px solid var(--border);
        background: #fff;
    }

    .sidebar-logout button {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #dc2626;
        font: inherit;
        text-align: left;
    }

    .sidebar-logout button:hover,
    .sidebar-logout button:focus-visible {
        background: #fef2f2;
        outline: 0;
    }

    /* main resize */

    .main-wrapper.expanded {
        margin-left: 80px;
        width: calc(100% - 80px);
    }

    /* smooth */

    .sidebar-menu a,
    .main-wrapper,
    .sidebar {
        transition: all .3s ease;
    }
</style>
<aside class="sidebar">

    <div class="sidebar-logo">

        <div class="logo-icon">
            <img
                src="/images/logo.png"
                width="50"
                alt="Logo">
        </div>

        <div class="logo-text">
            <h4>{{ __('login.app_name') }}</h4>
        </div>

    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dashboard') }}"
                class="{{ request()->is('dashboard') ? 'active' : '' }}"
                data-tooltip="Dashboard" aria-label="Dashboard">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if(auth()->check() && auth()->user()->role_id !== 1)
        <li>
            <a href="{{ route('tasks.index') }}" class="{{ request()->is('tasks') ? 'active' : '' }}" data-tooltip="My Tasks" aria-label="My Tasks">
                <i class="fa-solid fa-user-check"></i>
                <span>Tasks</span>
            </a>
        </li>

        @if(auth()->user()->can_assign_task == 1)
        <li>
            <a href="{{ route('tasks.index') }}" data-tooltip="Assigned By Me" aria-label="Assigned By Me">
                <i class="fa-solid fa-share"></i>
                <span>Assigned By Me</span>
            </a>
        </li>
        @endif

        @endif
        @if(auth()->check() && auth()->user()->role_id === 1)
        <li>
            <a href="{{ route('users.index') }}" data-tooltip="Users" aria-label="Users">
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        @endif

        @if(0)
        <li>
            <a href="#" data-tooltip="Hierarchy" aria-label="Hierarchy">
                <i class="fa-solid fa-sitemap"></i>
                <span>Hierarchy</span>
            </a>
        </li>

        <li>
            <a href="#" data-tooltip="Roles" aria-label="Roles">
                <i class="fa-solid fa-user-shield"></i>
                <span>Roles</span>
            </a>
        </li>
        @endif

        <li>
            <a href="#" data-tooltip="Reports" aria-label="Reports">
                <i class="fa-solid fa-chart-column"></i>
                <span>Reports</span>
            </a>
        </li>
        <li>
            <a
                href="#changePasswordModal"
                data-bs-toggle="modal"
                data-bs-target="#changePasswordModal"
                data-tooltip="Change Password"
                aria-label="Change Password"
                role="button">
                <i class="fa-solid fa-key"></i>
                <span>Change Password</span>
            </a>
        </li>

    </ul>

    <form class="sidebar-logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" data-tooltip="Logout" aria-label="Logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </button>
    </form>

</aside>
