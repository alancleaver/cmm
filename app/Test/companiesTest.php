<?php

require __DIR__ . '/../../bootstrap/init.php';
    
$db = new \PDO(
    sprintf(
        '%s:host=%s;port=%d;dbname=%s', 
        $_ENV['DB_TYPE'], 
        $_ENV['DB_HOST'], 
        $_ENV['DB_PORT'], 
        $_ENV['DB_NAME']
    ),
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD']
);


$companies = new App\Model\Companies($db);

$filtered_companies = $companies->randomize()->limit(3)->filterByPostcode(['B', 'BS'])->filterByBedrooms([3])->filterByTypes([2]);


/*
$companies->randomize();
$companies->limit(3);
$companies->filterByPostcode(['B', 'BS']);
$companies->filterByBedrooms([3]);
$companies->filterByTypes([2]);
*/

foreach ($filtered_companies as $company) {
    var_dump($company);
}












