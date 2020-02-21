<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Slangword_model extends CI_Model
{
    private $_table = "tb_singkatan";

    public $kata_singkatan;
    public $kata_asli;

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }

 
    public function save()
    {
        $post = $this->input->post();
        $this->kata_singkatan = $post["slangword"];
        $this->kata_asli = $post["kata_asli"];
        $this->db->insert($this->_table, $this);
    }

}