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
                                <th>HTTP Status Code</th>
                                <th>Error Details</th>
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
                                                </div>
                                            </td>
                                            <td><?= $tokenUsageRecord->requesters_ip; ?></td>
                                            <td><?= $tokenUsageRecord->http_status_code; ?></td>
                                            <td>
                                                    <?php if($tokenUsageRecord->http_status_code.'' === '200'): ?>
                                                        
                                                        No Error
                                                        
                                                    <?php else: ?>

                                                        <!-- Modal Trigger -->
                                                        <a class="waves-effect waves-light btn modal-trigger" href="#error-modal<?= $tokenUsageRecord->id; ?>">View</a>

                                                        <!-- Modal Structure -->
                                                        <div id="error-modal<?= $tokenUsageRecord->id; ?>" class="modal">
                                                            <div class="modal-content">
                                                                <h4>Error Details</h4>
                                                                <p><?= nl2br($tokenUsageRecord->request_error_details); ?></p>
                                                            </div>
                                                        </div>
                                                     
                                                    <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; // if(Lsia\Utils::isCountableWithData($tokenRecord->usages)) ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <script>
                        
                        // create the modals before the datatable initialization
                        $(document).on( 'preInit.dt', function (e, settings) {
                            $('.modal').modal();
                        });
                        
                        $(document).ready(function () {
                            
                            <?php if($__is_logged_in): ?>
                                $('#usages-table').DataTable({"responsive": true, "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500, 1000, 5000 ]});
                                $('select').formSelect();
                            <?php endif; ?>
                        });
                    </script>
