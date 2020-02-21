<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prepro extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("crawling_model");
        $this->load->model("stopword_model");
        $this->load->model("singkatan_model");
        $this->load->library('form_validation');
    }

    public function index(){
        $this->load->view("admin/product/form_input_prepro");
    }

    public function post()
    {
        if(!(isset($_POST["start_date"]))){
            $this->session->set_flashdata('item', array('message' => 'Masukkan tanggal dan waktu yang diinginkan','class' => 'success'));
            redirect('admin/bngram'); 
        }

        $start_date = str_replace('/','-',$_POST["start_date"]);
        $start_time = $_POST["start_time"];
        $end_date = str_replace('/','-',$_POST["end_date"]);
        $end_time = $_POST["end_time"];
        $start = date_parse($start_date.' '.$start_time);
        $end = date_parse($end_date.' '.$end_time);
        $data["crawling"] = $this->crawling_model->getAllByTime($start_date,$start_time,$end_date,$end_time);

        // $unicode = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $data);
        // CaseFolding
        if(empty($data['crawling'])){
            $this->session->set_flashdata('item', array('message' => 'Data dengan tanggal tersebut tidak ditemukan','class' => 'success'));
            redirect('admin/bngram'); 
        }


        foreach($data["crawling"] as $dt ){
            $casefolding[] = strtolower($dt->text);
            $data["crawling"]["casefolding"][] = strtolower($dt->text);
        }

        // Cleansing
        foreach($casefolding as $clean){
        $clean = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $clean); //remove url
        $clean = preg_replace('/#([\w-]+)/i', '', $clean); //  #remove tag
        $clean = preg_replace('/@([\w-]+)/i', '', $clean); // #remove @someone
        $clean = str_replace('rt : ', '', $clean); // #remove RT
        $clean = str_replace(',', '  ', $clean); #remove , replace with space
        $clean = str_replace('.', '  ', $clean); #remove . replace with space
        $clean = preg_replace('/[^A-Za-z0-9\  ]/', '', $clean); 
        $clean = trim(preg_replace('/\s+/', ' ', $clean));
        $cleansing[]=$clean;
        $data["crawling"]["cleansing"][] = $clean;
    }

        // Tokenizing
        foreach($cleansing as $token){
            $tokenizing[] = explode(' ', $token);
            $data["crawling"]["tokenizing"][] = explode(' ', $token);
        }
        
        //Stopword 
        foreach($tokenizing as $stop){
            $stopword_all = $this->stopword_model->getAll();
            foreach($stopword_all as $word){
                $list[] = $word->stopword;
            }
            // var_dump($list);
            $finalWords = array_diff($stop, $list);
            $implode = implode(" ", $finalWords);
            $result_stopword[] = $implode;
            $data["crawling"]["stopword"][] = $implode;

        }

        // Singkatan
        foreach($result_stopword as $result_singkatan){
            $singkatan_all = $this->singkatan_model->getAll();

            foreach($singkatan_all as $singkatan){
                $string_singkatan = str_replace(' '.$singkatan->kata_singkatan.' ',' '.$singkatan->kata_asli.' ',$result_singkatan);
            }
            $list_singkatan[] = $string_singkatan;
            $data["crawling"]["singkatan"][] = $string_singkatan;

        }
        // var_dump($data);
        $this->load->view("admin/product/prepro", $data);
    }

    public function add()
    {
        $product = $this->product_model;
        $validation = $this->form_validation;
        $validation->set_rules($product->rules());

        if ($validation->run()) {
            $product->save();
            $this->session->set_flashdata('success', 'Berhasil disimpan');
        }

        $this->load->view("admin/product/new_form");
    }

    public function edit($id = null)
    {
        if (!isset($id)) redirect('admin/crawling');
       
        $product = $this->product_model;
        $validation = $this->form_validation;
        $validation->set_rules($product->rules());

        if ($validation->run()) {
            $product->update();
            $this->session->set_flashdata('success', 'Berhasil disimpan');
        }

        $data["crawling"] = $product->getById($id);
        if (!$data["crawling"]) show_404();
        
        $this->load->view("admin/product/edit_form", $data);
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
        
        if ($this->product_model->delete($id)) {
            redirect(site_url('admin/crawling'));
        }
    }
} 