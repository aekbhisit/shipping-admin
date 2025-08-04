<!-- Bootstrap JS -->
<script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/validator/js/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<!--notification js -->
<script src="{{ URL::asset('assets/plugins/notifications/js/lobibox.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notifications/js/notifications.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notifications/js/notification-custom-script.js') }}"></script>

<script src="{{ URL::asset('assets/plugins/bootbox/bootbox.js') }}"></script>

<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<!-- datatable re order -->
<link href="{{ URL::asset('assets/plugins/datatable-reorder/rowReorder.dataTables.min.css') }}" rel="stylesheet" />
<script src="{{ URL::asset('assets/plugins/datatable-reorder/dataTables.rowReorder.min.js') }}"></script>

<!-- Datetimepicker js -->
<script src="{{ URL::asset('assets/plugins/datetime-picker/moment.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datetime-picker/bootstrap-datetimepicker.min.js') }}"></script>

<!--app JS-->
<script src="{{ URL::asset('assets/js/app.js') }}"></script>

<!--Select2 js -->
<script src="{{ URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
<!--MutipleSelect js-->
<script src="{{ URL::asset('assets/plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/multipleselect/multi-select.js') }}"></script>

<!--pusher-->
<script src = "https://js.pusher.com/7.2/pusher.min.js" ></script>

<!-- admin js css - Using mix() helper (now fixed) -->
<link rel="stylesheet" href="{{ mix('css/admin.css') }}">
<script src="{{ mix('js/admin.js') }}"></script>
<script src="{{ mix('js/lang.th.js') }}"></script>


@yield('scripts')
