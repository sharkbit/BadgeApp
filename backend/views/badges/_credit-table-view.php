<?php
 

?>

<tr>
    <td><?=$model->project_name?></td>
	<td><?=date('M d, Y',strtotime($model->work_date))?></td>
	<td><?=$model->authorized_by?></td>
	<td><?= $model->work_hours?></td>
</tr>