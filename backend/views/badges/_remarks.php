<?php 


?>
<div class="remarks-box">
    <div class="timeline-centered">
    <?php 
    if(!empty($remakrs_logs))
    foreach ($remakrs_logs as $log) { ?>
        <article class="timeline-entry">
            <div class="timeline-entry-inner">
                <div class="timeline-icon bg-primary">
                    <i class="entypo-feather"></i>
                </div>
                <div class="timeline-label">
                    <h2><!-- <a href="#">Update</a> --> 
    					<span><?= date('M d, Y h:i:s A',strtotime($log['created_at'])) ?>
    						<?php if(isset($log['changed']) && $log['changed']!='') echo ', ('.$log['changed'].')'; ?>
    					</span>
    				</h2>
                    <p> <?php if($log['data']!='') {echo $log['data'];} else {echo'--blank--';}?></p>
                </div>
            </div>
        </article>
        
    <?php } ?> 
            <article class="timeline-entry begin">
                <div class="timeline-entry-inner">
                    <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                        <i class="entypo-flight"></i> +
                    </div>
                </div>
            </article>
    </div>
</div>