<?php
class Comments_api extends CI_Controller {

        

        public function __construct()
        {
                parent::__construct();
                $this->load->model('comments_model');
                $this->load->helper('url_helper');
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

                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    $data['news'] = $this->comments_model->get_comments($news_id);
                    $result = array(
                        'dota' => $data
                    );

                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'POST'){

                $foo = file_get_contents("php://input");
                $bar = json_decode($foo, true);

                if(json_last_error() !== JSON_ERROR_NONE){
                    //ERROR
                    $error_text = array(
                        'type' => 'Bad request',
                        'message' => 'JSON malformed'
                    );
                    $result = array(
                        'error' => $error_text
                    );

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else if($bar['text'] == null){
                    //ERROR
                    $error_text = array(
                        'type' => 'No comment written',
                        'message' => 'empty comment'
                    );
                    $result = array(
                        'error' => $error_text
                    );

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    
                    http_response_code(201);
                    header('Content-Type: application/json');

                    $data = array(
                        'text' => $bar['text']
                    );

                    $this->comments_model->set_comments($news_id, $data);
                    //return $this->db->insert('news', $data);
                }
            }


            else if($_SERVER['REQUEST_METHOD'] == 'PUT'){

                $foo = file_get_contents("php://input");
                $bar = json_decode($foo, true);

                if(json_last_error() !== JSON_ERROR_NONE){
                    //ERROR
                    $error_text = array(
                        'type' => 'Bad request',
                        'message' => 'JSON malformed'
                    );
                    $result = array(
                        'error' => $error_text
                    );

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    
                    http_response_code(200);
                    header('Content-Type: application/json');


                    $data = array(
                        'title' => $bar['title'],
                        'slug' => $bar['slug'],
                        'text' => $bar['text']
                    );

                    $this->db->where('id', $bar['id']);
                    $this->db->update('news', $bar); 
                }
                
            }
        }

        public function view($news_id, $id = NULL)
        {

            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $data['news'] = $this->comments_model->get_comments($news_id, $id);
                
                if($data['news']['id'] == ''){
                    $error_text = array(
                        'type' => 'Error',
                        'message' => 'No comment found'
                    );
                    $result = array(
                        'Message' => $error_text
                    );

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    $result = array(
                        'dota' => $data
                    );

                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
                $data['news'] = $this->comments_model->get_comments($news_id, $cursor, $offset, $id);
                
                if($data['news']['id'] == ''){
                    $error_text = array(
                        'type' => 'Error',
                        'message' => 'No comment found'
                    );
                    $result = array(
                        'Message' => $error_text
                    );

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
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

                    http_response_code(200);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
            }

        }
}