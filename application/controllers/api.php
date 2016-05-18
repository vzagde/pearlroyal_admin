<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Kolkata');
	}

	public function index(){
		$this->chk_user_signin();
		$this->get_categories();
	}

	public function chk_user_signin(){
		$data = $this->session->all_userdata();
		if(!empty($data['username'])){
			$result[] = array( 'id'=> $data['user_id'] );
		} else {
			$result[] = array( 'id'=> 0 );
		}
		// $this->session->set_userdata('username', 'null');
		// echo $data['email'];

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	// public function forgot_password(){
	// 	$user_id = $this->input->post('id');
	// 	$password = code_gen();
	// 	$this->db->where("email", $user_id);
	// 	$q = $this->db->get("users");
	// 	if($q->num_rows() > 0){
	// 		$data = array(
	// 			'password' => md5($password);
	// 		);
	// 		$this->db->where("email", $user_id);
	// 		$this->db->update("users", $data);

 //            $this->load->library('email');
 //            $this->email->from('info@pearlroyale.com', 'Pearlroyale Team');
 //            $this->email->to($user_id);
 //            $this->email->cc('info@pearlroyale.com');

 //            $this->email->subject('Forgot Password Mail');
 //            $this->email->message('You have been successfully recreated the password for Username '.$user_id.', which is Password :'.$password);

 //            $this->email->send();
 //            $result = array('id' => 1, );
	// 		header('Access-Control-Allow-Origin: *');
	// 		header('Content-Type: application/json');
	// 		echo json_encode($result);
	// 	} else {
 //            $result = array('id' => 0, );
	// 		header('Access-Control-Allow-Origin: *');
	// 		header('Content-Type: application/json');
	// 		echo json_encode($result);
	// 	}
	// }

	function code_gen($n){
		$chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz123456789";
		$i = 0;
		$pass = '';

		while ($i <= $n){
			$num = mt_rand(0,59);
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		// echo $pass;
		return $pass;
	}

	function code_gen1(){
		$chars = "123456789";
		$i = 0;
		$pass = '';

		while ($i <= 4){
			$num = mt_rand(0,8);
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	public function register_user(){
		$username = $this->input->post('email');
		$password = substr($username, 0, 3).$this->code_gen1();
		$email = $this->input->post('email');

		$additional_data = array(
			'first_name' => $this->input->post('fname'),
			'last_name' => $this->input->post('lname'),
	        'phone'  => $this->input->post('tel'),
			'city' => $this->input->post('city'),
	        'country'  => $this->input->post('country'),
	        'company'  => $this->input->post('company'),
	    );

        if ($this->ion_auth->register($username, $password, $email, $additional_data)) {
            $query = $this->db->insert_id();
            $data = array("user_id"=>$query);

            $this->load->library('email');
            $this->email->from('info@pearlroyale.com', 'Pearlroyale Team');
            $this->email->to($username);
            $this->email->cc('vzagde110@gmail.com');
            $this->email->bcc('siddharth2428@gmail.com');

            $this->email->subject('Registeration Success Mail');
            $this->email->message('You have been registered successfully with Username '.$username.' and Password :'.$password);

            $this->email->send();
            // echo $this->email->print_debugger();

			header('Access-Control-Allow-Origin: *');
			header('Content-Type: application/json');
			echo json_encode($data);
        } else {
            echo 'failed';
        }
	}

	public function login_user(){
		$identity = $this->input->post('username');
		$password = $this->input->post('password');
		// $identity = 'vzagde110@gmail.com';
		// $password = 'xyz123';

		$identity = $identity;
		$password = $password;
		$remember = TRUE;
		if($this->ion_auth->login($identity, $password, $remember)){
			$data = $this->ion_auth->user()->result();
			$this->session->set_userdata('email', $this->input->post('username'));
		} else {
			$data[] = array('id' => '0', );
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	function logout(){
		if($this->ion_auth->logout()){
			$data =  array( 'id' => 1 );
			$this->session->set_userdata('email', '');
			$this->session->set_userdata('username', '');
		} else {
			$data =  array( 'id' => 0 );
		}
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function get_users_info(){
		$user_id = $this->input->post('user_id');
		// $user_id = 1;

		$this->db->where('id', $user_id);
		$query = $this->db->get('users');

		$que = $this->db->query('SELECT SUM(bidded_amount) as s FROM `uirelation` WHERE user_id ='.$user_id);
		$bidded_amount = $que->row()->s;

		$q = $this->db->get('usd_text');

		foreach ($query->result() as $value) {
			$results[] = array(
				'id' => $value->id,
				'username' => $value->username,
				'first_name' => $value->first_name,
				'last_name' => $value->last_name,
				'phone' => $value->phone,
				'city' => $value->city,
				'country' => $value->country,
				'amount' => $value->amount,
				'expence' => $bidded_amount,
				'total' => $value->amount + $bidded_amount,
				'usd_text' => $q->row()->text,
			);
		}


		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function get_categories(){
		$this->db->order_by('priority', 'asc');
		$query = $this->db->get('categories');
		$results = $query->result();

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function forgot_password(){
		$user_id = $this->input->post('id');
		if($user_id != ''){
			$this->db->where('email', $user_id);
			$q = $this->db->get('users')->row();

			$salt       = $this->ion_auth_model->store_salt ? $this->ion_auth_model->salt() : FALSE;
			$str1 = trim(explode("@",$user_id)[0]);
			$password   = substr($str1, 0, 3).trim($this->code_gen1());

			$data = array(
			    'password'                => $this->ion_auth_model->hash_password($password, $salt),
			    'forgotten_password_code' => NULL,
			    'active'                  => 1,
			);

			$this->db->where('email', $user_id);
			if ($this->db->update('users', $data)) {
				$res = array(
						'password' => $password,
					);

	            $this->load->library('email');
	            $this->email->from('info@pearlroyale.com', 'Pearlroyale Team');
	            $this->email->to($user_id);
	            // $this->email->cc('vzagde110@gmail.com');
	            $this->email->bcc('siddharth2428@gmail.com');

	            $this->email->subject('Forgot Password Confirmation Mail');
	            $this->email->message("Your New Password for the Username : ".$user_id." Password is :".$password);

	            $this->email->send();

			} else {
				$res = array(
						'password' => 0,
					);
			}
			header('Access-Control-Allow-Origin: *');
			header('Content-Type: application/json');
			echo json_encode($res);
		}
	}

	public function get_auctions(){
		$id = $this->input->post('id');
		// $id = 3;
		$auction_id = 0;
		$str = 'SELECT * FROM auctions JOIN items ON auctions.id=items.auction AND items.category = '.$id.' GROUP BY auctions.id ORDER BY items.item_key ASC';
		$query = $this->db->query($str);

		if ($query->num_rows == 0){
			$data[] = array('id' => 0 );
		}

		foreach ($query->result() as $value) {
			if ($auction_id != $value->auction) {
				$data[] = array(
					'id' => $value->auction,
					'auction_name' => $value->auction_name,
					'address' => $value->address,
					'auction_start' => $value->auction_start_date,
					'auction_end' => $value->auction_end_date,
					'auction_archive' => $value->auction_archive_date,
					'auction_start_time' => $value->auction_start_time,
					'auction_end_time' => $value->auction_end_time,
					'auction_archive_time' => $value->auction_archive_time
				);
			}
			$auction_id = $value->auction;
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function get_product_list(){
		$category_id = $this->input->post('category_id');
		$auction_id = $this->input->post('auction_id');
		// $category_id = 3;
		// $auction_id = 1;
		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$this->db->order_by("item_key", "asc");
		$query = $this->db->get('items');

			foreach ($query->result() as $value) {
				$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
				$q = $this->db->query($str);
				$filters = $q->result();
				if($q->num_rows() == 0){
					$filters[] = array( 'id' => 0 ); 
				}

				$this->db->where('priority', 1);
				$this->db->where('item_id', $value->id);
				$imageview = $this->db->get('item_images');

				$this->db->where('item_id', $value->id);
				$this->db->from('item_images');
				$que = $this->db->count_all_results();
				if($value->image != ''){
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_id,
						'item_key' => $value->item_key,
						'num_images' => $que,
						'image' => $value->image,
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				} else {
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_id,
						'item_key' => $value->item_key,
						'num_images' => $que,
						'image' => 'imagenotfound.png',
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				}
			}

		$data['highestflooramountdata'][] = $this->highestflooramount_data($auction_id, $category_id);
		$data['highestbidamountdata'][] = $this->highestbidamount_data($auction_id, $category_id);
		$data['lowestflooramountdata'][] = $this->lowestflooramount_data($auction_id, $category_id);
		$data['lowestbidamountdata'][] = $this->lowestbidamount_data($auction_id, $category_id);

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	function lowestbidamount_data($auction_id, $category_id){

		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$this->db->order_by("bid_amount", "asc");
		$query = $this->db->get('items');

		$data = array('id' => 0);
		foreach ($query->result() as $value) {
			$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
			$q = $this->db->query($str);
			$filters = $q->result();
			if($q->num_rows() == 0){
				$filters[] = array( 'id' => 0 ); 
			}

			$this->db->where('priority', 1);
			$this->db->where('item_id', $value->id);
			$imageview = $this->db->get('item_images');

			$this->db->where('item_id', $value->id);
			$this->db->from('item_images');
			$que = $this->db->count_all_results();

			if($value->image != ''){
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => $value->image,
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			} else {
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => 'imagenotfound.png',
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			}
		}
		//print_r($data);
		return ($data);
	}	

	function lowestflooramount_data($auction_id, $category_id){

		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$this->db->order_by("floor_amount", "asc");
		$query = $this->db->get('items');

		$data = array('id' => 0);
		foreach ($query->result() as $value) {
			$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
			$q = $this->db->query($str);
			$filters = $q->result();
			if($q->num_rows() == 0){
				$filters[] = array( 'id' => 0 ); 
			}

			$this->db->where('priority', 1);
			$this->db->where('item_id', $value->id);
			$imageview = $this->db->get('item_images');

			$this->db->where('item_id', $value->id);
			$this->db->from('item_images');
			$que = $this->db->count_all_results();

			if($value->image != ''){
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => $value->image,
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			} else {
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => 'imagenotfound.png',
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			}
		}
		return ($data);
	}

	function highestflooramount_data($auction_id, $category_id){

		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$this->db->order_by("floor_amount", "desc");
		$query = $this->db->get('items');

		$data = array('id' => 0);
		foreach ($query->result() as $value) {
			$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
			$q = $this->db->query($str);
			$filters = $q->result();
			if($q->num_rows() == 0){
				$filters[] = array( 'id' => 0 ); 
			}

			$this->db->where('item_id', $value->id);
			$this->db->from('item_images');
			$que = $this->db->count_all_results();

			$this->db->where('priority', 1);
			$this->db->where('item_id', $value->id);
			$imageview = $this->db->get('item_images');

			if($value->image != ''){
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => $value->image,
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			} else {
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => 'imagenotfound.png',
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			}
		}
		return ($data);
	}

	function highestbidamount_data($auction_id, $category_id){

		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$this->db->order_by("bid_amount", "desc");
		$query = $this->db->get('items');

		$data = array('id' => 0);
		foreach ($query->result() as $value) {
			$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
			$q = $this->db->query($str);
			$filters = $q->result();
			if($q->num_rows() == 0){
				$filters[] = array( 'id' => 0 ); 
			}

			$this->db->where('priority', 1);
			$this->db->where('item_id', $value->id);
			$imageview = $this->db->get('item_images');

			$this->db->where('item_id', $value->id);
			$this->db->from('item_images');
			$que = $this->db->count_all_results();

			if($value->image != ''){
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => $value->image,
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			} else {
				$data["filetered"][] = array(
					'id' => $value->id,
					'item_name' => $value->item_name,
					'item_id' => $value->item_id,
					'item_key' => $value->item_key,
					'num_images' => $que,
					'image' => 'imagenotfound.png',
					'floor_amount' => $value->floor_amount,
					'bid_amount' => $value->bid_amount,
					'filters' => $filters
				);
			}
		}
		return ($data);
	}

	public function get_products_info(){
		$id = $this->input->post('product_id');
		$user_id = $this->input->post('user_id');
		// $id = 16;
		// $user_id = 2;
		$qstr = 'SELECT * FROM auctions JOIN items ON auctions.id = items.auction AND items.id ='.$id;
		$query = $this->db->query($qstr);

		$this->db->where('user_id', $user_id);
		$this->db->where('item_id', $id);
		$fav = $this->db->get('uirelation');

		if ($fav->num_rows() > 0) {
			$fav_id = 0;
		} else {
			$fav_id = 1;
		}

		$this->db->where('priority', 1);
		$this->db->where('item_id', $id);
		$imageview = $this->db->get('item_images');

		$this->db->where('item_id', $id);
		$this->db->from('item_images');
		$que = $this->db->count_all_results();

		$this->db->where('item_id', $id);
		$images = $this->db->get('item_images');

		$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$id;
		$q = $this->db->query($str);
		$filters = $q->result();
		if($q->num_rows() == 0){
			$filters[] = array( 'id' => 0 );
		}

		$bid_time = $query->row()->auction_end_date.' '.$query->row()->auction_end_time;

		$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$query->row()->id;
		$q = $this->db->query($str);
		$filters = $q->result();
		if($q->num_rows() == 0){
			$filters[] = array( 'id' => 0 ); 
		}

		if($query->row()->image != ''){
			$data[] = array(
				'id' => $id,
				'item_name' => $query->row()->item_name,
				'item_id' => $query->row()->item_id,
				'item_key' => $query->row()->item_key,
				'description' => $query->row()->description,
				'image' => $query->row()->image,
				'num_images' => $que,
				'images' => $images->result(),
				'bid_amount' => $query->row()->bid_amount,
				'floor_amount' => $query->row()->floor_amount,
				'bid_time' => $bid_time,
				'filters' => $filters,
				'fav' => $fav_id,
			);
		} else {
			$data[] = array(
				'id' => $id,
				'item_name' => $query->row()->item_name,
				'item_id' => $query->row()->item_id,
				'item_key' => $query->row()->item_key,
				'description' => $query->row()->description,
				'image' => 'imagenotfound.png',
				'num_images' => $que,
				'images' => $images->result(),
				'bid_amount' => $query->row()->bid_amount,
				'floor_amount' => $query->row()->floor_amount,
				'bid_time' => $bid_time,
				'filters' => $filters,
				'fav' => $fav_id,
			);
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function get_single_image(){
		$id = $this->input->post('id');

	    $this->db->where('id',$id);
	    $query = $this->db->get('item_images');

	    $data[] = array(
	    	'image' => $query->row()->url
	    );

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	//create new user and item relation in uirelation table
	public function insert_uirelation($user_id, $product_id){
		$data = array(
		   'user_id' => $user_id,
		   'item_id' => $product_id,
		   'favourite' => 0,
		   'viewed' => 1,
		   'notification' => 0,
		   'bidded_amount' => 0,
		   'bid' => 0
		);

		$this->db->insert('uirelation', $data);
	}

	public function rel_exists(){
		$product_id = $this->input->post('product_id');
		$user_id = $this->input->post('user_id');

	    $this->db->where('user_id',$user_id);
	    $this->db->where('item_id', $product_id);
	    $query = $this->db->get('uirelation');
	    if ($query->num_rows() > 0){
			return true;		    	
	    }
	    else{
	        $this->insert_uirelation($user_id, $product_id);
	    }
	}

	public function mark_favourite(){
		$product_id = $this->input->post('item_id');
		$user_id = $this->input->post('user_id');

	    $this->db->where('user_id', $user_id);
	    $this->db->where('item_id', $product_id);
	    $query = $this->db->get('uirelation');
	    $data = $query->result();

	    if ( $query->row()->favourite == 0 ){
			$this->db->set('favourite', 1);
			$this->db->where('item_id', $product_id);
			$this->db->where('user_id', $user_id);
			$this->db->update('uirelation');
			$result = array(
					'id' => 1,
				);
	    } else {
			$this->db->set('favourite', 0);
			$this->db->where('item_id', $product_id);
			$this->db->where('user_id', $user_id);
			$this->db->update('uirelation');
			$result = array(
					'id' => 0,
				);
	    }

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	function ckh_valid_bidamount(){
		$product_id = $this->input->post('product_id');
		$bid_amount = $this->input->post('bid_amount');
		$user_id = $this->input->post('user_id');
		$current_date = date("Y-m-d H:i:s");

		$qstr = 'SELECT * FROM auctions JOIN items ON auctions.id = items.auction AND items.id ='.$product_id;
		$query = $this->db->query($qstr);		

		$bid_date_end = $query->row()->auction_end_date;
		$bid_time_end = $query->row()->auction_end_time;
		$bid_time_end = $bid_date_end.' '.$bid_time_end;

		$bid_date = $query->row()->auction_start_date;
		$bid_time = $query->row()->auction_start_time;
		$bid_time = $bid_date.' '.$bid_time;

		if (strtotime($current_date) < strtotime($bid_time_end) && strtotime($current_date) > strtotime($bid_time)){

			if($query->row()->bid_amount == 0){
				$pre_bid_amount = $query->row()->floor_amount;
			} else {
				$pre_bid_amount = $query->row()->bid_amount;
			}

			$this->db->where('id', $user_id);
			$users = $this->db->get('users');
			$balance_amount = $users->row()->amount;

			$data[] = array(
				'curr_bid' => $bid_amount,
				'amount' => $balance_amount,
				'pre_bid' => $pre_bid_amount 
			);

		} else {
			$data[] = array('id' => 0, );
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}	


	function ckh_valid_bidamount_demo(){
		// $product_id = $this->input->post('product_id');
		// $bid_amount = $this->input->post('bid_amount');
		// $user_id = $this->input->post('user_id');

		$product_id = 153;
		$bid_amount = 110;
		$user_id = 1;

		$current_date = date("Y-m-d H:i:s");

		$qstr = 'SELECT * FROM auctions JOIN items ON auctions.id = items.auction AND items.id ='.$product_id;
		$query = $this->db->query($qstr);		

		$bid_date_end = $query->row()->auction_end_date;
		$bid_time_end = $query->row()->auction_end_time;
		$bid_end = $bid_date_end.' '.$bid_time_end;

		$bid_start_date = $query->row()->auction_start_date;
		$bid_start_time = $query->row()->auction_start_time;
		$bid_time = $bid_start_date.' '.$bid_start_time;

		echo $current_date;
		echo $bid_time;
		echo $bid_end;

		if (strtotime($current_date) < strtotime($bid_end) && strtotime($current_date) > strtotime($bid_time)) {
			echo "sucess";
		}

		if (strtotime($current_date) < strtotime($bid_time_end) && strtotime($current_date) > strtotime($bid_time)) {
			if($query->row()->bid_amount == 0){
				$pre_bid_amount = $query->row()->floor_amount;
			} else {
				$pre_bid_amount = $query->row()->bid_amount;
			}

			$this->db->where('id', $user_id);
			$users = $this->db->get('users');
			$balance_amount = $users->row()->amount;

			$data[] = array(
				'curr_bid' => $bid_amount,
				'amount' => $balance_amount,
				'pre_bid' => $pre_bid_amount 
			);

		} else {
			$data[] = array('id' => 0, );
		}
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}	


	function bid_execute(){
	
		$item_id = $this->input->post('product_id');
		$bid_amount = $this->input->post('bid_amount');
		$user_id = $this->input->post('user_id');

		$old_values = $this->get_old_user($item_id);

		$this->db->set('bid_amount', $bid_amount);
		$this->db->where('id', $item_id);
		$this->db->update('items');

		$this->db->set('amount','amount - '.$bid_amount, false);
		$this->db->where('id', $user_id);
		$this->db->update('users');

		$this->db->set('favourite', 1);
		$this->db->set('bid', 2);
		$this->db->set('notification', 0);
		$this->db->set('bidded_amount', $bid_amount);
		$this->db->where('user_id', $user_id);
		$this->db->where('item_id', $item_id);
		$this->db->update('uirelation');

		$this->db->set('amount','amount + '.$old_values['bidded_amount'], false);
		$this->db->where('id', $old_values['old_id']);
		$this->db->update('users');

		$logs = array(
		               'item_id' => $item_id,
		               'user_id' => $user_id,
		               'bid_amount' => $bid_amount
		            );

		$this->db->insert('log', $logs);

		$this->db->set('bid', 1);
		$this->db->set('notification', 1);
		$this->db->set('bidded_amount', 0);
		$this->db->set('previous_biding', $old_values['bidded_amount']);
		$this->db->where('user_id', $old_values['old_id']);
		$this->db->where('item_id', $item_id);
		$this->db->update('uirelation');
	}

	public function get_old_user($item_id){

		$this->db->where('item_id', $item_id);
		$this->db->where('bid', 2);
		$query = $this->db->get('uirelation');
		$data = array(
			'old_id' => $query->row()->user_id,
			'bidded_amount' => $query->row()->bidded_amount,
		);
		return $data;
	}

	public function get_viewed_fav_noti(){
		$user_id = $this->input->post('user_id');
		$filter = $this->input->post('filter');

		$querystr = 'SELECT uirelation.favourite, uirelation.bid, uirelation.notification, items.id, items.item_id, items.item_name, items.item_key, items.image, items.floor_amount, items.bid_amount FROM uirelation JOIN items ON uirelation.item_id = items.id JOIN auctions ON items.auction = auctions.id AND uirelation.user_id = '.$user_id.' AND uirelation.'.$filter.' = 1 AND DATE(auctions.auction_archive_date) > DATE(NOW())';
		// $querystr = 'SELECT uirelation.favourite,uirelation.bid,uirelation.notification,items.id,items.item_id,items.item_name,items.item_key,items.image,items.floor_amount,items.bid_amount FROM uirelation JOIN items ON uirelation.item_id=items.id AND uirelation.user_id = '.$user_id.' AND uirelation.'.$filter.'=1';

		$query = $this->db->query($querystr);
		if($query->num_rows() == 0){
			$data[] = array('id' => 0 );
		} else {
			foreach ($query->result() as $value) {
				$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
				$q = $this->db->query($str);
				$filters = $q->result();
				if($q->num_rows() == 0){
					$filters[] = array( 'id' => 0 ); 
				}

				$this->db->where('priority', 1);
				$this->db->where('item_id', $value->id);
				$imageview = $this->db->get('item_images');

				$this->db->where('item_id', $value->id);
				$this->db->from('item_images');
				$que = $this->db->count_all_results();

				if($value->image != ''){
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_id,
						'item_key' => $value->item_key,
						'num_images' => $que,
						'bid' => $value->bid,
						'notification' => $value->notification,
						'favourite' => $value->favourite,
						'image' => $value->image,
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				} else {
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_id,
						'item_key' => $value->item_key,
						'num_images' => $que,
						'bid' => $value->bid,
						'notification' => $value->notification,
						'favourite' => $value->favourite,
						'image' => 'imagenotfound.png',
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				}
			}
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function get_bid_list(){
		$user_id = $this->input->post('user_id');
		// $user_id = 1;
		$querystr = "SELECT uirelation.favourite, uirelation.bid, uirelation.notification, items.id, items.item_id, items.item_name, items.item_key, items.image, items.floor_amount, items.bid_amount FROM uirelation JOIN items ON uirelation.item_id=items.id JOIN auctions ON items.auction = auctions.id AND uirelation.user_id= '".$user_id."' AND uirelation.bid > 0 AND DATE(auctions.auction_archive_date) > DATE(NOW())";
		// $querystr = 'SELECT uirelation.favourite,uirelation.bid,uirelation.notification,items.id,items.item_id,items.item_name,items.item_key,items.image,items.floor_amount,items.bid_amount FROM uirelation JOIN items ON uirelation.item_id=items.id AND uirelation.user_id='.$user_id.' AND uirelation.bid>0';
		$query = $this->db->query($querystr);
		if($query->num_rows() == 0){
			$data[] = array('id' => 0 );
		} else {

			foreach ($query->result() as $value) {
				$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
				$q = $this->db->query($str);
				$filters = $q->result();
				if($q->num_rows() == 0){
					$filters[] = array( 'id' => 0 ); 
				}

				$this->db->where('priority', 1);
				$this->db->where('item_id', $value->id);
				$imageview = $this->db->get('item_images');

				$this->db->where('item_id', $value->id);
				$this->db->from('item_images');
				$que = $this->db->count_all_results();

				if($value->image != ''){
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_name,
						'item_key' => $value->item_key,
						'num_images' => $que,
						'bid' => $value->bid,
						'notification' => $value->notification,
						'favourite' => $value->favourite,
						'image' => $value->image,
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				} else {
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_name,
						'item_key' => $value->item_key,
						'num_images' => $que,
						'bid' => $value->bid,
						'notification' => $value->notification,
						'favourite' => $value->favourite,
						'image' => 'imagenotfound.png', 
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				}
			}
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	// public function get_notification_count(){
	// 	$user_id = $this->input->post('user_id');

	// 	$this->db->where('user_id', $user_id);
	// 	$this->db->where('notification', 1);
	// 	$this->db->from('uirelation');
	// 	$que = $this->db->count_all_results();

	// 	$data[] = array('notify' => $que, );

	// 	header('Access-Control-Allow-Origin: *');
	// 	header('Content-Type: application/json');
	// 	echo json_encode($data);		
	// }

	public function get_notification_count(){
		$user_id = $this->input->post('user_id');

		$this->db->from('usd_text');
		$que = $this->db->count_all_results();

		$data[] = array('notify' => $que, );

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);		
	}	

	public function get_bid_amount(){
		$product_id = $this->input->post('product_id');
		$this->db->where('id', $product_id);
		$query = $this->db->get('items');

		$data[] = array(
			'bid_amount' => $query->row()->bid_amount 
		);

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);		
	}

	public function check_bid_status(){
		$user_id = $this->input->post('user_id');
		$product_id = $this->input->post('product_id');

		$this->db->where('user_id', $user_id);
		$this->db->where('item_id', $product_id);
		$query = $this->db->get('uirelation');
 		$results = $query->result();

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function get_about_us(){
		$query = $this->db->get('about_us');
 		$results = $query->result();

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function get_notification_content(){

		$query = $this->db->get('usd_text');
 		$results = $query->result();

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function get_filterslist(){
		$category = $this->input->post('id');

		$query = $this->db->query('SELECT itemfilterrel.filter_id, GROUP_CONCAT(itemfilterrel.filter_value) as t, categoryfilterrel.filter_name, categoryfilterrel.filter_type FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id = categoryfilterrel.id AND categoryfilterrel.category_id = '.$category.' GROUP BY itemfilterrel.filter_id');

		foreach ($query->result() as $value) {
			$data[] = array(
				'id' => $value->filter_id,
				'filter_name' => $value->filter_name,
				'filter_value' => $value->t,
				'filter_type' => $value->filter_type
			);
		}

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}

		// $query = $this->db->query("SELECT * FROM itemfilterrel WHERE filter_id in (SELECT id FROM categoryfilterrel WHERE category_id = 1) ");

		// $d = array();
		// foreach ($query->result() as $t){
		// 	$d = array_merge($d, explode(',',trim($t->filter_value)));
		// }

		// $data = array_unique($d);
				/*'filter_value' => $query->row()->GROUP_CONCAT(filter_value),*/
	public function get_filtered_items(){
		$category_id = $this->input->post('category_id');
		$auction_id = $this->input->post('auction_id');
		$filter_id = $this->input->post('filter_id');
		$filter_value = $this->input->post('filter_value');
/*		$category_id = 1;
		$auction_id = 1;
		$filter_id = 11;
		$filter_value = 'blue';		*/

		$questr = 'select items.id, items.item_name, items.item_key, items.item_id, items.image, items.bid_amount, items.floor_amount from items JOIN itemfilterrel ON items.id = itemfilterrel.item_id AND itemfilterrel.filter_id = '.$filter_id.' AND items.category = '.$category_id.' AND items.auction = '.$auction_id.' AND itemfilterrel.filter_value = "'.$filter_value.'"  GROUP BY items.item_name';
		$query = $this->db->query($questr);
		
			foreach ($query->result() as $value) {
				$str = 'SELECT * FROM itemfilterrel JOIN categoryfilterrel ON itemfilterrel.filter_id=categoryfilterrel.id AND itemfilterrel.item_id='.$value->id;
				$q = $this->db->query($str);
				$filters = $q->result();
				if($q->num_rows() == 0){
					$filters[] = array( 'id' => 0 ); 
				}

				$this->db->where('priority', 1);
				$this->db->where('item_id', $value->id);
				$imageview = $this->db->get('item_images');

				$this->db->where('item_id', $value->id);
				$this->db->from('item_images');
				$que = $this->db->count_all_results();

				if($value->image != ''){
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_id,
						'item_key' => $value->item_key,
						'num_images' => $que,'image' => $value->image,   
						'floor_amount' => $value->floor_amount,
						'bid_amount' => $value->bid_amount,
						'image' => $value->image,
						'filters' => $filters
					);
				} else {
					$data['productdata'][] = array(
						'id' => $value->id,
						'item_name' => $value->item_name,
						'item_id' => $value->item_id,
						'item_key' => $value->item_key,
						'num_images' => $que,'image' => $value->image,   
						'floor_amount' => $value->floor_amount,
						'image' => 'imagenotfound.png',
						'bid_amount' => $value->bid_amount,
						'filters' => $filters
					);
				}
			}

		$data['highestflooramountdata'][] = $this->highestflooramount_data($auction_id, $category_id);
		$data['highestbidamountdata'][] = $this->highestbidamount_data($auction_id, $category_id);
		$data['lowestflooramountdata'][] = $this->lowestflooramount_data($auction_id, $category_id);
		$data['lowestbidamountdata'][] = $this->lowestbidamount_data($auction_id, $category_id);

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($data);
	}
}