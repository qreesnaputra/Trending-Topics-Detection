<?php
defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(0);
class Bngram extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("crawling_model");
        $this->load->model("stopword_model");
        $this->load->model("singkatan_model");
        $this->load->model("boost_model");
        $this->load->model("Trending_model");
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view("admin/product/form_input_bngram");
    }

    public function post()
    {
        // handling masukkan
        if(!(isset($_POST["start_date"]))){
            $this->session->set_flashdata('item', array('message' => 'Masukkan tanggal dan waktu yang diinginkan','class' => 'success'));
            redirect('admin/bngram'); 
        }
        $start_date = str_replace('/','-',$_POST["start_date"]);
        $start_time = $_POST["start_time"];
        $end_date = str_replace('/','-',$_POST["end_date"]);
        $end_time = $_POST["end_time"];
        // end handling masukkan
        
        // konversi date ke date time
        $start = date_parse($start_date.' '.$start_time);
        $end = date_parse($end_date.' '.$end_time);
        // end konversi date ke date time

        // membaca data crawling sesuai tanggal dan waktu yang sudah ditentukan
        $data["crawling"] = $this->crawling_model->getAllByTime($start_date,$start_time,$end_date,$end_time);
        // membaca data crawling sesuai tanggal dan waktu yang sudah ditentukan

        // membaca data boost
        $boost = $this->boost_model->getAll();
        // end membaca data boost

        // memasukkan date time tweet dibuat ke variabel baru
        foreach($data["crawling"] as $dt ){
            $created_at[] = $dt->created_at;
        }
        // end memasukkan date time tweet dibuat ke variabel baru

        // handling jika data tidak ada
        if(!(isset($created_at))){
            $this->session->set_flashdata('item', array('message' => 'Data dengan tanggal tersebut tidak ditemukan','class' => 'success'));
            redirect('admin/bngram'); 
        }
        // end handling jika data tidak ada

        // menentukan waktu awal dari tweet yang tersedia dan menentukan waktu 2 menit kedepan
        $current_date = $created_at[0];
        $future_date =date('Y-m-d H:i:s', strtotime('+2 minutes', strtotime($current_date)));
        // end menentukan waktu awal dari tweet yang tersedia dan menentukan waktu 2 menit kedepan

        // membagi tweet setiap 2 menit sekali
        $i=0;
        $casefolding= array();
        $casefolding[$i]['created_at'] = "";
        foreach($data["crawling"] as $dt ){
            if( $dt->created_at >= $current_date && $dt->created_at <= $future_date){
                if($casefolding[$i]['created_at'] == null){
                    $casefolding[$i]['created_at'] = $dt->created_at;
                }
                $casefolding[$i][] = strtolower($dt->text);
            }
            else{
                $current_date = $future_date;
                $future_date =date('Y-m-d H:i:s', strtotime('+2 minutes', strtotime($current_date)));
                $i++;
                $casefolding[$i]['created_at'] = $dt->created_at;
                $casefolding[$i][] = $dt->text;

            }
        }
        // end membagi tweet setiap 2 menit sekali

        // memasukkan data tweet 2 menit sekali ke dalam variabel baru 
        foreach($casefolding as $dta_casefolding){
            $time_slot[] = $dta_casefolding['created_at'];
        }
        // end memasukkan data tweet 2 menit sekali ke dalam variabel baru 

        // membersihkan data tweet ( preprocessing )
        foreach($casefolding as $clean){
            unset($cleansing);
            foreach($clean as $cl){
                $cl = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $cl); //remove url
                $cl = preg_replace('/#([\w-]+)/i', '', $cl); //  #remove tag
                $cl = preg_replace('/@([\w-]+)/i', '', $cl); // #remove @someone
                $cl = str_replace('rt : ', '', $cl); // #remove RT
                $cl = str_replace('RT', ' ', $cl);
                $cl = str_replace(',', '  ', $cl);
                $cl = str_replace('.', '  ', $cl);
                $cl = preg_replace('/[^A-Za-z0-9\  ]/', '', $cl);

                $words = explode(' ', $cl);
                unset($result);
                foreach ($words as $word) {
                    if(strlen($word) >= 2) {
                        $result[] = $word; // push word into result array
                    }
                }

                if(isset($result)){
                    $cl = implode(' ', $result); // re-create the string
                }
                // $clean = trim(preg_replace('/\s+/', ' ', $clean));
                $cleansing[]=$cl;        
            }
            $data["crawling"]["cleansing"][] = $cleansing;
        }
        // end membersihkan data tweet ( preprocessing )

        // Tokenizing
        foreach($data['crawling']['cleansing'] as $token){
            unset($list);
            foreach($token as $clean){
                $tokenizing[] = explode(' ', $clean);
                $list[] = explode(' ', $clean);    
            }
            $data["crawling"]["tokenizing"][] = $list;
        }
        // end Tokenizing
        
        //Stopword 
        unset($list);
        $stopword_all = $this->stopword_model->getAll();
        foreach($data['crawling']['tokenizing'] as $stop){
            unset($result_stopword);
            foreach($stop as $cl_stop){
                foreach($stopword_all as $word){
                    $list[] = $word->stopword;
                }
                $finalWords = array_diff($cl_stop, $list);
                $implode = implode(" ", $finalWords);
                $result_stopword[] = $implode;
            }
            $data["crawling"]["stopword"][] = $result_stopword;

        }
        // end Stopword 

        // Singkatan
        $singkatan_all = $this->singkatan_model->getAll();
        foreach($data['crawling']['stopword'] as $arr_singkatan){
            unset($list_singkatan);
            foreach($arr_singkatan as $list_kata){
                foreach($singkatan_all as $singkatan){
                    unset($hasil_kata_singkatan);
                    $temp_kata = explode(' ',$list_kata);
                    foreach($temp_kata as $kata){
                        if(strlen($singkatan->kata_singkatan) == strlen($kata)){
                            $hasil_kata_singkatan[] = str_replace($singkatan->kata_singkatan,$singkatan->kata_asli,$kata);
                        }
                        else{
                            $hasil_kata_singkatan[] = $kata;
                        }
                    }
                    $string_singkatan = implode(' ',$hasil_kata_singkatan);
                    $string_singkatan = str_replace('  ','', $string_singkatan);

                }
                $list_singkatan[] = $string_singkatan;
            }

            $data["crawling"]["singkatan"][] = $list_singkatan;
        }
        // end Singkatan

        // memasukkan data singkatan kedalam variabel baru 
        foreach($data['crawling']['singkatan'] as $dta){
            for($i =0;$i < sizeof($dta) ;$i++){
                if($i != 0){
                    $list_tweet_cluster[] = $dta[$i];
                }
            }
        }
        // end memasukkan data singkatan kedalam variabel baru 

        // membuat kata menjadi bigram
        $i=0;
        foreach($data['crawling']['singkatan'] as $dt){
            $i++;
            unset($dt_bingram);
            foreach($dt as $list_ngram){
                $bingram = ngrams(explode(' ',$list_ngram));
                $token = tokenize(implode(" ",$dt));    
                $dt_bingram[] = $bingram;
            }
            $list_bingram[] = $dt_bingram;
            $list_token[] = $token;
        }
        // end membuat kata menjadi bigram

        // membuat ngram menjadi per time slot
        foreach($list_bingram as $dta){
            $tmp_list_bngram =null;
            unset($new_array_bgram);
            foreach($dta as $dt){
                foreach($dt as $t){
                    $new_array_bgram[] = $t;
                }
            }
            $new_list_bngram[] = $new_array_bgram;
        }
        // end membuat ngram menjadi per time slot

        // handling error jika bigram tidak didapatkan
        if($new_list_bngram[0] == null){
                redirect('admin/bngram'); 
           }
        // end handling error jika bigram tidak didapatkan

        // membenarkan key array
        foreach($new_list_bngram as $dta){
            foreach($dta as $dt){
                unset($dta[0]);
                $new_dta = array_values($dta);
            }
            $list_dta[] = $new_dta;
        }
        // end membenarkan key array

        // membuat kata menjadi bigram dan memasukkan per time slot
        $i=0;
        foreach($list_dta as $pre_ngram){
            $bigram = $pre_ngram;
            $trigram = ngrams($pre_ngram);    
            $list_bigram[$i][] = $bigram;
            $list_bigram[$i][] = $time_slot[$i];
            $list_tigram[$i][] = $trigram;
            $i++;
        }
        // end membuat kata menjadi bigram dan memasukkan per time slot

        // memasukkan bigram kedalam variabel baru
        $data['list_bngram'] = $list_bigram;
        // end memasukkan bigram kedalam variabel baru

        // membuat list ngram keseluruhan
        $merge_list_seluruh_bngram = '';
        foreach($list_bigram as $dt) {
            $list_seluruh_bngram[] = implode(', ',$dt[0]);
        }

        foreach($list_seluruh_bngram as $dt){
            $merge_list_seluruh_bngram .= $dt;
        }
        // end membuat list ngram keseluruhan

        // menghitung df ke - i dan keseluruhan time slot
        $count_1_timeslot = [];
        $t =0;
        foreach($data['list_bngram'] as $dt){
            $compare_data = implode(', ',$dt[0]);
            unset($dt[1]);
            foreach($dt as $dta){
                unset($count_all_timeslot);
                unset($count_1_timeslot);
                $count_1_timeslot= [];
                foreach($dta as $data_string){
                    $count_1_timeslot[] = substr_count($compare_data,$data_string);
                }
                $list_count_1_timeslot[] = $count_1_timeslot;
            }
            $t = $t +1;
        }
        // end menghitung df ke - i dan keseluruhan time slot

        // mapping df ke - i ke variabel baru
        $i = 0;
        foreach($data['list_bngram'] as $count_bngram){
            $data['list_bngram'][$i][2] = $list_count_1_timeslot[$i];
            $i++;
        }
        // end mapping df ke - i ke variabel baru

        // menghitung df ke - i secara keseluruhan
        $data['t']= $t;
        foreach($data['list_bngram'] as $count_double){
            for($i=0 ;$i < sizeof($count_double[0])-1; $i++){
                for($j=$i+1 ;$j < sizeof($count_double[0]); $j++){
                    if(array_key_exists($j,$count_double[0]) && array_key_exists($i,$count_double[0])){
                        if($count_double[0][$i] == $count_double[0][$j]){
                            unset($count_double[0][$j]);
                            unset($count_double[2][$j]);
                            unset($count_double[3][$j]);
                        }
                    }
                }

            }
            $new_count_double[] = array_values($count_double);
        }
        // end menghitung df ke - i secara keseluruhan

        // menghitung boost kata ngram
        $data['list_bngram'] = $new_count_double;
        $h=0;
        foreach($data['list_bngram'] as $dta){
            $i=0;
            foreach($dta[0] as $dt){
                foreach($boost as $bs){
                    if($dt == $bs->kata){
                        $data['list_bngram'][$h][3][$i] = 1.5;
                        break;
                    }else{
                        $data['list_bngram'][$h][3][$i] = 1;
                    }
                }
                $i++;   
            }
            $h++;
        }
        // end menghitung boost kata ngram

        // memperbaiki key array
        $i=0;
        foreach($data['list_bngram'] as $dta){
            $j=0;
            foreach($dta as $dt){
                if($j != 1){
                $data['list_bngram'][$i][$j]= array_values($dt);
                }
                $j++;
            }
            $i++;
        }
        // end memperbaiki key array
        
        $h=0;
        foreach($data['list_bngram'] as $dta){
            $i=0;
            foreach($dta[0] as $dt){
                if($h == 0){
                    $div = log10((0/$data['t'])+1) + 1;
                    $sum = ($data['list_bngram'][$h][2][$i]+1) / $div * $data['list_bngram'][$h][3][$i];
                    $data['list_bngram'][$h][4][$i] = $sum;
                }
                else{
                    $count=0;
                    for($j=$h-1;$j >= 0; $j--){
                        for($k=0;$k < sizeof($data['list_bngram'][$j][0]); $k++){
                            if($data['list_bngram'][$h][0][$i] == $data['list_bngram'][$j][0][$k]){
                                $count += $data['list_bngram'][$j][2][$k];
                            }
                        } 
                    }
                    $div = (log10(($count/$data['t'])+1) + 1);
                    $sum = ($data['list_bngram'][$h][2][$i]+1) / $div * $data['list_bngram'][$h][3][$i];
                    $data['list_bngram'][$h][4][$i] = $sum;
                    $data['list_bngram'][$h][5][$i] = $count;
                }
                $i++;
            }
            $h++;
        }

        // memasukan data ngram kedalam variabel baru
        foreach($data['list_bngram'] as $dt){
            foreach($dt[0] as $dta){
                $list_cluster[] = $dta;
            }
        }

        // mengecek ngram ke seluruh data
        foreach($data['list_bngram'] as $dta){
            if(array_key_exists(4,$dta)){
                foreach($dta[4] as $dt){
                    $dfidf[1][]=$dt;
                }    
            }
        }

        foreach($data['list_bngram'] as $dta){
            foreach($dta[0] as $dt){
                $dfidf[0][]=$dt;
            }
        }

        // menentukan max dfidf
        $i=0;
        $temp_max =0;
        foreach($dfidf[1] as $dt){
            if($temp_max < $dt){
                $temp_max = $dt;
                unset($max_dfidf);
                $max_dfidf[] =$dfidf[0][$i];
            }
            elseif($temp_max == $dt){
                $max_dfidf[] = $dfidf[0][$i];
            }
            $i++;
        }
        
        // menghilangkan data duplikat pada array
        $unique_list_cluster = array_values(array_unique($list_cluster));

        // memunculkan kata ngram
        foreach($unique_list_cluster as $dt){
            $temp = explode(' ',$dt);
            $fix_list[] = $temp[0];
        }

        // menghitung jarak average
        $dt_2d =[];
        for($i=0; $i < sizeof($fix_list); $i++){
            for($j=0; $j< sizeof($list_tweet_cluster) ; $j++){
                $explode_tweet_cluster = explode(' ',$list_tweet_cluster[$j]);
                if(in_array($fix_list[$i],$explode_tweet_cluster)){
                    $dt_2d[$i][$j] = 1;
                }
                else{
                    $dt_2d[$i][$j] = 0;
                }
            }
        }

        foreach($dt_2d as $dt){
            $tot_n_ngram[] = array_sum($dt);
        }

        // update nilai average
        $data['dt_2d']=$dt_2d;
        for($i=0; $i < sizeof($data['dt_2d']); $i++){
            for($j=0; $j < sizeof($data['dt_2d']) ; $j++){
                $temp_dt_min = [$tot_n_ngram[$i],$tot_n_ngram[$j]];
                $count =0;
                for($k=0;$k < sizeof($data['dt_2d'][$i]) ;$k++){
                    if($data['dt_2d'][$i][$k] == 1 && $data['dt_2d'][$j][$k] == 1){
                        $count++;
                    }
                }
                $dt_dman_cluster[$i][$j] = 1 - ($count/min($temp_dt_min));
            }
        }

        // mencari nilai cluster
        $data['dt_dman_cluster'] = $dt_dman_cluster;
        for($i=0 ; $i < sizeof($data['dt_dman_cluster']) ; $i++){
            for($j=0 ; $j < sizeof($data['dt_dman_cluster']) ; $j++){
                for($k=0 ; $k < sizeof($data['dt_dman_cluster']) ; $k++){
                    if(array_key_exists($i,$data['dt_dman_cluster'])){
                        $temp_arr = [$data['dt_dman_cluster'][$i][$j],$data['dt_dman_cluster'][$i][$k]];
                        $average = array_sum($temp_arr)/sizeof($temp_arr);
                        $key_cluster = $data['dt_dman_cluster'][$i][$j];
                        $cluster[$average][] = $j;
                        $cluster[$average][] = $k;
                        unset($data['dt_dman_cluster'][$k]);
                        unset($data['dt_dman_cluster'][$j]);
                    }
                }   
            }        
        } 

        // menghilangkan kata duplikat pada cluster
        foreach($cluster as $cs){
            $new_cluster[] = array_values(array_unique($cs));
        }

        // memasukkan kata ngram dalam cluster
        $i=0;
        foreach($new_cluster as $dt){
                foreach($dt as $dta){
                    $nama_tweet_cluster[$i][] = $fix_list[$dta];
                }
                $i++;
        }

        // menyusun max dfidf menjadi kalimat
        foreach($max_dfidf as $dt){
            $temp_arr = explode(' ',$dt);
            $new_max_dfidf[] = $temp_arr[0];
            $new_max_dfidf[] = $temp_arr[1];
            $i++;
        }

        // menghitung kesamaan maxdfidf 
        $temp_count=0;
        foreach($nama_tweet_cluster as $dt){
            $compare_cluster = array_intersect($new_max_dfidf,$dt);
            if(sizeof($compare_cluster) > $temp_count){
                $hasil_akhir = $dt;
                $temp_count = sizeof($compare_cluster);
            }
        }

        // error handling jika cluster tidak ditemukan
        if(!(isset($hasil_akhir))){
            $hasil_akhir = $nama_tweet_cluster[0];
        }

        // dd($hasil_akhir);
        $hasil_akhir = array_map('trim',array_filter($hasil_akhir));

        foreach($hasil_akhir as $dt){
            $i=0;
            foreach($dfidf[0] as $dta){
                $ex_dta = explode(' ',$dta);
                $temp[] = $ex_dta[0].' == '.$dt;
                if($ex_dta[0] == $dt){
                    $new_hasil_akhir[$ex_dta[0]] = $dfidf[1][$i];
                }
                $i++;
            }
        }

        // mengubah hasil akhir menjadi kalimat
        $insert_trending = implode(' ',array_unique($hasil_akhir));
        $data['cluster_trending'] = $insert_trending;
        arsort($new_hasil_akhir);

        $i=0;
        foreach($new_hasil_akhir as $key=>$value){
            if($i < 10){
                $top_10[] = $key;
            }
            $i++;
        }

        $data_tweet = array(
            'tweet' => $insert_trending,
            );

        $check = $this->Trending_model->save($data_tweet);

        $data['hasil_akhir'] = array_unique($max_dfidf);
        $data['hasil_akhir'] = implode(' ',$data['hasil_akhir']);
        $ex_hasil_akhir = array_unique(explode(' ',$data['hasil_akhir']));
        $data['hasil_akhir'] = implode(' ',$ex_hasil_akhir);

        $recent_same =0;
        foreach($data['crawling'] as $dt){
            if(isset($dt->text)){
            $text[] = $dt->text;
            $ex_tweet = explode(' ', $dt->text);
            $same = array_intersect($ex_hasil_akhir,$ex_tweet);
                if($recent_same < sizeof($same)){
                    $recent_same = sizeof($same);
                    $relevant_tweet = $dt->text;
                }
            }
        }
        $data['relevant_tweet'] = $relevant_tweet;
        $this->load->view("admin/product/result_bngram",$data);
    }

