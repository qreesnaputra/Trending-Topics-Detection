<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stopword_model extends CI_Model
{
    private $_table = "tb_stopword";

    public $stopword;

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }


    public function save()
    {
        $post = $this->input->post();
        $this->stopword = $post["stopword"];
        $this->db->insert($this->_table, $this);
    }

}