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


$sql = 'SELECT * FROM company_matching_settings';
$stmt = $db->prepare($sql);
$stmt->execute();

$rows = $stmt->fetchAll();

//The test data is fairly simple so there's a few assumptions we 
//can make here. Such as each row has only one postcode and one surveytype

foreach ($rows as $row) {

    $company_id = $row['company_id'];
    $postcode = json_decode($row['postcodes'])[0];

    $type_id = 0;
    switch ($row['type']) {
        case 'homebuyer':
            $type_id = 1;
            break;
        case 'building':
            $type_id = 2;
            break;
        case 'valuation':
            $type_id = 3;
            break;
    }

    foreach (json_decode($row['bedrooms']) as $bedroom) {

        $sql = 'INSERT INTO company_survey_types SET 
            company_id_fk = '.$company_id.', postcode = "'.$postcode.'", bedroom = '.$bedroom.',
            type = '.$type_id;
        $stmt = $db->prepare($sql);
        $stmt->execute();

    }

}