//     public function post_test()
//     {

//         $start_date = str_replace('/','-',$_POST["start_date"]);
//         $start_time = $_POST["start_time"];
//         $end_date = str_replace('/','-',$_POST["end_date"]);
//         $end_time = $_POST["end_time"];
//         $start = date_parse($start_date.' '.$start_time);
//         $end = date_parse($end_date.' '.$end_time);
//         $data["crawling"] = $this->crawling_model->getAllByTime($start_date,$start_time,$end_date,$end_time);
//         // dd($data["crawling"]);
//         $boost = $this->boost_model->getAll();
//         dd($boost);
//         foreach($data["crawling"] as $dt ){
//             // dd($created_at);

//             $created_at[] = $dt->created_at;
//         }
//         // dd($created_at);
//         if($created_at == null){

//         }
//         $current_date = $created_at[0];
//         $future_date =date('Y-m-d H:i:s', strtotime('+2 minutes', strtotime($current_date)));
//         $i=0;
//         $casefolding= array();
//         $casefolding[$i]['created_at'] = "";

//         foreach($data["crawling"] as $dt ){
//             if( $dt->created_at >= $current_date && $dt->created_at <= $future_date){
//                 if($casefolding[$i]['created_at'] == null){
//                     $casefolding[$i]['created_at'] = $dt->created_at;
//                 }
//                 $casefolding[$i][] = strtolower($dt->text);
//             }
//             else{
//                 $current_date = $future_date;
//                 $future_date =date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($current_date)));
//                 $i++;
//                 $casefolding[$i]['created_at'] = $dt->created_at;
//                 $casefolding[$i][] = $dt->text;

