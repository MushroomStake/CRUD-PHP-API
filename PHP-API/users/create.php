<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include('function.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'POST') {
    $inputData = json_decode(file_get_contents("php://input"), true);
    
  
    if (empty($inputData)) {
        $inputData = $_POST;
    }
    
   
    if (isset($inputData['firstname'], $inputData['lastname'], $inputData['is_admin'])) {
      
        $inputData['is_admin'] = filter_var($inputData['is_admin'], FILTER_VALIDATE_BOOLEAN);
        
        $storeUser = storeUser($inputData);
        echo $storeUser; 
    } else {
        $data = [
            'status' => 422,
            'message' => 'Missing required fields',
        ];
        header("HTTP/1.0 422 Unprocessable Entity");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

?>