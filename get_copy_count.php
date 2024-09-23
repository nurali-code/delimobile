<?php
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
        $response['success'] = true;
        $response['demonstration'] = $data['demonstration'];
        $response['main_count'] = $data['main_count'];
        $response['copyCount'] = $data['copyCount'];
        $response['promocode'] = $data['promocode'];
    } else {
        $response['message'] = 'JSON file not found';
    }
} catch (Exception $e) {
    // Handle any errors
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Output the response as JSON
echo json_encode($response);
?>
