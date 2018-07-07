<?php

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $orders = factory(Order::class)->times(50)->make()->each(function ($order, $index) {
            if ($index == 0) {
                // $order->field = 'value';
            }
        });

        Order::insert($orders->toArray());
    }

}

