<?php
require_once 'vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];

if ( $_GET && !empty($_GET['url']) && filter_var($_GET['url'], FILTER_VALIDATE_URL) ) {
    $url            = $_GET['url'];
    $headers        = getallheaders();
    $requestHeaders = array_diff_key($headers, array_flip([ 'Host' ]));
    $request        = new Requests_Session($url);
    $body           = file_get_contents('php://input');

    // TODO: Get authorization header
    // TODO: Any other headers?
    $response = $request->request($url, $requestHeaders, !empty($body) ? $body : [], $method);

    if ( $method == "PUT" || $method == "PATCH" || ($method == "POST" && empty($_FILES)) ) {
        //$data_str = file_get_contents('php://input');
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
        //error_log($method.': '.$data_str.serialize($_POST).'\n',3, 'err.log');
    }
    elseif ( $method == "POST" ) {
        // TODO: Support file uploads
        /*

         $data_str = array();
        if ( !empty($_FILES) ) {
            foreach ($_FILES as $key => $value) {
                $full_path        = realpath($_FILES[ $key ]['tmp_name']);
                $data_str[ $key ] = '@' . $full_path;
            }
        }
        //error_log($method.': '.serialize($data_str+$_POST).'\n',3, 'err.log');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str + $_POST);
        */
    }

    // GET OUTPUT
    // --------------------------------

    foreach ($request->headers as $key => $header) {
        header($key . ': ' . $header);
    }

    http_response_code($response->status_code);

    echo $response->body;
}
else {
    echo $method;
    var_dump($_POST);
    var_dump($_GET);
    $data_str = file_get_contents('php://input');
    echo $data_str;
}