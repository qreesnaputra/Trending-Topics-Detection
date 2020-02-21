<?php

class Overview extends CI_Controller{
    public function __contsruct(){
        parent :: __contsruct();
    }

    public function index(){
        $this->load->view("admin/overview");
    }
}