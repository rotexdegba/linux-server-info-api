<div class="col s12">
    <div class="row">
        <div class="col s12 l6">
            <div class="icon-block">
                <h4 class="">My Tokens</h4>
            </div>
        </div>
        <div class="col s12 l6">
            <a class="waves-effect waves-light btn right"
               href="<?= s3MVC_MakeLink('/tokens/add'); ?>"
            >
                Add Token<i class="material-icons right">add</i>
            </a>
        </div>
    </div>
    
    <div class="divider"></div>
    <br>
    
    <div class="icon-block">
        <h5 class="">Active Tokens</h5>
    </div>
    
    <?php if(\Lsia\Utils::isCountableWithData($activeTokenRecords)): ?>
        <?php $cssActiveClassForFirstItem =  empty($idOfLastEditedToken) ? 'active' : ''; ?>
        <ul class="collapsible expandable">
            
            <?php /** @var \Lsia\Atlas\Models\Token\TokenRecord $tokenRecord */ ?>
            <?php foreach($activeTokenRecords as $tokenRecord): ?>
                <li class="<?= ($idOfLastEditedToken.'' === $tokenRecord->id) ? 'active' : $cssActiveClassForFirstItem; ?>"
                    id="token-<?= $tokenRecord->id ?>"
                >
                    <div class="collapsible-header"><i class="material-icons">vpn_key</i><?= substr($tokenRecord->token, 0 , 10). ' ....'; ?></div>
                    <div class="collapsible-body blue-grey darken-1">
                        <ul class="collection">
                            
                            <li class="collection-item right-align">
                                <a class="waves-effect waves-light btn-small"
                                   href="<?= s3MVC_MakeLink('/tokens/edit/'.$tokenRecord->id); ?>"
                                >
                                    Edit<i class="material-icons right">edit</i>
                                </a>
                                <a class="waves-effect waves-light btn-small"
                                   href="<?= s3MVC_MakeLink('/tokens/delete/'.$tokenRecord->id); ?>"
                                   onclick="return confirm('Are you sure you want to delete this token?');"
                                >
                                    Delete<i class="material-icons right">delete</i>
                                </a>
                            </li>
                            
                            <li class="collection-item" style="word-wrap: break-word; width: 100%;">
                                <strong>Token:</strong> 
                                <span id="token-span-<?= $tokenRecord->id; ?>"><?= $tokenRecord->token; ?></span>
                                <button class="right" 
                                        onclick="copyToken('token-span-<?= $tokenRecord->id; ?>');"
                                >
                                    Copy<i class="material-icons right">content_copy</i>
                                </button>
                            </li>
                            
                            <li class="collection-item"><strong>Date Created:</strong> <?= $tokenRecord->date_created . date(' T'); ?></li>
                            <li class="collection-item"><strong>Last Edited:</strong> <?= $tokenRecord->date_last_edited . date(' T'); ?></li>
                            <li class="collection-item"><strong>Creator's IP:</strong> <?= $tokenRecord->creators_ip; ?></li>
                            <li class="collection-item"><strong>Maximum Api Requests per Day:</strong> <?= $tokenRecord->max_requests_per_day; ?></li>
                            <li class="collection-item"><strong>Expiry Date:</strong> <?= $tokenRecord->expiry_date . date(' T'); ?></li>
                        </ul> 
                    </div>
                </li>
                <?php $cssActiveClassForFirstItem = ''; ?>
             <?php endforeach; ?>
        </ul>
    
    <?php else: ?>
    
        <p>None</p>
    
    <?php endif; ?>
    <br>
    
    <div class="icon-block">
        <h5 class="">Expired Tokens</h5>
    </div>
    
    <?php if(\Lsia\Utils::isCountableWithData($expiredTokenRecords)): ?>
        <?php $cssActiveClassForFirstItem =  empty($idOfLastEditedToken) ? 'active' : ''; ?>
        <ul class="collapsible expandable">
            <?php /** @var \Lsia\Atlas\Models\Token\TokenRecord $tokenRecord */ ?>
            <?php foreach($expiredTokenRecords as $tokenRecord): ?>
                <li class="<?= ($idOfLastEditedToken.'' === $tokenRecord->id) ? 'active' : $cssActiveClassForFirstItem; ?>"
                    id="token-<?= $tokenRecord->id ?>"
                >
                    <div class="collapsible-header">
                        <i class="material-icons">vpn_key</i><?= substr($tokenRecord->token, 0 , 10). ' ....'; ?>
                    </div>
                    <div class="collapsible-body blue-grey darken-1">
                        <ul class="collection">
                            
                            <li class="collection-item right-align">
                                <a class="waves-effect waves-light btn-small"
                                   href="<?= s3MVC_MakeLink('/tokens/edit/'.$tokenRecord->id); ?>"
                                >
                                    Edit<i class="material-icons right">edit</i>
                                </a>
                                <a class="waves-effect waves-light btn-small"
                                   href="<?= s3MVC_MakeLink('/tokens/delete/'.$tokenRecord->id); ?>"
                                   onclick="return confirm('Are you sure you want to delete this token?');"
                                >
                                    Delete<i class="material-icons right">delete</i>
                                </a>
                            </li>
                            
                            <li class="collection-item" style="word-wrap: break-word;"><strong>Token:</strong> <?= $tokenRecord->token; ?></li>
                            <li class="collection-item"><strong>Date Created:</strong> <?= $tokenRecord->date_created . date(' T'); ?></li>
                            <li class="collection-item"><strong>Last Edited:</strong> <?= $tokenRecord->date_last_edited . date(' T'); ?></li>
                            <li class="collection-item"><strong>Creator's IP:</strong> <?= $tokenRecord->creators_ip; ?></li>
                            <li class="collection-item"><strong>Maximum Api Requests per Day:</strong> <?= $tokenRecord->max_requests_per_day; ?></li>
                            <li class="collection-item"><strong>Expiry Date:</strong> <?= $tokenRecord->expiry_date . date(' T'); ?></li>
                        </ul> 
                    </div>
                </li>
                <?php $cssActiveClassForFirstItem = ''; ?>
             <?php endforeach; ?>
        </ul>
    
    <?php else: ?>
    
        <p>None</p>
    
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {

        $('.collapsible').collapsible({accordion: false});
        
        <?php if( !empty($idOfLastEditedToken) ): ?>
            // Scroll screen to the edited token
            //var elmnt = document.getElementById("token-<?= $idOfLastEditedToken; ?>");
            //elmnt.scrollIntoView();
        <?php endif; ?>
    });
    
    function copyToken(tokenSpanId) {
        var copyText = document.getElementById(tokenSpanId);
        var textArea = document.createElement("textarea");
        textArea.value = copyText.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();
        M.toast({html: 'Copied!'})
    }
</script>