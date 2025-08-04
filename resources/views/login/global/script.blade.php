<!-- Bootstrap JS -->
<script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<!--notification js -->
<script src="{{ URL::asset('assets/plugins/notifications/js/lobibox.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notifications/js/notifications.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notifications/js/notification-custom-script.js') }}"></script>

<script src="{{ URL::asset('assets/plugins/bootbox/bootbox.js') }}"></script>

<!--Password show & hide js -->
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });
    });
</script>
<!--app JS-->
<script src="{{ URL::asset('assets/js/app.js') }}"></script>

@yield('scripts')
