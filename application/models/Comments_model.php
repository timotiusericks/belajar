<?php
class Comments_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_comments($news_id, $cursor = FALSE, $offset = FALSE, $id = FALSE)
		{
			$this->db->select('*');
			//
			
			//$this->db->join('news', 'comments.news_id = news.id', 'inner');

				/*
				$this->db->where('news_id', $news_id);
                $query = $this->db->get();
                return $query->result_array();
				*/
				if($cursor !== FALSE && $offset !== FALSE){
					$query = $this->db->get('comments', $offset, $cursor);
					return $query->result_array();
				}
		        if ($id === FALSE)
		        {
		                $this->db->where('news_id', $news_id);
		                $this->db->from('comments');
		                $query = $this->db->get();
		                return $query->result_array();
		        }

		        $this->db->where('news_id', $news_id);
		        $this->db->where('id', $id);
		        $query = $this->db->get();
		        return $query->row_array();
		        
		    
		}

		public function set_comments($news_id, $text)
		{
		    $this->load->helper('url');

		    $data = array(
		        'news_id' => $news_id,
		        'text' => $text['text']
		    );

		    return $this->db->insert('comments', $data);
		}

		public function delete_comments($news_id, $id)
		{
			$this->db->where('news_id', $news_id);
		    $this->db->where('id', $id);

	        $query = $this->db->delete('comments');
		}
}