        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/datatables/jquery.dataTables.min.css'); ?>" media="screen,projection" />
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/datatables/jquery.dataTables.min.js'); ?>"></script> 

        <link type="text/css" rel="stylesheet" href="<?= s3MVC_MakeLink('/datatables/responsive.dataTables.min.css'); ?>" media="screen,projection" />
        <script type="text/javascript" src="<?= s3MVC_MakeLink('/datatables/dataTables.responsive.min.js'); ?>"></script>                       

                    <div class="row">
                        <div class="col s12">
                            <div class="icon-block">
                                <h4 class="">Usage of My Tokens</h4>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>
                    <br>
        
                    <table id="usages-table" class="display">
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Request Uri</th>
                                <th>Time of Request</th>
                                <th>Request Full Details</th>
                                <th>Requesters IP</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($tokenRecords as $tokenRecord): ?>
                                <?php if(Lsia\Utils::isCountableWithData($tokenRecord->usages)): ?>
                                    <?php foreach($tokenRecord->usages as $tokenUsageRecord): ?>
                                        <tr>
                                            <td>
                                                <span title="<?= $tokenRecord->token; ?>">
                                                    <?= substr($tokenRecord->token, 0 , 10). ' ....'; ?>
                                                </span>
                                            </td>
                                            <td><?= $tokenUsageRecord->request_uri; ?></td>
                                            <td><?= $tokenUsageRecord->date_time_of_request; ?></td>
                                            <td>
                                                
                                                
                                                <!-- Modal Trigger -->
                                                <a class="waves-effect waves-light btn modal-trigger" href="#modal<?= $tokenUsageRecord->id; ?>">View</a>

                                                <!-- Modal Structure -->
                                                <div id="modal<?= $tokenUsageRecord->id; ?>" class="modal">
                                                    <div class="modal-content">
                                                        <h4>Request Details</h4>
                                                        <p><?= nl2br($tokenUsageRecord->request_full_details); ?></p>
                                                    </div>
                                                    <!--                                                    
                                                    <div class="modal-footer">
                                                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
                                                    </div>
                                                    -->
                                                </div>
                                            </td>
                                            <td><?= $tokenUsageRecord->requesters_ip; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; // if(Lsia\Utils::isCountableWithData($tokenRecord->usages)) ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <script>
                        $(document).ready(function () {
                            
                            <?php if($__is_logged_in): ?>
                                $('#usages-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('select').formSelect();
                                $('.modal').modal();
                            <?php endif; ?>
                                
                        });
                    </script>
