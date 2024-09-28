<?php

require '../inc/dbcon.php';

function error422($message) {
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function deleteUser($userId) {
    global $conn;


    $userId = intval($userId); 

    if ($userId <= 0) {
        return error422('Invalid user ID.');
    }

    $query = "DELETE FROM users WHERE id = $userId";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 200,
            'message' => 'User Deleted Successfully',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'DELETE') {
    // Get the user ID from the request body
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if (isset($inputData['id'])) {
        $response = deleteUser($inputData['id']);
        echo $response;
    } else {
        $data = [
            'status' => 422,
            'message' => 'Missing user ID',
        ];
        header("HTTP/1.0 422 Unprocessable Entity");
        echo json_encode($data);
    }
}


?>
