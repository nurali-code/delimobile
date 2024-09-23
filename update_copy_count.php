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
        $data = array('copyCount' => 0, 'main_count' => 0, 'promocode' => '');
    }

    // Check if the action is copy
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'copy') {
        // Increment the copy count
        $data['demonstration'] += 1;
        if (isset($input['newUser']) && $input['newUser'] === true) {
            $data['copyCount'] += 1;
        }

        // Check if the copy count has reached or exceeded the main count
        if ($data['copyCount'] >= $data['main_count']) {
            // Prepare data for the external request
            $dataString = http_build_query([
                'copyCount' => $data['copyCount'],
                'promocode' => $data['promocode']
            ]);

            $url = "https://script.google.com/macros/s/AKfycbyu9IrRTCyA1nZb04s5gKGBkMYy7B9Pz9_UII1HXnymEc87eLLAEGj1zQ-SnHgQdGjR/exec";

            $options = [
                'http' => [
                    'header' => "Content-Type: text/plain;charset=utf-8\r\n",
                    'method' => 'POST',
                    'content' => $dataString,
                    'follow_location' => 1 // Follow redirects
                ]
            ];

            $context = stream_context_create($options);

            // Send the request and handle errors
            $externalResponse = @file_get_contents($url, false, $context);

            if ($externalResponse === FALSE) {
                $response['message'] = "Error occurred while sending request.";
            } else {
                $externalData = json_decode($externalResponse, true);

                // If external request succeeds, update copyCount and promocode
                if (isset($externalData['copyCount']) && isset($externalData['promocode'])) {
                    $data['copyCount'] = 0;
                    $data['main_count'] = $externalData['copyCount'];
                    $data['promocode'] = $externalData['promocode'];
                } else {
                    $response['message'] = 'Invalid response from external API';
                }
            }
        }

        // Save the updated count back to the JSON file
        if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT))) {
            // Set the response
            $response['success'] = true;
            $response['demonstration'] = $data['demonstration'];
            $response['main_count'] = $data['main_count'];
            $response['copyCount'] = $data['copyCount'];
            $response['promocode'] = $data['promocode'];
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
?>