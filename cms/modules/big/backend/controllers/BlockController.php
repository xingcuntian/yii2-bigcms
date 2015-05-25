<?php
/**
 * @link http://www.bigbrush-agency.com/
 * @copyright Copyright (c) 2015 Big Brush Agency ApS
 * @license http://www.bigbrush-agency.com/license/
 */

namespace cms\modules\big\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\InvalidCallException;
use yii\web\MethodNotAllowedHttpException;

/**
 * BlockController
 */
class BlockController extends Controller
{
    /**
     * Show a page with all created blocks.
     *
     * @return string the rendering result.
     */
    public function actionIndex()
    {
        $manager = Yii::$app->big->blockManager;
        $blocks = $manager->find()->all();
        $installedBlocks = $manager->getInstalledBlocks();
        return $this->render('index', [
            'blocks' => $blocks,
            'installedBlocks' => $installedBlocks,
        ]);
    }

    /**
     * Edit and create a block
     *
     * @param string|int $id if an integer is provided it is regarded as database record. If it is a
     * string it is regarded as a new block.
     * @return string
     * @throws MethodNotAllowedHttpException if form is posted and 'Block' is not a key in $_POST
     */
    public function actionEdit($id)
    {
        $block = Yii::$app->big->blockManager->createBlock($id);
        $model = $block->model;
        if ($model->getIsNewRecord()) {
            $model->name = $id;
            $model->show_title = true;
        }
        $post = Yii::$app->getRequest()->post();
        if ($model->load($post)) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Block saved');
                if (Yii::$app->toolbar->stayAfterSave()) {
                    return $this->redirect(['edit', 'id' => $model->id]);
                } else {
                    return $this->redirect(['index']);
                }
            }
        } elseif (!empty($post)) {
            throw new MethodNotAllowedHttpException('Form not saved because "Block" was not set in $_POST');
        }
        return $this->render('edit', [
            'block' => $block,
        ]);
    }

    /**
     * Deletes a block.
     *
     * @return int $id an id of a block to delete.
     * @throws InvalidCallException if id in $_POST does not match the provided id. 
     */
    public function actionDelete($id)
    {
        $model = Yii::$app->big->blockManager->getModel()->findOne($id);
        $blockId = $_POST['id'];
        if ($blockId != $id) {
            throw new InvalidCallException("Invalid form submitted. Block with id: '$id' not deleted.");
        }

        if ($model->delete()) {
            Yii::$app->getSession()->setFlash('success', 'Block deleted.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Block "' . $model->name . '" could not be deleted.');
        }

        return $this->redirect(['index']);
    }
}
