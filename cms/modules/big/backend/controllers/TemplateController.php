<?php
/**
 * @link http://www.bigbrush-agency.com/
 * @copyright Copyright (c) 2015 Big Brush Agency ApS
 * @license http://www.bigbrush-agency.com/license/
 */

namespace cms\modules\big\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use bigbrush\big\widgets\templateeditor\TemplateEditor;

/**
 * TemplateController
 */
class TemplateController extends Controller
{
    /**
     * Lists all available templates
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->big->getTemplate()->getModel()->find(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates and edits a template
     *
     * @param int $id optional template id to edit. If not provided a new template will be created
     */
    public function actionEdit($id = 0)
    {
        $model = TemplateEditor::getModel($id);
        if (TemplateEditor::save($model)) {
            Yii::$app->getSession()->setFlash('success', 'Template saved');
            if (Yii::$app->toolbar->stayAfterSave()) {
                return $this->redirect(['edit', 'id' => $model->id]);
            } else {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
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
        $model = TemplateEditor::getModel($id);
        $templateId = $_POST['id'];
        if ($templateId != $id) {
            throw new InvalidCallException("Invalid form submitted. Template with id: '$id' not deleted.");
        }

        if ($model->delete()) {
            Yii::$app->getSession()->setFlash('success', 'Template deleted.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Template "' . $model->title . '" could not be deleted.');
        }

        return $this->redirect(['index']);
    }
}