//             }
//         }
//         foreach($casefolding as $dta_casefolding){
//             $time_slot[] = $dta_casefolding['created_at'];
//         }
//         // Cleansing
//         foreach($casefolding as $clean){
//             unset($cleansing);
//             foreach($clean as $cl){
//                 $cl = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $cl); //remove url
//                 $cl = preg_replace('/#([\w-]+)/i', '', $cl); //  #remove tag
//                 $cl = preg_replace('/@([\w-]+)/i', '', $cl); // #remove @someone
//                 $cl = str_replace('rt : ', '', $cl); // #remove RT
//                 $cl = str_replace(',', '  ', $cl);
//                 $cl = str_replace('.', '  ', $cl);
//                 $cl = preg_replace('/[^A-Za-z0-9\  ]/', '', $cl);
//                 // $clean = trim(preg_replace('/\s+/', ' ', $clean));
//                 $cleansing[]=$cl;        
//             }
//         $data["crawling"]["cleansing"][] = $cleansing;
//     }
// // dd($data['crawling']['cleansing']);
   
//         // Tokenizing
//         foreach($data['crawling']['cleansing'] as $token){
//             unset($list);
//             foreach($token as $clean){
//                 $tokenizing[] = explode(' ', $clean);
//                 $list[] = explode(' ', $clean);    
//             }
//             $data["crawling"]["tokenizing"][] = $list;
//         }
//         // dd($data['crawling']['tokenizing']);
        
