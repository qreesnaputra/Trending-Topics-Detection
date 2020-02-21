<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Slangword extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("slangword_model");
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data["slangword"] = $this->slangword_model->getAll();
        $this->load->view("admin/product/slangword", $data);
    }
 
    public function add()
    {
        $this->load->view("admin/product/form_slangword");
    }

    public function save()
    {
        $this->slangword_model->save();
        $data["slangword"] = $this->slangword_model->getAll();
        $this->load->view("admin/product/slangword", $data);

    }

} 