<?php
class NewsController extends CI_Controller {

        

        public function __construct()
        {
                parent::__construct();
                $this->load->model('news_model');
                $this->load->helper('url_helper');
                $this->load->helper('function_helper');
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
                    'data' => $data
                );
                simple_json_write($result, 200);
            }

            else if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $temp_array = simple_json_read();

                if(json_last_error() !== JSON_ERROR_NONE){
                    simple_json_write(NULL, 400, "error");
                }
                else {
                    http_response_code(201);
                    header('Content-Type: application/json');

                    $data = array(
                        'title' => $temp_array['title'],
                        'slug' => $temp_array['slug'],
                        'text' => $temp_array['text']
                    );
                    return $this->db->insert('news', $data);
                }
            }

            else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
                $temp_array = simple_json_read();

                if(json_last_error() !== JSON_ERROR_NONE){
                    simple_json_write(NULL, 400, "error");
                }
                else {
                    http_response_code(200);
                    header('Content-Type: application/json');

                    $data = array(
                        'title' => $temp_array['title'],
                        'slug' => $temp_array['slug'],
                        'text' => $temp_array['text']
                    );

                    $this->db->where('id', $temp_array['id']);
                    $this->db->update('news', $temp_array); 
                }
            }
        }

        public function view($id = NULL)
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $data['news_item'] = $this->news_model->get_news($id);

                if (empty($data['news_item']))
                {   
                    $this->simple_json_write(NULL, 400, "error", 1);
                }
                else {
                    $result = array(
                        'data' => $data['news_item']
                    );

                    simple_json_write($result, 400);
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

                    simple_json_write($result, 400);
                }
                else {
                    simple_json_write(NULL, 400, "error", 1);
                }
            }
        }
}