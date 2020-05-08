<?php
    /** @var \Vespula\Form\Form $vespForm */
    $vespForm->autoLf();
?>
<?php if( count($errorMessages) > 0 ): ?>

    <div class="card-panel red darken-4 white-text">
        There are one or more Error(s) you need to attend to
    </div>

<?php endif; ?>
    <div class="col s12">
        <div class="icon-block">
            <h5 class=""><?= $formTitle; ?></h5>
        </div>
        
        <div class="divider"></div>

        <?= 
            $vespForm->action($formAction)
                     ->name('add-edit-token-form')
                     ->id('add-edit-token-form')
                     ->attribute('class', "pad-l1")
                     ->method('post')
                     ->begin();
        ?>
        
        <?php
            // Spit out all the hidden inputs
            echo is_null($formData['id']) ? '' : $vespForm->hidden()->idName('id')->valueRaw($formData['id']);            
            echo $vespForm->hidden()->idName($formData['__csrf_key'])->valueRaw($formData['__csrf_value']);
            echo $vespForm->hidden()->idName('generators_username')->value($formData['generators_username']);
            echo $vespForm->hidden()->idName('date_created')->value($formData['date_created']);
            echo $vespForm->hidden()->idName('date_last_edited')->value($formData['date_last_edited']);
            echo $vespForm->hidden()->idName('creators_ip')->value($formData['creators_ip']);
        ?>
            <br>
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">vpn_key</i>
                    <?= 
                        $vespForm->textarea()
                                 ->idName('token')
                                 ->addClass('materialize-textarea')
                                 ->attribute('readonly', 'readonly')
                                 ->attribute('required', 'required')
                                 ->value($formData['token']); 
                    ?>
                    <?= $vespForm->label('Token')->attribute('for', 'token'); ?>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s6">
                    <i class="material-icons prefix">iso</i>
                    <?= 
                        $vespForm->text()
                                 ->idName('max_requests_per_day')
                                 ->type('number')
                                 ->attribute('min', '0')
                                 ->attribute('required', 'required')
                                 ->attribute('placeholder', '0 for unlimited requests per day')
                                 ->addClass('validate')
                                 ->value($formData['max_requests_per_day']); 
                    ?>
                    <?= $vespForm->label('Maximum Api Requests per Day')->attribute('for', 'max_requests_per_day'); ?>
                    <span class="helper-text">0 for unlimited requests per day</span>
                    <?= \Lsia\Utils::displayFieldErrors('max_requests_per_day', $errorMessages) ?>
                </div>
                
                <div class="input-field col s6">
                    <i class="material-icons prefix">insert_invitation</i>
                    <?= 
                        $vespForm->text()
                                 ->idName('expiry_date')
                                 ->type('date')
                                 ->attribute('required', 'required')
                                 ->addClass('validate')
                                 ->value($formData['expiry_date']); 
                    ?>
                    <?= $vespForm->label('Expiry Date')->attribute('for', 'expiry_date'); ?>
                    <span class="helper-text">Day this token becomes invalid for making API requests to this application</span>
                    <?= \Lsia\Utils::displayFieldErrors('expiry_date', $errorMessages) ?>
                </div>
            </div>

            <div>
                <input type="submit" 
                       name="submit-button" 
                       id="submit-button" 
                       class="btn white-text waves-button-input" 
                       value="Save"
                >
                <a href="<?= s3MVC_MakeLink('/tokens/my-tokens'); ?>" class="btn white-text waves-button-input">Cancel</a>
            </div>

        <?= $vespForm->end(); ?>
    </div>
