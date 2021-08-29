<?php

namespace app\controllers;

use Yii;
use app\models\Manager;
use app\models\ManagerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Класс контроллера для работы с менеджерами.
 */
class ManagerController extends Controller
{
    /**
     * Метод просмотра списка менеджеров.
     *
     * @return string
     */
    public function actionIndex() : string
    {
        $searchModel  = new ManagerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Метод просмотра карточки менеджера.
     *
     * @param int $id Идентификатор менеджера.
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id) : string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Метод создания менеджера.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Manager();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Метод обновления менеджера.
     *
     * @param int $id Идентификатор менеджера.
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Метод поиска модели по её идентификатору.
     *
     * @param int $id Идентификатор модели.
     *
     * @return Manager|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id)
    {
        if (($model = Manager::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