//         //Stopword 
//         unset($list);
//         $stopword_all = $this->stopword_model->getAll();
//         foreach($data['crawling']['tokenizing'] as $stop){
//             unset($result_stopword);
//             foreach($stop as $cl_stop){
//                 foreach($stopword_all as $word){
//                     $list[] = $word->stopword;
//                 }
//                 // var_dump($list);
//                 $finalWords = array_diff($cl_stop, $list);
//                 $implode = implode(" ", $finalWords);
//                 $result_stopword[] = $implode;
//             }
//             $data["crawling"]["stopword"][] = $result_stopword;

//         }
//         // dd($data['crawling']['stopword']);

//         // Singkatan
//         $singkatan_all = $this->singkatan_model->getAll();
//         foreach($data['crawling']['stopword'] as $arr_singkatan){
//             unset($list_singkatan);
//             foreach($arr_singkatan as $list_kata){
//                 foreach($singkatan_all as $singkatan){
//                     unset($hasil_kata_singkatan);
//                     $temp_kata = explode(' ',$list_kata);
//                     foreach($temp_kata as $kata){
//                         if(strlen($singkatan->kata_singkatan) == strlen($kata)){
//                             $hasil_kata_singkatan[] = str_replace($singkatan->kata_singkatan,$singkatan->kata_asli,$kata);
//                         }
//                         else{
//                             $hasil_kata_singkatan[] = $kata;
//                         }
//                     }
//                     $string_singkatan = implode(' ',$hasil_kata_singkatan);
//                     $string_singkatan = str_replace('  ','', $string_singkatan);

