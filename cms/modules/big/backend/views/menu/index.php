<?php
/**
 * @link http://www.bigbrush-agency.com/
 * @copyright Copyright (c) 2015 Big Brush Agency ApS
 * @license http://www.bigbrush-agency.com/license/
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropDown;
use yii\bootstrap\Alert;

$this->registerJs('
    function alert(message, type) {
        var button = $("<button>", {
            type: "button",
            class: "close",
            "data-dismiss": "alert",
            "aria-hidden": "true",
            text: "x"
        });
        var alert = $("<div>", {
            class: "alert alert-"+type+" fade in",
        }).css("margin-top", "15px").append(button).append(message);
        $("#alert").empty().html(alert);
    }
    
    var wrapper = $("#grid");
    $("#grid").on("click", ".changeDirectionBtn", function(e){
        var self = $(this),
            direction = self.data("direction"),
            menuId = self.data("pid");

        $.post("'.Url::to(['move']).'", {node_id: menuId, direction: direction}, function(data){
            if (data.status === "success") {
                wrapper.empty().html(data.grid);
            }
            var type = data.status == "error" ? "danger" : data.status;
            alert(data.message, type);
        }, "json");

        e.preventDefault();
    });

    $(".delete-form .btn").click(function(e){
        if (confirm("Are you sure to delete this menu item?")) {
            return true;
        }
        return false;
    });
');

Yii::$app->toolbar->add()->add('Edit menus', ['menus'], 'bars');

$this->title = Yii::$app->id . ' | Menu items';
?>

<div class="row">
    <div class="col-md-12">
        <div id="alert">
        </div>
        <h1>Menu items</h1>
        <?= ButtonDropDown::widget([
            'label' => 'Select menu',
            'options' => ['class' => 'btn btn-default', 'style' => 'margin-bottom: 10px;'],
            'dropdown' => [
                'items' => $dropdown,
            ],
        ]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="grid">
            <?= $this->render('_grid', ['dataProvider' => $dataProvider]); ?>
        </div>
    </div>
</div>