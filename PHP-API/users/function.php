<?php

require '../inc/dbcon.php';

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function storeUser($userInput){
    global $conn;
    
    $firstname = mysqli_real_escape_string($conn, $userInput['firstname']);
    $lastname = mysqli_real_escape_string($conn, $userInput['lastname']);
    $is_admin = $userInput['is_admin'] ? 1 : 0;  // Convert boolean to integer

    if (empty(trim($firstname))) {
        return error422('firstname is required.');
    } else if (empty(trim($lastname))) {
        return error422('lastname is required.');
    } else if (!isset($userInput['is_admin'])) { // Check if is_admin is set
        return error422('is_admin is required.');
    } else {
        $query = "INSERT INTO users (firstname, lastname, is_admin) VALUES ('$firstname', '$lastname', '$is_admin')";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            $data = [
                'status' => 201,
                'message' => 'User Created Successfully',
            ];
            header("HTTP/1.0 201 Created");
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

function getUserList(){
    global $conn;
    $query = "SELECT * FROM users";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            
            // Convert is_admin from tinyint to boolean
            foreach ($res as &$user) {
                $user['is_admin'] = (bool)$user['is_admin'];
            }
            
            $data = [
                'status' => 200,
                'message' => 'User List Fetched Successfully',
                'data' => $res 
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No User Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

?>
