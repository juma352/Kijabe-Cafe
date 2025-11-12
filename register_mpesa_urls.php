<?php

require __DIR__ . '/vendor/autoload.php';

// Create a Guzzle client
$client = new GuzzleHttp\Client();

try {
    // Send a POST request to the register-urls endpoint
    $response = $client->post('http://127.0.0.1:8000/api/payments/register-urls', [
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]);

    // Get the response body
    $body = $response->getBody();

    // Decode the JSON response
    $data = json_decode($body, true);

    // Print the response
    print_r($data);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Response: " . $e->getResponse()->getBody()->getContents() . "\n";
}