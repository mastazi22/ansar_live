<!DOCTYPE html>
<html>
<head>
    @include('template.resource')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
            $(window).load(function () {
                var text = $.trim($(".module-menu-container > ul.module-menu>li.active>a").text());
                $(".module-menu-container > .module-small-header > .header-content").text(text ? text : "ERP");
            })
            $('#national_id_no,#birth_certificate_no,#mobile_no_self').keypress(function (e) {
                var code = e.keyCode ? e.keyCode : e.which;
                if ((code >= 47 && code <= 57) || code == 8) ;
                else e.preventDefault();
            });
            $(".module-small-header").on('click', function (e) {
                $(".module-menu:not('.still')").slideToggle(200, function () {
                    $(this).addClass('still');
                    $(".module-small-header>.icon>i").addClass('fa-angle-up').removeClass('fa-angle-down')
                })
                $(".module-menu.still").slideToggle(200, function () {
                    $(this).removeClass('still');
                    $(".module-small-header>.icon>i").addClass('fa-angle-down').removeClass('fa-angle-up')
                })
            })
            $(window).resize(function () {
                if ($(this).width() > 864) {
                    $(".module-menu").removeAttr('style')
                    $(".module-menu").removeClass('still')
                    $(".module-small-header>.icon>i").addClass('fa-angle-down').removeClass('fa-angle-up')
                }
            })
        });

        GlobalApp.controller('MenuController', function ($scope, $rootScope) {
            $scope.menu = [];
            var permission = '{{auth()->user()->userPermission->permission_list?auth()->user()->userPermission->permission_list:""}}'
            var p_type = parseInt('{{auth()->user()->userPermission->permission_type}}')
            if (permission) $scope.menu = JSON.parse(permission.replace(/&quot;/g, '"'))
            //alert($scope.menu.indexOf('reduce_guard_strength')>=0||p_type==1)
            $scope.checkMenu = function (value) {
                return $scope.menu.indexOf(value) >= 0 || p_type == 1
            }
        })
    </script>
    <script src="{{asset('dist/js/app.min.js')}}" type="text/javascript"></script>


</head>
<body class="skin-blue sidebar-mini " ng-app="GlobalApp"><!-- ./wrapper -->
<div class="wrapper" ng-cloak>
    <calender></calender>
</div>
</body>
</html>

