<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="/assets/ico/favicon.png">

        <title>eLinet | @yield('title')</title>

        <!-- Bootstrap core CSS -->
        <link href="/assets/css/bootstrap.css" rel="stylesheet">
        <!-- Font Awesome CSS -->
        <link href="/assets/css/font-awesome/font-awesome.min.css" rel="stylesheet">
        <!-- Global CSS -->
        <link href="/assets/css/site/global.style.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="../../assets/js/html5shiv.js"></script>
            <script src="../../assets/js/respond.min.js"></script>
        <![endif]-->
        @yield('internalCss')
    </head>

    <body>

        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="/home" data-toggle="tooltip" title="Home" style="font-size: 20px;">
                                <i class="icon-home"></i>
                            </a>
                        </li>
                        <li>
                            <a href="/planner" data-toggle="tooltip" title="Planner" style="font-size: 20px;">
                                <i class="icon-list-alt"></i>
                            </a>
                        </li>
                        <li>
                            <a href="/the-forum" data-toggle="tooltip" title="The Forum" style="font-size: 20px;">
                                <i class="icon-trello"></i>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav pull-right">
                        <li class="dropdown">
                            <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                                Me <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
                                <li role="presentation">
                                    <a role="menuitem" tabindex="-1" href="/profile/{{ Auth::user()->username }}">Profile</a>
                                </li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="/settings">Settings</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Report a Problem</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="/signout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div class="container">
            @yield('content')
        </div>

        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        @yield('js')
        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip({'placement': 'bottom'});
            })
        </script>
    </body>
</html>
