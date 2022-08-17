<?php

namespace Drupal\broodmodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class BackOfficeController extends ControllerBase
{
    public static function getOrders()
    {
        $query = \Drupal::database()->query("SELECT * FROM orders")->fetchAll(\PDO::FETCH_OBJ);
        $result = $query;

        $data = [];

        foreach ($result as $row) {
            $data[] = [
                'id' => $row->id,
                'first_name' => $row->first_name,
                'last_name' => $row->last_name,
                'phone_number' => $row->phone_number,
                'bread_type' => $row->bread_type,
                'pastry_type' => $row->pastry_type,
                'order_date' => $row->order_date
            ];
        }
        $header = array('Order ID', 'First Name', 'Last Name', 'Phone Number', 'Bread Type', 'Pastry Type', 'Order Date');
        $build['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $data
        ];
        return [
            $build,
            '#title' => 'Orders for Bakkerij Peeters.'
        ];
    }
}