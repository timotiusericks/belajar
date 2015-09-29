<?php
class CommentsController extends CI_Controller {

        

        public function __construct()
        {
                parent::__construct();
                $this->load->model('comments_model');
                $this->load->helper('url_helper');
                $this->load->helper('function_helper');
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
                        'data' => $data
                    );

                    simple_json_write($result, 200);
                }
                else {
                    $data['news'] = $this->comments_model->get_comments($news_id);
                    $result = array(
                        'data' => $data
                    );

                    simple_json_write($result, 200);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $temp_array = simple_json_read();

                if(json_last_error() !== JSON_ERROR_NONE){
                    simple_json_write($temp_array, 400, "error");
                }
                else if($temp_array['text'] == null){
                    simple_json_write($temp_array, 400, "error");
                }
                else {
                    
                    http_response_code(201);
                    header('Content-Type: application/json');

                    $data = array(
                        'text' => $temp_array['text']
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
                    simple_json_write(NULL, 400, "error", 2);
                }
                else {
                    $result = array(
                        'data' => $data
                    );

                    simple_json_write($result, 200);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
                $temp_array = simple_json_read();
                
                if(json_last_error() !== JSON_ERROR_NONE){
                    simple_json_write($temp_array, 400, "error");
                }
                else if($temp_array['text'] == null){
                    simple_json_write($temp_array, 400, "error");
                }
                else {
                    
                    http_response_code(201);
                    header('Content-Type: application/json');

                    $data = array(
                        'text' => $temp_array['text']
                    );

                    $this->comments_model->update_comments($news_id, $id, $data);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
                $data['news'] = $this->comments_model->get_comments($news_id, FALSE, FALSE, $id);
                
                if($data['news']== null){
                    simple_json_write(NULL, 400, "error", 2);
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

                    simple_json_write($result, 200);
                }
            }
        }
}