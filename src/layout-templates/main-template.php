<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
        <title>Server Info API</title>

        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/materialize/css/materialize.min.css'); ?>" media="screen,projection" />

        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/css/app.css'); ?>" media="screen,projection" />
        
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/js/jquery-3.5.0.min.js'); ?>"></script>
    </head>
    <body>
        <nav class="light-blue lighten-1" role="navigation">
            
            <div class="nav-wrapper pad-l1 pad-r1">
                
                <div class="row">
                    
                    <!-- home link -->
                    <div class="col s12 l5">
                        <a href="#" data-target="slide-out" class="sidenav-trigger show-on-large"><i class="material-icons">menu</i></a>
                        <a id="logo-container" href="<?= s3MVC_MakeLink('/'); ?>" class="brand-logo left pad-l-2-5-on-med-and-down">
                            <i class="large material-icons">dns</i>Server Info API
                        </a>
                    </div>

                    <!-- bread crumb links -->
<!--                    
                    <div class="col l4 center hide-on-med-and-down">
                        <a href="#!" class="breadcrumb">First</a>
                        <a href="#!" class="breadcrumb">Second</a>
                        <a href="#!" class="breadcrumb">Third</a>
                    </div>
-->

                    
                    <!-- login / logout button -->
                    <div class="col s12 l7">
                        <ul class="right">
                            <?php if ($__is_logged_in): ?>
                                <li>
                                    <a  class="waves-effect waves-light btn light-blue darken-4  tooltipped"
                                         data-position="bottom" data-tooltip="<?= $__logged_in_user_name; ?>'s Tokens"
                                        href="<?= s3MVC_MakeLink("/tokens/my-tokens"); ?>"
                                    >
                                        <i class="material-icons right">vpn_key</i>My Tokens
                                    </a>
                                    <a  class="waves-effect waves-light btn light-blue darken-4  tooltipped"
                                         data-position="bottom" data-tooltip="<?= $__logged_in_user_name; ?>"
                                        href="<?= s3MVC_MakeLink("/{$__controller_name_from_uri}/logout"); ?>"
                                    >
                                        <i class="material-icons right">person</i>Logout
                                    </a>
                                </li>
                           <?php else: ?>
                                <li>
                                    <a  class="waves-effect waves-light btn light-blue darken-4"
                                        href="<?= s3MVC_MakeLink("/{$__controller_name_from_uri}/login"); ?>"
                                    >
                                        <i class="material-icons right">person</i>Login 
                                    </a>
                                </li>
                           <?php endif; ?> 
                        </ul>
                    </div>
                    
                </div> <!-- <div class="row"> -->
                
                <!-- Initial Responsive Nav system -->
                <!--
                    <ul class="right hide-on-med-and-down">
                        <li><a href="#">Navbar Link</a></li>
                    </ul>

                    <ul id="nav-mobile" class="sidenav">
                        <li><a href="#">Navbar Link</a></li>
                    </ul>
                    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                -->
            </div> <!-- <div class="nav-wrapper pad-l1 pad-r1"> -->
            
        </nav>
        
        <ul id="slide-out" class="sidenav">
            <?php if ($__is_logged_in): ?>
                <li>
                    <a href="<?= s3MVC_MakeLink("/token-usage/index"); ?>">Usage of My Tokens</a>
                </li>
            <?php endif; // if ($__is_logged_in) ?>
                <li><a href="#!" onclick="alert('Coming Soon!');">API Documentation</a></li>
        </ul>

        <div class="row" id="main-content-div">

            <div class="col s12">

                <div class="section no-pad-bot">
                    <div class="container">
                        
                        <?php if( isset($__last_flash_message) && $__last_flash_message !== null ): ?>

                            <!-- Header Alert Messaging region - only show if the message variable is not empty -->
                            <div class="card-panel rounded <?= $__last_flash_message_css_class ?>">

                                <div class="row">

                                    <div class="col s1">
                                        <h1 class="d-inline">
                                            <?= $__last_flash_message['title'] ?>
                                        </h1>
                                    </div>
                                    <div class="col s11">
                                        <p class="d-inline">
                                            <?= $__last_flash_message['message'] ?>
                                        </p>
                                    </div>
                                </div>

                            </div>

                        <?php endif; // if( isset($__last_flash_message) && $__last_flash_message !== null ): ?>

                    </div>
                </div>

                <div class="container">
                    <div class="section">

                        <div class="row">
                            <div class="col s12 m12">
                                <?= $content; ?>
                            </div>
                        </div>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>

        <footer class="page-footer orange">
            
            <div class="container">
                <div class="row">
<!--                    <div class="col l6 s12">
                        <h5 class="white-text">Company Bio</h5>
                        <p class="grey-text text-lighten-4">
                            We are a team of college students working on this 
                            project like it's our full time job. Any amount would 
                            help support and continue development on this project 
                            and is greatly appreciated.
                        </p>
                    </div>
                    <div class="col l3 s12">
                        <h5 class="white-text">Settings</h5>
                        <ul>
                            <li><a class="white-text" href="#!">Link 1</a></li>
                            <li><a class="white-text" href="#!">Link 2</a></li>
                            <li><a class="white-text" href="#!">Link 3</a></li>
                            <li><a class="white-text" href="#!">Link 4</a></li>
                        </ul>
                    </div>
                    <div class="col l3 s12">
                        <h5 class="white-text">Connect</h5>
                        <ul>
                            <li><a class="white-text" href="#!">Link 1</a></li>
                            <li><a class="white-text" href="#!">Link 2</a></li>
                            <li><a class="white-text" href="#!">Link 3</a></li>
                            <li><a class="white-text" href="#!">Link 4</a></li>
                        </ul>
                    </div>-->
                </div>
            </div>
            
            <div class="footer-copyright orange accent-4" style="padding-bottom: 0;">
                <div class="container">

                    <div class="row">
                        <div class="col s3">
                            <p>
                                &copy; Copyright <?= date('Y'); ?>
                            </p>
                        </div>
                        <div class="col s9 right-align">
                            <p>
                                Powered by the <a target="_blank" class="blue-text text-darken-3" href="https://github.com/rotexsoft/slim3-skeleton-mvc-app">SlimPHP 3 Skeleton MVC App framework</a>
                                and UI goodness from <a target="_blank" class="blue-text text-darken-3" href="https://materializecss.com/">Materializecss</a>
                            </p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </footer>

        
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/materialize/js/materialize.min.js'); ?>"></script>
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/js/app.js'); ?>"></script>
    </body>
</html>
