
<!DOCTYPE html>
<html lang="en" ng-app="SimpleInjection">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>Simple Injection| Admin Dashboard</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #6 for statistics, charts, recent events and reports" name="description" />
        <meta content="" name="author" />
        <base href="<?= url('/') . '/' ?>">
        <!-- BEGIN LAYOUT FIRST STYLES -->
        <link href="//fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
        <!-- END LAYOUT FIRST STYLES -->
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="resources/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="resources/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="resources/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="resources/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        
        <link href="resources/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="resources/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="resources/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />,
        <link href="resources/assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="resources/assets/global/plugins/bootstrap-sweetalert/sweetalert.css">
        <link rel="stylesheet" href="resources/assets/global/plugins/typeahead/typeahead.css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="resources/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="resources/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="resources/assets/layouts/layout6/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="resources/assets/layouts/layout6/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> </head>
        <script> var BASEURL = '<?php echo url('/') ?>' + '/'; </script>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        
        <!-- END HEAD -->
        <style type="text/css">
            .page-content { margin :5px 0 }
            body{
                background: #56a843;
            }
            .page-header{
                background: #56a843;
                border-bottom-color: #56a843;
            }
            .page-header .topbar-actions .btn-group-img .btn {
                padding: 0;
                border-radius: 0;
                line-height: normal;
                background: #56a843;
                height: 60px;
                border-left: 1px solid #56a843;
            }
            .bg-green-turquoise {
                background: #56a843!important;
            }
            .page-sidebar .page-sidebar-menu>li>a>i, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu>li>a>i {

                color: #56a843;
            }
            .page-sidebar .page-sidebar-menu li.active>a>i, .page-sidebar-closed.page-sidebar-fixed .page-sidebar:hover .page-sidebar-menu li.active>a>i{
                
                color: #56a843;
            }
        </style>
        <body class="" ng-controller="mainController">
        <!-- BEGIN HEADER -->
        <header class="page-header">
            <nav class="navbar" role="navigation">
                <div class="container-fluid">
                    <div class="havbar-header">
                        <!-- BEGIN LOGO -->
                        <a href="manage/dashboard">
                            <img src="public/images/simple_injection_w.png" alt="logo" class="logo-default" style="height: 65px; margin: 3px 49px 0;" />
                         </a>
                        <!-- END LOGO -->
                        <!-- BEGIN TOPBAR ACTIONS -->
                        <div class="topbar-actions">
                            <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
                            <!-- <span style="color: white">Hey, {{ Auth::guard('admin')->user()->name }}</span> -->
                            <!-- END HEADER SEARCH BOX -->
                            <!-- BEGIN USER PROFILE -->
                            <div class="btn-group-img btn-group">
                                <button type="button" style="color: white;margin-right: 30px; font-size: 18px;" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-user"></i> Hey, {{ Auth::guard('admin')->user()->name }} &nbsp;&nbsp;&nbsp;<i class="fa fa-angle-down"> </i>
                                </button>
                                <ul class="dropdown-menu-v2" role="menu">
                                    <li>
                                        <a href="manage/my-profile">  <i class="icon-user"></i> My Profile  </a>
                                    </li>
                                    <li>  <a href="javascript:;" onclick="window.location = '<?php echo url('/') . Config('constant.paths.BASEURL_MANAGE') .'/manage/'. 'logout' ?>'"> <i class="icon-key"></i> Logout</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- END USER PROFILE -->
                        </div>
                        <!-- END TOPBAR ACTIONS -->
                    </div>
                </div>
                <!--/container-->
            </nav>
        </header>
        <!-- END HEADER -->
        <!-- BEGIN CONTAINER -->
        <div class="container-fluid">
            <div class="page-content page-content-popup">
                <div class="page-content-fixed-header">
                    <!-- BEGIN BREADCRUMBS -->
                    <!-- <ul class="page-breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                    </ul> -->
                    <!-- END BREADCRUMBS -->
                    <div class="content-header-menu">
                        <!-- BEGIN DROPDOWN AJAX MENU -->
                        <div class="dropdown-ajax-menu btn-group">
                           <ul class="dropdown-menu-v2">
                                <li>
                                    <a href="start.html">Application</a>
                                </li>
                                <li>
                                    <a href="start.html">Reports</a>
                                </li>
                                <li>
                                    <a href="start.html">Templates</a>
                                </li>
                                <li>
                                    <a href="start.html">Settings</a>
                                </li>
                            </ul>
                        </div>
                        <!-- END DROPDOWN AJAX MENU -->
                        <!-- BEGIN MENU TOGGLER -->
                        <button type="button" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="toggle-icon">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>
                        </button>
                        <!-- END MENU TOGGLER -->
                    </div>
                </div>
                <div class="page-sidebar-wrapper">
                    <!-- BEGIN SIDEBAR -->
                    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                    <div class="page-sidebar navbar-collapse collapse">
                        <!-- BEGIN SIDEBAR MENU -->
                        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
                        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
                        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
                        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
                        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
                        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
                        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                            <li class="nav-item" ng-class="{'active': isActive(['manage/dashboard'])}">
                                <a href="manage/dashboard" class="nav-link nav-toggle" >
                                    <i class="icon-home"></i>
                                    <span class="title">Dashboard</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item" ng-class="{'active': isActive(['manage/users'])}">
                                <a href="manage/users" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Users</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item" ng-class="{'active': isActive(['manage/devices'])}">
                                <a href="manage/devices" class="nav-link nav-toggle">
                                    <i class="icon-layers"></i>
                                    <span class="title">Devices</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item" ng-class="{'active': isActive(['manage/feedback'])}">
                                <a href="manage/feedback" class="nav-link nav-toggle">
                                    <i class="icon-call-end"></i>
                                    <span class="title">Feedback</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                        </ul>
                        <!-- END SIDEBAR MENU -->
                    </div>
                    <!-- END SIDEBAR -->
                </div>

                <div class="page-fixed-main-content">
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="page-content" ng-view>
                           
                    </div>
                  <!-- END PAGE BASE CONTENT -->
                </div>
                
                <!-- BEGIN FOOTER -->
                <p class="copyright-v2"> {{date('Y')}} &copy; By Simple Injection.
                   
                </p>
               <a href="#index" class="go2top">
                    <i class="icon-arrow-up"></i>
                </a>
                <!-- END FOOTER -->
            </div>
        </div>
        <!-- END CONTAINER -->

        <!-- BEGIN CORE PLUGINS -->
        <script src="resources/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="resources/assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="resources/assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js"></script>
        
        <script src="resources/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="resources/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="resources/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="resources/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        
        <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
        <script src="https://www.amcharts.com/lib/3/serial.js"></script>
        <script src="https://www.amcharts.com/lib/3/plugins/dataloader/dataloader.min.js"></script>
        <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
        <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
        <script src="https://www.amcharts.com/lib/3/themes/patterns.js"></script>

        <script type="text/javascript" src="resources/assets/global/plugins/typeahead/handlebars.min.js"></script>
        <script type="text/javascript" src="resources/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
        <script src="resources/assets/pages/scripts/components-typeahead.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="resources/assets/global/plugins/select2/js/select2.full.min.js"></script>
        <script src="resources/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="resources/assets/layouts/layout6/scripts/layout.min.js" type="text/javascript"></script>
        <script src="resources/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="resources/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        <script src="https://www.gstatic.com/firebasejs/5.3.0/firebase.js"></script>
        <script>
          // Initialize Firebase
          var config = {
            apiKey: "AIzaSyA-5ozUsTS-Lxk_l2ZWXqsPMWVQc57UB2g",
            authDomain: "simpleinjection.firebaseapp.com",
            databaseURL: "https://simpleinjection.firebaseio.com",
            projectId: "simpleinjection",
            storageBucket: "simpleinjection.appspot.com",
            messagingSenderId: "607108522936"
          };
          firebase.initializeApp(config);
        </script>
         <script src="resources/assets/js/angular.min.js" type="text/javascript"></script>
        <script src="resources/assets/js/angular-route.js" type="text/javascript"></script>
        <script src="resources/assets/js/ui-bootstrap-tpls-1.3.2.js" type="text/javascript"></script>
        <script src="resources/assets/js/script.js"></script>
    </body>

</html>