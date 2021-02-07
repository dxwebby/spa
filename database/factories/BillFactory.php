<?php

use App\Bill;
use Faker\Generator as Faker;

$factory->define(Bill::class, function (Faker $faker) {
    $tender = new \Faker\Provider\Payment($faker);
    $text = new \Faker\Provider\en_US\Text($faker);
    $date = new \Faker\Provider\DateTime($faker);

    $bill_type = array("Bill", "Payment", "Other");
    $bill_index = array_rand($bill_type);
    
    return [
        'user_id' => 1,
        'cost' => rand(1, 252) / 10,      
        'bill_type' => $bill_type[$bill_index],
        'bill' => $faker->name,
        'bill_batch' => '012019',
        'payment_order' => rand(1,2),
        'payment_date' => $date->date(),
        'created_at' => $date->date($format = 'Y-d-m H:i:s'),
        'notes' =>  $text->realText($maxNbChars = 25),
        'payment_status' => '0'
    ];
});



