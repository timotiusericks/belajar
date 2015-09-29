<?php
function simple_json_write($arr = NULL, $code, $error = FALSE, $error_code = NULL){
    if($error !== FALSE){
        if($error_code != NULL){
            if($error_code == 1){
                $message = 'No news found';
            }
            else if($error_code == 2){
                $message = 'No comments found';
            }
        }
        else {
            $message = 'JSON malformed';
        }
        $error_text = array(
            'type' => 'Client error',
            'message' => $message
        );
        $result = array(
            'error' => $error_text
        );
    }
    else {
        $result = $arr;
    }

    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($result);
}

function simple_json_read(){
    return json_decode(file_get_contents("php://input"), true);
}
?>