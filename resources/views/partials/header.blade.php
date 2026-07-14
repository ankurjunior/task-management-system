@php
    $headerNotifications = collect();
    $headerUnreadNotificationCount = 0;

    if (auth()->check()) {
        $headerUnreadNotificationCount = \App\Models\Notification::query()
            ->where('user_id', auth()->id())
            ->unread()
            ->count();

        $headerNotifications = \App\Models\Notification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->unread()
            ->limit(5)
            ->get();
    }
@endphp

<header class="top-header">

    <div class="d-flex align-items-center">

        <button
            class="btn btn-light me-3"
            id="sidebarToggle">
            <i class="fa fa-bars"></i>
        </button>

        <div>
            <h5 class="mb-0">
                @yield('page-title','Dashboard')
            </h5>

            <small class="text-muted">
             {{ __('login.h1') }}
            </small>
        </div>
    </div>

    <div class="header-right">

        @if(auth()->check() && auth()->user()->role_id !== 1)
        <div class="dropdown">
            <button
                class="btn btn-light position-relative"
                type="button"
                id="notificationDropdown"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                aria-expanded="false"
                aria-label="Show notifications">
                <i class="fa fa-bell"></i>
                @if($headerUnreadNotificationCount > 0)
                <span class="notification-badge">
                    {{ $headerUnreadNotificationCount > 99 ? '99+' : $headerUnreadNotificationCount }}
                </span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown">
                <div class="notification-menu-header">
                    <strong>Notifications</strong>
                    <span>{{ $headerUnreadNotificationCount }} unread</span>
                </div>

                @forelse($headerNotifications as $notification)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}" class="notification-item-form">
                        @csrf
                        <button
                            type="submit"
                            class="notification-item {{ $notification->read_at ? '' : 'notification-item-unread' }}">
                            <span class="notification-item-icon">
                                <i class="fa fa-bell"></i>
                            </span>
                            <span class="notification-item-body">
                                <strong>{{ $notification->title }}</strong>
                                @if($notification->message)
                                <small>{{ $notification->message }}</small>
                                @endif
                                <em>{{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}</em>
                            </span>
                        </button>
                    </form>
                @empty
                    <div class="notification-empty">
                        No notifications yet.
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        @auth
        <button
            type="button"
            class="btn btn-light"
            data-bs-toggle="modal"
            data-bs-target="#changePasswordModal"
            title="Change Password">
            <i class="fa fa-key"></i>
        </button>
        @endauth

        <div class="user-box">

            <!-- <div class="avatar">
                AG
            </div> -->

            <div>

                <div class="fw-semibold">
                    {{ auth()->user()->name ?? 'Admin User' }}
                </div>

                <small class="text-muted">
                    {{ auth()->user()->designation_name ?? date('Y-m-d') }}
                </small>
            </div>
        </div>
    </div>

</header>
