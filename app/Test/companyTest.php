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


    $c = new App\Model\Company($db , 7);

    echo ($c->getName().PHP_EOL);
    echo ($c->getCredits().PHP_EOL);

    $c->decrementCredits(4);

    echo ($c->getCredits().PHP_EOL);


    //var_dump($c);


