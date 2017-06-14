<?php
require 'vendor/autoload.php';
// loading template engine
$loader = new Twig_Loader_Filesystem('view');
$twig = new Twig_Environment($loader);

// initializing pest, setting url of twitter api
$pest = new Pest('https://api.twitter.com');

if (!empty($_GET['search-query'])) {
    $response = json_decode(getSearchResult($pest, $_GET['search-query']), true);
    $tweets = jsonHandler($response);
    echo $twig->render('index.html', array(
        'response' => $tweets,
    ));
}

/**
 * Converts json to array
 */
function jsonHandler($response)
{
    $tweets = array();
    foreach ($response as $tweet) {
        if (is_array($tweet)) {
            foreach ($tweet as $item) {
                if (is_array($item)) {
                    $arr = array(
                        'text' => $item['text'],
                        'created_at' => $item['created_at']
                    );
                    array_push($tweets, $arr);
                }
            }
        }
    }
    return $tweets;
}

/**
 * Get token returns auth token for twitter api
 */
function getToken($pest)
{
    // require file with api key and api secret
    require 'credentials.php';

    // auth code in twitter format
    $auth = 'Basic ' . base64_encode($API_Key . ':' . $API_Secret);

    // initializing twitter query
    $query = '/oauth2/token';

    // make request to twitter api
    $response = $pest->post($query, [
        'grant_type' => 'client_credentials',
    ], [
        'Authorization' => $auth,
        'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
    ]);
    $result = json_decode($response, true);
    return $result['access_token'];
}

/**
 * Returns search result
 */
function getSearchResult($pest, $searchQuery)
{
    // auth code in twitter format
    $auth = 'Bearer ' . getToken($pest);

    // initializing twitter query
    $query = '/1.1/search/tweets.json?q=' . $searchQuery;

    // make request to twitter api
    $response = $pest->get($query, [], [
        'Authorization' => $auth,
    ]);
    return $response;
}