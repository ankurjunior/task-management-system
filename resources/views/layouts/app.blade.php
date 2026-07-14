<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Organization TMS')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fontawesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

    <div class="app-wrapper">

        @include('partials.sidebar')

        <div class="main-wrapper">

            @include('partials.header')

            <main class="content-wrapper">
                @yield('content')
            </main>

            @include('partials.footer')

        </div>

    </div>

    @if(auth()->check() && auth()->user()->role_id !== 1 )
    @if(auth()->user()->designation_can_assign_task == 1)
    @include('partials.task-modal')

    <button
        type="button"
        class="floating-task-button"
        aria-label="Add New Task"
        data-bs-toggle="modal"
        data-bs-target="#addTaskModal">
        <i class="fa fa-plus"></i>
        <span>Add New Task</span>
    </button>
    @endif
    @endif

    @auth
    @include('partials.change-password-modal')
    @endauth

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.3.3/css/dataTables.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.bootstrap5.js"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $(document).ready(function() {

            $("#sidebarToggle").click(function() {

                $(".sidebar").toggleClass("collapsed");

                $(".main-wrapper").toggleClass("expanded");

            });

        });
    </script>

    @stack('scripts')

</body>

</html>
