<?php
class api extends CI_Controller {

        

        public function __construct()
        {
                parent::__construct();
                $this->load->model('news_model');
                $this->load->helper('url_helper');
        }

        public function index()
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                if(isset($_GET['page'])){
                    $data['title'] = 'News archive';
                    $offset = $_GET['offset'];
                    $cursor = (($_GET['page'] - 1) * $offset) + 1;
                    $data['news'] = $this->news_model->get_news(FALSE, $cursor, $offset);
                    
                    $result = array(
                        'dota' => $data
                    );

                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    $data['news'] = $this->news_model->get_news();
                    
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

        public function view($id = NULL)
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $data['news_item'] = $this->news_model->get_news($id);

                if (empty($data['news_item']))
                {   
                    //ERROR
                    $error_text = array(
                        'type' => 'Client error',
                        'message' => 'No News found'
                    );
                    $result = array(
                        'error' => $error_text
                    );

                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    $result = array(
                        'dota' => $data['news_item']
                    );

                    header('Content-Type: application/json');
                    echo json_encode($result);
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

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                else {
                    //ERROR
                    $error_text = array(
                        'type' => 'Bad request',
                        'message' => 'No news found'
                    );
                    $result = array(
                        'error' => $error_text
                    );

                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
            }
        }



        public function create()
        {
            $this->load->helper('form');
            $this->load->library('form_validation');

            $data['title'] = 'Create a news item';

            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('text', 'text', 'required');

            if ($this->form_validation->run() === FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('news/create');
                $this->load->view('templates/footer');

            }
            else
            {
                $this->news_model->set_news();

                $data['news'] = $this->news_model->get_news();
                $data['title'] = 'News archive';
                
                $this->load->view('templates/header', $data);
                $this->load->view('news/index', $data);
                $this->load->view('templates/footer');
            }
        }
}