//                 }
//                 $list_singkatan[] = $string_singkatan;
//             }

//             // dd($list_singkatan);
//             $data["crawling"]["singkatan"][] = $list_singkatan;
//         }
// // dd($data['crawling']['singkatan']);

//         foreach($data['crawling']['singkatan'] as $dta){
//             // dd(sizeof($dta));
//             for($i =0;$i < sizeof($dta) ;$i++){
//                 if($i != 0){
//                     $list_tweet_cluster[] = $dta[$i];
//                 }
//             }
//         }

//         $i=0;
//         foreach($data['crawling']['singkatan'] as $dt){
//             $i++;
//             unset($dt_bingram);
//             foreach($dt as $list_ngram){
//                 $bingram = ngrams(explode(' ',$list_ngram));
//                 $token = tokenize(implode(" ",$dt));    
//                 $dt_bingram[] = $bingram;
//             }
//             $list_bingram[] = $dt_bingram;
//             $list_token[] = $token;
//         }
//         // dd($list_bingram);

//         foreach($list_bingram as $dta){
//             $tmp_list_bngram =null;
//             unset($new_array_bgram);
//             foreach($dta as $dt){
//                 foreach($dt as $t){
//                     $new_array_bgram[] = $t;
//                 }
//             }
//             $new_list_bngram[] = $new_array_bgram;
//         }

