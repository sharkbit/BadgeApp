<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use backend\models\CardReceiptDate;

/**
 * Searches dated card receipt records.
 */
class CardReceiptDateSearch extends CardReceiptDate {
    public function rules() {
        return [
            [['cart', 'date_rng', 'tx_type', 'name', 'cashier_badge'], 'safe'],
            [['badge_number'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = CardReceiptDate::find()
            ->select([
                'badge_subscriptions_date.created_at',
                'badge_subscriptions_date.transaction_type',
                'badge_subscriptions_date.badge_number',
                'cc_receipts_date.*',
            ])
            ->from('cc_receipts_date')
            ->joinWith('badges', true, 'LEFT JOIN')
            ->joinWith('badge_subscriptions_date', true, 'LEFT JOIN');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!isset($params['sort'])) {
            $query->orderBy(['cc_receipts_date.tx_date' => SORT_DESC]);
        }

        if (!Yii::$app->controller->hasPermission('sales/all')) {
            $query->andFilterWhere(['cc_receipts_date.badge_number' => $_SESSION['badge_number']]);
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $dateRange = preg_split('/\s+[-–—]\s+/', trim((string) $this->date_rng), 2);
        if (count($dateRange) === 2 && strtotime($dateRange[0]) !== false && strtotime($dateRange[1]) !== false) {
            $query->andWhere(['>=', 'cc_receipts_date.tx_date', date('Y-m-d', strtotime($dateRange[0])).' 00:00:00']);
            $query->andWhere(['<=', 'cc_receipts_date.tx_date', date('Y-m-d', strtotime($dateRange[1])).' 23:59:59']);
        } else {
            $this->tx_date = date('Y-m-d', strtotime('-30 days'));
            $query->andWhere(['>=', 'cc_receipts_date.tx_date', $this->tx_date.' 00:00:00']);
        }

		if(!empty($this->badge_number)) {$query->andFilterWhere(['like', 'cc_receipts_date.badge_number', $this->badge_number]); }
		if(!empty($this->cart))			{$query->andFilterWhere(['like', 'cc_receipts_date.cart', $this->cart]); }
		if(!empty($this->name)) 		{$query->andFilterWhere(['like', 'cc_receipts_date.name', $this->name]); }
		if(!empty($this->amount)) 		{$query->andFilterWhere(['like', 'cc_receipts_date.amount', $this->amount]); }
		if(!empty($this->tx_type)) 		{$query->andFilterWhere(['like', 'cc_receipts_date.tx_type', $this->tx_type]); }

        $cashierNames = array_values(array_filter(
            array_map('trim', explode(',', (string) $this->cashier_badge)),
            static function ($name) {
                return $name !== '';
            }
        ));
        if ($cashierNames) {
            $cashierNameExpression = new Expression("CONCAT(badges.first_name, ' ', badges.last_name)");
            $cashierConditions = ['or'];
            foreach ($cashierNames as $cashierName) {
                $cashierConditions[] = ['like', $cashierNameExpression, $cashierName];
            }
            $query->andWhere($cashierConditions);
        }

//yii::$app->controller->createLog(true, 'trex-b-m-s-crs', 'Raw Sql: '.var_export($query->createCommand()->getRawSql(),true));

		return $dataProvider;
	}
}
