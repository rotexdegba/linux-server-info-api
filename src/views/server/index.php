                    <div id="server-index">
                        <div class="row pad-t-2-5-on-med-and-down">
                            <div class="col s12">
                                <ul class="collection with-header">
                                    <li class="collection-header"><h5>Server Summary</h5></li>
                                    <li class="collection-item"> <strong><?= $hostName['label']; ?>:</strong> <?= $hostName['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $distroNameAndVersion['label']; ?>:</strong> <?= $distroNameAndVersion['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $kernelVersion['label']; ?>:</strong> <?= $kernelVersion['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $osFamily['label']; ?>:</strong> <?= $osFamily['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $architecture['label']; ?>:</strong> <?= $architecture['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $machineModel['label']; ?>:</strong> <?= $machineModel['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $lastBootedOn['label']; ?>:</strong> <?= $lastBootedOn['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $uptime['label']; ?>:</strong> <?= $uptime['value']; ?> </li>
                                    <li class="collection-item"> <strong><?= $loggedInUsers['label']; ?>:</strong> <?= $loggedInUsers['value']; ?> </li>
                                    
                                    <li class="collection-item">
                                        
                                        <ul class="collection with-header">
                                            
                                            <li class="collection-header"><h6>Process Information</h6></li>
                                            
                                            <?php foreach ($processSummaryInfo as $processInfo): ?>
                                            
                                                <li class="collection-item"> <strong><?= $processInfo['label']; ?>:</strong> <?= $processInfo['value']; ?> </li>
                                                
                                            <?php endforeach; ?>
                                                
                                        </ul>
                                    </li>
                                    
                                </ul>
                            </div>
                        </div>
                        
                        <?php if($__is_logged_in): ?>
                            <div class="row">
                                <div class="col s12 m4">
                                    <div class="icon-block">
                                        <h2 class="center light-blue-text"><i class="material-icons">flash_on</i></h2>
                                        <h5 class="center">Speeds up development</h5>

                                        <p class="light">We did most of the heavy lifting for you to provide a default stylings that incorporate our custom components. Additionally, we refined animations and transitions to provide a smoother experience for developers.</p>
                                    </div>
                                </div>

                                <div class="col s12 m4">
                                    <div class="icon-block">
                                        <h2 class="center light-blue-text"><i class="material-icons">group</i></h2>
                                        <h5 class="center">User Experience Focused</h5>

                                        <p class="light">By utilizing elements and principles of Material Design, we were able to create a framework that incorporates components and animations that provide more feedback to users. Additionally, a single underlying responsive system across all platforms allow for a more unified user experience.</p>
                                    </div>
                                </div>

                                <div class="col s12 m4">
                                    <div class="icon-block">
                                        <h2 class="center light-blue-text"><i class="material-icons">settings</i></h2>
                                        <h5 class="center">Easy to work with</h5>

                                        <p class="light">We have provided detailed documentation as well as specific code examples to help new users get started. We are also always open to feedback and can answer any questions a user may have about Materialize.</p>
                                    </div>
                                </div>
                            </div>
                        
                        <?php else: ?>
                                <div class="row">
                                    <div class="col s12 center">
                                        <h5>Please Log in to view more detailed Server Information</h5>
                                    </div>
                                </div>
                        <?php endif; ?>
                    </div>


