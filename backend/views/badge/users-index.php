<?php
/* @var $this yii\web\View */
?>
<h2>Users Index</h2>

<a href="/badge/create-user" class="btn btn-success pull-right"> Create Authorized User</a>
<div id="w0" class="grid-view">
	<div class="summary">
		Showing <b>1-9</b> of <b>9</b> items.
	</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>#</th>
				<th style="width: 15%">	<a href="" data-sort="">Badge Number</a>
				</th>
				<th>	<a href="" data-sort="">Username</a>
				</th>

				<th>	<a href="" data-sort="">Full Name</a> </th>	
				<th>	<a href="" data-sort="">Privileges</a> </th>
				<th> Action </th>
			</tr>
			<tr id="w0-filters" class="filters">
				<td>&nbsp;</td>
				<td><input type="text" class="form-control" name="InvoiceSearch[invoice_id]"></td>
				<td><input type="text" class="form-control" name="InvoiceSearch[invoice_id]"></td>
				<td><input type="text" class="form-control" name="InvoiceSearch[customer_id]"></td>
				<td><select id="badges-gender" class="form-control" name="Badges[gender]">
				<option value="">select</option>
				<option value="1">Root</option>
				<option value="2">Admin</option>
				</select></td>
				<td> </td>
				
			</tr>
		</thead>
		<tbody>
			<tr data-key="4509">
				<td>1</td>
				<td>200001</td>
				<td>admin@11</td>
				<td>Jhon Doe</td>
				<td>Admin</td>
				<td>
					<a href="/badge/view-user" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
					<a href="/badge/edit-user" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
				</td>
			</tr>

			<tr data-key="4510">
				<td>2</td>
				<td>200002</td>
				<td>admin@12</td>
				<td>Hammy M</td>
				<td>Root</td>
				<td>
					<a href="/badge/view-user" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
					<a href="/badge/edit-user" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
				</td>
			</tr>

			<tr data-key="4510">
				<td>3</td>
				<td>200003</td>
				<td>admin@13</td>
				<td>Ben B</td>
				<td>Root</td>
				<td>
					<a href="/badge/view-user" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
					<a href="/badge/edit-user" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
				</td>
			</tr>
			
			
			
			

		</tbody>
	</table>
</div>