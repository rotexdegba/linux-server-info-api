<div class="col s12">


    <div class="row">
        <div class="col s6">
            <div class="icon-block">
                <h5 class="">My Tokens</h5>
            </div>
        </div>
        <div class="col s6">
            <a class="waves-effect waves-light btn right"
               href="<?= s3MVC_MakeLink('/tokens/add'); ?>"
            >
                Add Token<i class="material-icons right">add</i>
            </a>
        </div>
    </div>
    
    <div class="divider"></div>
    <br>
    
    <?php if(\Lsia\Utils::isCountableWithData($tokenRecords)): ?>
        <?php $cssActiveClassForFirstItem = 'active'; ?>
        <ul class="collapsible expandable">
            <?php foreach($tokenRecords as $tokenRecord): ?>
                <li class="<?= $cssActiveClassForFirstItem; ?>">
                    <div class="collapsible-header"><i class="material-icons">vpn_key</i><?= substr($tokenRecord->token, 0 , 10). ' ....'; ?></div>
                    <div class="collapsible-body"><span><?= $tokenRecord->token; ?></span></div>
                </li>
                <?php $cssActiveClassForFirstItem = ''; ?>
             <?php endforeach; ?>
        </ul>
    
    <?php else: ?>
    
        <p>None</p>
    
    <?php endif; ?>
</div>
