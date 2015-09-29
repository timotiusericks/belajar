<?php
class api extends CI_Controller {

        

        public function __construct()
        {
                parent::__construct();
                $this->load->model('news_model');
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

        public function index()
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                if(isset($_GET['page'])){
                    $data['title'] = 'News archive';
                    $offset = $_GET['offset'];
                    $cursor = (($_GET['page'] - 1) * $offset);
                    $data['news'] = $this->news_model->get_news(FALSE, $cursor, $offset);
                }
                else {
                    $data['news'] = $this->news_model->get_news();
                }

                $result = array(
                    'dota' => $data
                );
                $this->simple_json_write($result, 200);
            }

            else if($_SERVER['REQUEST_METHOD'] == 'POST'){

                $foo = file_get_contents("php://input");
                $bar = json_decode($foo, true);

                if(json_last_error() !== JSON_ERROR_NONE){
                    $this->simple_json_write($bar, 400, 1);
                }
                else {
                    http_response_code(201);
                    header('Content-Type: application/json');

                    $data = array(
                        'title' => $bar['title'],
                        'slug' => $bar['slug'],
                        'text' => $bar['text']
                    );
                    return $this->db->insert('news', $data);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'PUT'){

                $foo = file_get_contents("php://input");
                $bar = json_decode($foo, true);

                if(json_last_error() !== JSON_ERROR_NONE){
                    $this->simple_json_write($bar, 400, 1);
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

        public function view($id = NULL)
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $data['news_item'] = $this->news_model->get_news($id);

                if (empty($data['news_item']))
                {   
                    $this->simple_json_write($bar, 400, 1);
                }
                else {
                    $result = array(
                        'dota' => $data['news_item']
                    );

                    $this->simple_json_write($result, 400);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
                $data['news_item'] = $this->news_model->get_news($id);
                if(isset($data['news_item']) && $data['news_item'] != NULL){
                    $this->news_model->delete_news($id);

                    $error_text = array(
                        'type' => 'Success',
                        'message' => 'News deleted'
                    );
                    $result = array(
                        'Message' => $error_text
                    );

                    $this->simple_json_write($result, 400);
                }
                else {
                    $this->simple_json_write($data['news_item'], 400, 1);
                }
            }
        }
}