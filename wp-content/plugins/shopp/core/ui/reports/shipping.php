<?php

class ShippingReport extends ShoppReportFramework implements ShoppReport {

	var $periods = true;

	function setup () {
		$this->setchart(array(
			'yaxis' => array('tickFormatter' => 'asMoney')
		));

		$this->chartseries( __('Shipping','Shopp'), array('column' => 'shipping') );
	}

	function query () {
		extract($this->options, EXTR_SKIP);

		$where = array();
		$where[] = "p.type = 'Shipped'";
		$where[] = "$starts < " . self::unixtime('o.created');
		$where[] = "$ends > " . self::unixtime('o.created');

		$where = join(" AND ",$where);
		$id = $this->timecolumn('o.created');
		$orders_table = ShoppDatabaseObject::tablename('purchase');
		$purchased_table = ShoppDatabaseObject::tablename('purchased');
		$query = "SELECT CONCAT($id) AS id,
							UNIX_TIMESTAMP(o.created) as period,
							COUNT(DISTINCT p.id) AS items,
							COUNT(DISTINCT o.id) AS orders,
							SUM(o.freight) as shipping
					FROM $purchased_table AS p
					LEFT JOIN $orders_table AS o ON p.purchase=o.id
					WHERE $where
					GROUP BY CONCAT($id)";

		return $query;
	}

	function columns () {
	 	return array(
			'period'=>__('Period','Shopp'),
			'orders'=>__('Orders','Shopp'),
			'items'=>__('Items','Shopp'),
			'subtotal'=>__('Subtotal','Shopp'),
			'shipping'=>__('Shipping','Shopp')
		);
	}

	static function orders ($data) { return intval($data->orders); }

	static function items ($data) { return intval($data->items); }

	static function subtotal ($data) { return money($data->subtotal); }

	static function shipping ($data) { return money($data->shipping); }

}