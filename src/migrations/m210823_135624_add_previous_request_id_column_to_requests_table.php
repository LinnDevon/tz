<?php

use app\models\Request;
use yii\db\Migration;

/**
 * Миграция по добавлению колонки previous_request_id в таблицу requests.
 */
class m210823_135624_add_previous_request_id_column_to_requests_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('requests', 'previous_request_id', $this->integer());

        $this->addForeignKey(
            'fk-requests-previous_request_id',
            'requests',
            'previous_request_id',
            'requests',
            'id'
        );

        $requests = Request::find()->all();

        foreach ($requests as $request) {
            $request->previous_request_id = $request->getDuplicateRequestId();

            if ($request->previous_request_id) {
                $request->save();
            }
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-requests-previous_request_id',
            'requests'
        );

        $this->dropColumn('requests', 'previous_request_id');
    }
}
