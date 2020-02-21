<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stopword extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("stopword_model");
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data["stopword"] = $this->stopword_model->getAll();
        $this->load->view("admin/product/stopword", $data);
    }

    public function add()
    {
        $this->load->view("admin/product/form_stopword");
    }

    public function save()
    {
        $this->stopword_model->save();
        $data["stopword"] = $this->stopword_model->getAll();
        $this->load->view("admin/product/stopword", $data);
    }

} 