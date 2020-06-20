<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = 'Club Name Look up';
$this->params['breadcrumbs'][] = $this->title;
?>

             
<?php
/* @var $this yii\web\View */
?>

<h2>Club Names</h2>
<a  class="btn btn-success pull-right" href="/badge/club-name-create" > Create Club</a>

<div id="w0" class="grid-view">
    <div class="summary">
        Showing <b>1-6</b> of <b>6</b> items.
    </div>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th> <a href="" data-sort="">Proper Club</a>
                </th>
                <th> <a href="" data-sort="">Properid</a>
                </th>

                <th> <a href="" data-sort="">Abbrev</a> </th>    
                <th> action</th>
            </tr>
            <tr id="w0-filters" class="filters">
                <td>&nbsp;</td>
                <td><input type="text" class="form-control" name="InvoiceSearch[invoice_id]"></td>
                <td><input type="text" class="form-control" name="InvoiceSearch[customer_id]"></td>
                <td><input type="text" class="form-control" name="InvoiceSearch[customer_id]"></td>
                <td> </td>
              
            </tr>
        </thead>
        <tbody>
            <tr data-key="4509">
                <td> 1</td>
                <td>Anne Aundel Country County Gun Club</td>
                <td>30</td>
                <td>AA County</td>
                <td>
                    <a href="/badge/club-name-edit" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="/badge/club-name-edit" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                
            </tr>

            <tr data-key="4509">
                <td> 2</td>
                <td>Applied Physics Laboratory Gun Club</td>
                <td>01</td>
                <td>APL</td>
                <td>
                    <a href="/badge/club-name-edit" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="/badge/club-name-edit" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                
            </tr>

            <tr data-key="4509">
                <td> 3</td>
                <td>Arlington Rifle and pistol Club</td>
                <td>02</td>
                <td>Arlington</td>
                <td>
                    <a href="/badge/club-name-edit" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="/badge/club-name-edit" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                
            </tr>

            <tr data-key="4509">
                <td> 4</td>
                <td>Anne Aundel Country County Gun Club</td>
                <td>30</td>
                <td>AA County</td>
                <td>
                    <a href="/badge/club-name-edit" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="/badge/club-name-edit" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                
            </tr>

            <tr data-key="4509">
                <td> 5</td>
                <td>Applied Physics Laboratory Gun Club</td>
                <td>01</td>
                <td>APL</td>
                <td>
                    <a href="/badge/club-name-edit" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="/badge/club-name-edit" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                
            </tr>

            <tr data-key="4509">
                <td> 6</td>
                <td>Arlington Rifle and pistol Club</td>
                <td>02</td>
                <td>Arlington</td>
                <td>
                    <a href="/badge/club-name-edit" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="/badge/club-name-edit" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                </td>
                
            </tr>

           
            
            
            
            

        </tbody>
    </table>
</div>
           

