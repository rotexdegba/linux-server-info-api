<?php
    $prepend_action = !S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;

    $action = ($prepend_action) ? 'action-login' : 'login';
    $login_path = s3MVC_MakeLink("/{$controller_object->controller_name_from_uri}/$action");
    
    $action1 = ($prepend_action) ? 'action-logout' : 'logout';
    $logout_action_path = s3MVC_MakeLink("/{$controller_object->controller_name_from_uri}/$action1/0");
?>

<?php if( !empty($error_message) ): ?>

    <p style="background-color: orange;"></p>
    
    <div class="card-panel red darken-4 white-text">
        <?= $error_message;  ?>
    </div>

    
<?php endif; ?>

<?php if( !$controller_object->isLoggedIn() ): ?>
    
    <div class="col s12">
        <div class="icon-block">
            <h5 class="">Login</h5>
        </div>
        
        <div class="divider"></div>
        
        <form action="<?php echo $login_path; ?>" method="post" class="pad-l1">

            <div class="row">
                <div class="input-field col s12">
                    <input name="username" id="username" type="text" class="validate" value="<?php echo $username; ?>">
                    <label for="username">User Name</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input name="password" id="password" type="password" class="validate">
                    <label for="password">Password</label>
                </div>
            </div>

            <div>
                <input type="submit" class="btn white-text waves-button-input" value="Login">
            </div>

        </form>
    </div>
    

    
<?php else: ?>
    
    <form action="<?php echo $logout_action_path; ?>" method="post">
        
      <input type="submit" value="Logout">
      
    </form>
    
<?php endif; //if( !$controller_object->isLoggedIn() ): ?>
