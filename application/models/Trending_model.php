<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trending_model extends CI_Model
{
    private $_table = "tb_trending";
    
    public function getAll()
        {
            return $this->db->get($this->_table)->result();
        }

    public function getAllByTime($start_date,$start_time,$end_date,$end_time)
    {
        return $this->db->where('created_at BETWEEN "'. $start_date.' '.$start_time. '" and "'.$end_date.' '.$end_time.'"')->get($this->_table)->result();
    }

    // public $product_id;
    // public $name;
    // public $price;
    // public $image = "default.jpg";
    // public $description;

    // public function rules()
    // {
    //     return [
    //         ['field' => 'name',
    //         'label' => 'Name',
    //         'rules' => 'required'],

    //         ['field' => 'price',
    //         'label' => 'Price',
    //         'rules' => 'numeric'],
            
    //         ['field' => 'description',
    //         'label' => 'Description',
    //         'rules' => 'required']
    //     ];
    // }

    
    
//     public function getById($id)
//     {
//         return $this->db->get_where($this->_table, ["product_id" => $id])->row();
//     }

    public function save($data)
    {
        return $this->db->insert($this->_table, $data);
    }

    public function last()
    {
        return $this->db->order_by('id',"desc")
		->limit(1)
		->get('tb_trending')
		->row();
    }


//     public function update()
//     {
//         $post = $this->input->post();
//         $this->product_id = $post["id"];
//         $this->name = $post["name"];
//         $this->price = $post["price"];
//         $this->description = $post["description"];
//         $this->db->update($this->_table, $this, array('product_id' => $post['id']));
//     }

//     public function delete($id)
//     {
//         return $this->db->delete($this->_table, array("product_id" => $id));
//     }
}