<?php
// update_copy_count.php

header("Content-Type: application/json; charset=UTF-8");

// Path to the JSON file
$jsonFile = 'copy_count.json';

// Initialize the response array
$response = array('success' => false, 'message' => '', 'copyCount' => 0);

try {
    // Read the current copy count from the JSON file
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
    } else {
        $data = array('count' => 0);
    }

    // Check if the action is copy
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input['action'] === 'copy') {
        // Increment the copy count
        $data['count'] += 1;

        // Save the updated count back to the JSON file
        if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT))) {
            // Set the response
            $response['success'] = true;
            $response['copyCount'] = $data['count'];
        } else {
            $response['message'] = 'Failed to write to JSON file';
        }
    } else {
        $response['message'] = 'Invalid action';
    }
} catch (Exception $e) {
    // Handle any errors
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Output the response as JSON
echo json_encode($response);
