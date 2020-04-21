<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
        <title>Server Info API</title>
        
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="<?php echo s3MVC_MakeLink('/materialize/css/materialize.min.css'); ?>" />
        
        <link type="text/css" rel="stylesheet" href="<?php echo s3MVC_MakeLink('/css/app.css'); ?>" />
    </head>
    <body>
        <div>
            <ul style="padding-left: 0;">
                <li style="display: inline;"><a href="#">Section 1</a></li>
                <li style="display: inline;"><a href="#">Section 2</a></li>
                <li style="display: inline;"><a href="#">Section 3</a></li>
            </ul>
        </div>
        
        <div>
            <h1>Welcome to the Server Info API</h1>
            <p>This site is powered by the <a href="https://github.com/rotexsoft/slim3-skeleton-mvc-app">SlimPHP 3 Skeleton MVC App.</a></p>
        </div>
        
        <br>
        
        <div>    
            <div>
                <?php echo $content; ?>                
            </div>
        </div>

        <footer>
            <div>
                <hr/>
                <p>© Copyright no one at all. Go to town.</p>
            </div> 
        </footer>

        <script type="text/javascript" src="<?php echo s3MVC_MakeLink('/materialize/js/materialize.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo s3MVC_MakeLink('/js/app.js'); ?>"></script>
    </body>
</html>
