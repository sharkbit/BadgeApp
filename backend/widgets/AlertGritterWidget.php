<?php

namespace backend\widgets;

use yii\base\Widget;
use yii\web\View;
use Yii;

class AlertGritterWidget extends Widget
{
    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        foreach ($flashes as $type => $message) {
            $this->registerGritterNotification($type, $message);
        }

        $session->removeAllFlashes();
    }

    protected function registerGritterNotification($type, $message)
    {
        $js = "
            $.gritter.add({
                title: '" . ucfirst($type) . "',
                text: '" . addslashes($message) . "',
                class_name: 'gritter-" . $type . "',
            });
        ";

        $this->view->registerJs($js, View::POS_READY);
    }
}