//         // dd($new_list_bngram);
//         // dd($list_token);        
//         if($new_list_bngram[0] == null){
//             $this->load->view("admin/product/result_bngram",$data);
//         }

//         foreach($new_list_bngram as $dta){
//             foreach($dta as $dt){
//                 unset($dta[0]);
//                 // unset($dta[3]);
//                 $new_dta = array_values($dta);
//             }
//             $list_dta[] = $new_dta;
//         }
//         // dd($list_dta);
//         $i=0;
//         // dd($casefolding);
//         // dd($list_dta);
//         foreach($list_dta as $pre_ngram){
//             $bigram = $pre_ngram;
//             $trigram = ngrams($pre_ngram);    
//             $list_bigram[$i][] = $bigram;
//             $list_bigram[$i][] = $time_slot[$i];
//             $list_tigram[$i][] = $trigram;
//             $i++;
//         }
//         $data['list_bngram'] = $list_bigram;
//         // dd($data['list_bngram']);

//         // dd($list_bigram);
//         $merge_list_seluruh_bngram = '';
//         foreach($list_bigram as $dt) {
//             $list_seluruh_bngram[] = implode(', ',$dt[0]);
//         }

//         foreach($list_seluruh_bngram as $dt){
//             $merge_list_seluruh_bngram .= $dt;
//         }

