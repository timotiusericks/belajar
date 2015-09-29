<?php
class Comments_api extends CI_Controller {

        

        public function __construct()
        {
                parent::__construct();
                $this->load->model('comments_model');
                $this->load->helper('url_helper');
        }

        public function simple_json_write($arr, $code, $error = NULL){
            if($error !== NULL){
                $error_text = array(
                    'type' => 'Client error',
                    'message' => 'JSON malformed'
                );
                $result = array(
                    'error' => $error_text
                );
            }
            else $result = $arr;

            http_response_code($code);
            header('Content-Type: application/json');
            echo json_encode($result);
        }


        public function index($news_id = NULL)
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){

                if(isset($_GET['page'])){
                    $offset = $_GET['offset'];
                    $cursor = $_GET['page'] * $offset - ($offset - ($offset - 1));
                    $data['news'] = $this->comments_model->get_comments($news_id, $cursor, $offset);
                    $result = array(
                        'page' => $_GET['page'],
                        'dota' => $data
                    );

                    $this->simple_json_write($result, 200);
                }
                else {
                    $data['news'] = $this->comments_model->get_comments($news_id);
                    $result = array(
                        'dota' => $data
                    );

                    $this->simple_json_write($result, 200);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'POST'){

                $foo = file_get_contents("php://input");
                $bar = json_decode($foo, true);

                if(json_last_error() !== JSON_ERROR_NONE){
                    $this->simple_json_write($bar, 400, 1);
                }
                else if($bar['text'] == null){
                    $this->simple_json_write($bar, 400, 1);
                }
                else {
                    
                    http_response_code(201);
                    header('Content-Type: application/json');

                    $data = array(
                        'text' => $bar['text']
                    );

                    $this->comments_model->set_comments($news_id, $data);
                }
            }
        }

        public function view($news_id, $id = NULL)
        {

            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $data['news'] = $this->comments_model->get_comments($news_id, FALSE, FALSE, $id);
                
                if($data['news'] == null){
                    $this->simple_json_write($data, 400, 1);
                }
                else {
                    $result = array(
                        'dota' => $data
                    );

                    $this->simple_json_write($result, 200);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
                $data['news'] = $this->comments_model->get_comments($news_id, FALSE, FALSE, $id);
                
                if($data['news']== null){
                    $this->simple_json_write($data, 400, 1);
                }
                else {
                    $this->comments_model->delete_comments($news_id, $id);
                    $error_text = array(
                        'type' => 'Success',
                        'message' => 'Comment deleted'
                    );
                    $result = array(
                        'Message' => $error_text
                    );

                    $this->simple_json_write($result, 200);
                }
            }
        }
}