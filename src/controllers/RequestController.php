<?php

namespace app\controllers;

use app\models\Manager;
use Yii;
use app\models\Request;
use app\models\RequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Класс контроллера для работы с заявками.
 */
class RequestController extends Controller
{
    /**
     * Метод просмотра списка заявок.
     *
     * @return string
     */
    public function actionIndex() : string
    {
        $searchModel  = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Метод просмотра карточки заявки.
     *
     * @param int $id Идентификатор заявки.
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
     * Метод создания заявки.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Request();

        if ($model->load(Yii::$app->request->post())) {
            $model->previous_request_id = $model->getDuplicateRequestId();
            if ($model->previous_request_id) {
                $previousRequest = Request::findOne($model->previous_request_id);
                $previousManager = Manager::findOne($previousRequest->manager_id);
                if ($previousManager->is_works) {
                    $model->manager_id = $previousManager->id;
                } else {
                    $model->manager_id = Manager::getRandomManagerId();
                }
            } else {
                $model->manager_id = Manager::getRandomManagerId();
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Метод обновления заявки.
     *
     * @param int $id Идентификатор заявки.
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->previous_request_id = $model->getDuplicateRequestId();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
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
     * @return Request|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