//         // dd($merge_list_seluruh_bngram);
//         $count_1_timeslot = [];
//         $t =0;
//         foreach($data['list_bngram'] as $dt){
//             $compare_data = implode(', ',$dt[0]);
//             unset($dt[1]);
//             foreach($dt as $dta){
//                 unset($count_all_timeslot);
//                 unset($count_1_timeslot);
//                 $count_1_timeslot= [];
//                 foreach($dta as $data_string){
//                     $count_1_timeslot[] = substr_count($compare_data,$data_string);
//                 }
//                 $list_count_1_timeslot[] = $count_1_timeslot;
//             }
//             $t = $t +1;
//         }
//         // dd($data['list_bngram']);
//         $i = 0;
//         foreach($data['list_bngram'] as $count_bngram){
//             $data['list_bngram'][$i][2] = $list_count_1_timeslot[$i];
//             $i++;
//         }
//         $data['t']= $t;
//         foreach($data['list_bngram'] as $count_double){
//             for($i=0 ;$i < sizeof($count_double[0])-1; $i++){
//                 for($j=$i+1 ;$j < sizeof($count_double[0]); $j++){
//                     // $compare[] = $count_double[0][$i].'=='. $count_double[0][$j];
//                     if(array_key_exists($j,$count_double[0]) && array_key_exists($i,$count_double[0])){
//                         if($count_double[0][$i] == $count_double[0][$j]){
//                             unset($count_double[0][$j]);
//                             unset($count_double[2][$j]);
//                             unset($count_double[3][$j]);
//                         }
//                     }
//                 }

//             }
//             $new_count_double[] = array_values($count_double);
//         }
//         // dd($compare,$data['list_bngram'],$new_count_double);
//         $data['list_bngram'] = $new_count_double;
// // dd($data['list_bngram']);
// // dd(sizeof($data['list_bngram'][1][0]));   
//         $h=0;
//         foreach($data['list_bngram'] as $dta){
//             $i=0;
//             foreach($dta[0] as $dt){
//                 foreach($boost as $bs){
//                     if($dt == $bs->kata){
//                         $data['list_bngram'][$h][3][$i] = 1.5;
//                         break;
//                     }else{
//                         $data['list_bngram'][$h][3][$i] = 1;
//                     }
//                 }
//                 $i++;   
//             }
//             $h++;
//         }
//         $i=0;
//         foreach($data['list_bngram'] as $dta){
//             $j=0;
//             foreach($dta as $dt){
//                 if($j != 1){
//                 $data['list_bngram'][$i][$j]= array_values($dt);
//                 }
//                 $j++;
//             }
//             $i++;
//         }
        
//         $h=0;
//         foreach($data['list_bngram'] as $dta){
//             $i=0;
//             foreach($dta[0] as $dt){
//                 // dd($data['list_bngram'][$h][3]);
//                 if($h == 0){
//                     $div = log10((0/$data['t'])+1) + 1;
//                     $sum = ($data['list_bngram'][$h][2][$i]+1) / $div * $data['list_bngram'][$h][3][$i];
//                     $data['list_bngram'][$h][4][$i] = $sum;
//                 }
//                 else{
//                     $count=0;
//                     for($j=$h-1;$j >= 0; $j--){
//                         for($k=0;$k < sizeof($data['list_bngram'][$j][0]); $k++){
//                             if($data['list_bngram'][$h][0][$i] == $data['list_bngram'][$j][0][$k]){
//                                 $count += $data['list_bngram'][$j][2][$k];
//                             }
//                         } 

//                     }
//                     $div = (log10(($count/$data['t'])+1) + 1);
//                     $sum = ($data['list_bngram'][$h][2][$i]+1) / $div * $data['list_bngram'][$h][3][$i];
//                     $data['list_bngram'][$h][4][$i] = $sum;
//                     $data['list_bngram'][$h][5][$i] = $count;
//                 }
//                 $i++;
//             }
//             $h++;
//         }
        
//         foreach($data['list_bngram'] as $dt){
//             foreach($dt[0] as $dta){
//                 $list_cluster[] = $dta;
//             }
//         }

//         foreach($data['list_bngram'] as $dta){
//             if(array_key_exists(4,$dta)){
//                 foreach($dta[4] as $dt){
//                     $dfidf[1][]=$dt;
//                 }    
//             }
//         }

//         foreach($data['list_bngram'] as $dta){
//             foreach($dta[0] as $dt){
//                 $dfidf[0][]=$dt;
//             }
//         }

//         $i=0;
//         // dd($dfidf);
//         foreach($dfidf[1] as $dt){
//             if($dt == max($dfidf[1])){
//                 $max_dfidf[] =$dfidf[0][$i];
//             }
//             $i++;
//         }
        
//         $unique_list_cluster = array_values(array_unique($list_cluster));
//         foreach($unique_list_cluster as $dt){
//             $temp = explode(' ',$dt);
//             $fix_list[] = $temp[0];
//         }

