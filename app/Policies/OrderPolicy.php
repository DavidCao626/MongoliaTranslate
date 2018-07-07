<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy extends Policy
{
    public function update(User $user, Order $order)
    {
        // return $order->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, Order $order)
    {
        return true;
    }
}
