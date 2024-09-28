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

function updateUser($userId, $userInput) {
    global $conn;

    $userId = intval($userId);
    

    $firstname = mysqli_real_escape_string($conn, $userInput['firstname']);
    $lastname = mysqli_real_escape_string($conn, $userInput['lastname']);
    $is_admin = isset($userInput['is_admin']) ? ($userInput['is_admin'] ? 1 : 0) : null;

    if (empty(trim($firstname))) {
        return error422('firstname is required.');
    } else if (empty(trim($lastname))) {
        return error422('lastname is required.');
    } else if (is_null($is_admin)) {
        return error422('is_admin is required.');
    } else {
        $query = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', is_admin = '$is_admin' WHERE id = $userId";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $data = [
                'status' => 200,
                'message' => 'User Updated Successfully',
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
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'PUT') {
  
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if (isset($inputData['id'])) {
        $response = updateUser($inputData['id'], $inputData);
        echo $response;
    } else {
        $data = [
            'status' => 422,
            'message' => 'Missing user ID',
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