//         // dd($fix_list);
//         $dt_2d =[];
//         for($i=0; $i < sizeof($fix_list); $i++){
//             for($j=0; $j< sizeof($list_tweet_cluster) ; $j++){
//                 $explode_tweet_cluster = explode(' ',$list_tweet_cluster[$j]);
//                 if(in_array($fix_list[$i],$explode_tweet_cluster)){
//                     $dt_2d[$i][$j] = 1;
//                 }
//                 else{
//                     $dt_2d[$i][$j] = 0;
//                 }
//             }
//         }
//         foreach($dt_2d as $dt){
//             $tot_n_ngram[] = array_sum($dt);
//         }

//         $data['dt_2d']=$dt_2d;
//         for($i=0; $i < sizeof($data['dt_2d']); $i++){
//             for($j=0; $j < sizeof($data['dt_2d']) ; $j++){
//                 $temp_dt_min = [$tot_n_ngram[$i],$tot_n_ngram[$j]];
//                 $count =0;
//                 for($k=0;$k < sizeof($data['dt_2d'][$i]) ;$k++){
//                     if($data['dt_2d'][$i][$k] == 1 && $data['dt_2d'][$j][$k] == 1){
//                         $count++;
//                     }
//                 }
//                 $dt_dman_cluster[$i][$j] = 1 - ($count/min($temp_dt_min));
//             }
//         }
        
//         $data['dt_dman_cluster']= $dt_dman_cluster;

//         // new cluster
//         unset($temp_arr);
//         $temp_average = null;
//         for($i=0 ; $i <sizeof($data['dt_dman_cluster']) ; $i++){
//             $temp_average = null;
//             for($j=0 ; $j <sizeof($data['dt_dman_cluster']) ; $j++){
//                 for($k=0 ; $k <sizeof($data['dt_dman_cluster']) ; $k++){
//                     $temp_arr = [$data['dt_dman_cluster'][$k][$j],$data['dt_dman_cluster'][$j][$k]];
//                     $average = array_sum($temp_arr)/sizeof($temp_arr);
//                     if($temp_average == null){
//                         $temp_average[] = $average;
//                         $indeks[] = $j.','.$k.';'.$k.','.$j;
//                     }                    
//                     if($average < $temp_average){
//                         $temp_average[] = $average;
//                         $indeks[] = $j.','.$k;
//                     }

//                 }
//             }
            
//             dd($temp_average,$indeks);
//         }

//         // new cluster


//         for($i=0; $i < sizeof($data['dt_dman_cluster']); $i++){
//             for($j=0; $j <sizeof($data['dt_dman_cluster']); $j++){
//                 $temp_arr = [$data['dt_dman_cluster'][$i][$j],$data['dt_dman_cluster'][$i][$j]];
//                 $average = array_sum($temp_arr)/sizeof($temp_arr);
//                 $cluster[''.$average.''][] = $j;
//                 $cluster[''.$average.''][] = $i;
//             }
//         } 

//             foreach($cluster as $dt){
//                 foreach($dt as $dta){
//                     $nama_tweet_cluster[$j][] = $fix_list[$dta];
//                     $i++;
//                 }
//                 $nama_tweet_cluster[$j] = array_unique($nama_tweet_cluster[$j]);
//                 $j++;
//                 $i=0;
//             }

//             foreach($max_dfidf as $dt){
//                 $temp_arr = explode(' ',$dt);
//                 $new_max_dfidf[] = $temp_arr;
//             }

//             foreach($hasil_akhir as $dt){
//                 $i=0;
//                 foreach($dfidf[0] as $dta){
//                     $ex_dta = explode(' ',$dta);
//                     $temp[] = $ex_dta[0].' == '.$dt;
//                     if($ex_dta[0] == $dt){
//                         $new_hasil_akhir[$ex_dta[0]] = $dfidf[1][$i];
//                     }
//                     $i++;
//                 }
//             }

//             $insert_trending = implode(', ',array_unique($hasil_akhir));

//             arsort($new_hasil_akhir);
//             $i=0;
//             foreach($new_hasil_akhir as $key=>$value){
//                 if($i < 10){
//                     $top_10[] = $key;
//                 }
//                 $i++;
//             }

//         $data_tweet = array(
//             'tweet' => $insert_trending,
//             );

//         $check = $this->Trending_model->save($data_tweet);

//         $data['hasil_akhir'] = array_unique($max_dfidf);
        
//         $this->load->view("admin/product/result_bngram",$data);
//     }
} 