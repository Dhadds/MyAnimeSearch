<?php
require_once '../secrets.php';

function makeGetRequest($url){
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

    $response = curl_exec($curl);

    if($response === false){
        echo 'Curl Error: ' . curl_error($curl);
        curl_close($curl);
        return null;
    }

    curl_close($curl);
    
    return json_decode($response, true);
}

function generateCodeVerifier() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~';
    $charactersLength = strlen($characters);
    $randomString = '';

    for($i = 0; $i < 128; $i++){
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function generateAccessTokenUrlForUser($redirectUri = '') {
    global $clientID;
    $codeChallenge = generateCodeVerifier();
    $state = bin2hex(random_bytes(32));

    $baseUrl = "https://myanimelist.net/v1/oauth2/authorize";
    $queryParams = [
        'response_type' => 'code',
        'client_id' => $clientID,
        'code_challenge' => $codeChallenge,
        'state' => $state,
        'redirect_uri' => $redirectUri,
        'code_challenge_method' => 'plain'
    ];

    $queryParams = array_filter($queryParams);
    $queryString = http_build_query($queryParams);

    return $baseUrl . '?' . $queryString;
}

$testUrl = generateAccessTokenUrlForUser();
echo($testUrl);
?>