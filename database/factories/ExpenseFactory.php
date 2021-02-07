<?php

use App\Expense;
use Faker\Generator as Faker;

$factory->define(Expense::class, function (Faker $faker) {        
    $tender = new \Faker\Provider\Payment($faker);
    $text = new \Faker\Provider\en_US\Text($faker);
    $date = new \Faker\Provider\DateTime($faker);

    $category = array("Groceries", "Restaurant", "Online Purchases", "Gas", "Maintenance", "Other");
    $category_index = array_rand($category);
    

    $random_date = "2019-01-" . rand(1,30) . " 21:36:11";
    
    return [
        'user_id' => 1,
        'amount' => rand(1, 1252) / 10,        
        'bill_id' => rand(7,10),
        'bill_batch' => '042019',        
        'description' => $text->realText($maxNbChars = 50),        
        'category' => $category[$category_index],
        'created_at' => $date->date($random_date),
    ];
});
 