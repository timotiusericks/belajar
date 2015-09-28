<?php
class News_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_news($id = FALSE, $cursor = FALSE, $offset = FALSE)
		{
				if($cursor !== FALSE && $offset !== FALSE){
					$query = $this->db->get('news', $offset, $cursor);
					return $query->result_array();
				}
		        if ($id === FALSE)
		        {
		                $query = $this->db->get('news');
		                return $query->result_array();
		        }

		        $query = $this->db->get_where('news', array('id' => $id));
		        return $query->row_array();
		}

		

		public function set_news()
		{
		    $this->load->helper('url');

		    $slug = url_title($this->input->post('title'), 'dash', TRUE);

		    $data = array(
		        'title' => $this->input->post('title'),
		        'slug' => $slug,
		        'text' => $this->input->post('text')
		    );

		    return $this->db->insert('news', $data);
		}

		public function delete_news($id = FALSE)
		{

	        $query = $this->db->delete('news', array('id' => $id));
	        return $query->row_array();
		}
}