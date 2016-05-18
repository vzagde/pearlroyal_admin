<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function index(){
		$this->get_categories();
	}

	public function get_categories(){
		$this->db->order_by('priority', 'asc');
		$query = $this->db->get('category');
		$results = $query->result();

		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function get_notification_count(){
		$user_id = $this->input->post('user_id');

		$this->db->where('user_id', $user_id);
		$this->db->where('notification', 1);
		$this->db->from('uirelation');
		$query = $this->db->count_all_results();

		header('Content-Type: application/json');
		echo json_encode($query);
	}

	public function register_new_user(){
		$username = $this->input->post('email');
		$password = $this->input->post('password');
		$email = $this->input->post('email');
		
		$additional_data = array(
			'first_name' => $this->input->post('fname'),
			'last_name' => $this->input->post('lname'),
	        'phone'  => $this->input->post('tel'),
	    );

        if ($this->ion_auth->register($username, $password, $email, $additional_data)) {
                $query = $this->db->insert_id();
                $data = array("user_id"=>$query);

                $this->load->library('email');
                
                $this->email->from('kreaserv@hotmail.com', 'Kreaserv');
                $this->email->to($username);
                $this->email->cc('kreaserv@hotmail.com');
                
                $this->email->subject('Registeration Success Mail');
                $this->email->message('You have been registered successfully with '.$username.' and '.$password);
                
                $this->email->send();
                
                echo $this->email->print_debugger();
				
				header('Content-Type: application/json');
				echo json_encode($data);
        }
        else {
                echo 'failed';
        }

	}

	public function get_users_info(){
		$user_id = $this->input->post('user_id');

		$this->db->where('id', $user_id);
		$query = $this->db->get('users');
		$results = $query->result();

		header('Content-Type: application/json');
		echo json_encode($results);
	}


	public function get_auctions(){

		$id = $this->input->post('id');
		$filter = $this->input->post('filter');
		$current_date = date("Y-m-d H:i:s");
		

		if($filter == 'online'){
			$this->db->where('category_name', $id);
			$this->db->where('auction_start_date <', $current_date);
			$this->db->where('auction_end_date >', $current_date);
	    }

		if($filter == 'ended'){
			$this->db->where('category_name', $id);
			$this->db->where('auction_end_date <', $current_date);
			$this->db->where('archive_date >', $current_date);
	    }

		if($filter == 'upcoming'){
			$this->db->where('category_name', $id);
			$this->db->where('auction_start_date >', $current_date);
	    }
		if($filter == 'archived'){
			$this->db->where('category_name', $id);
			$this->db->where('auction_end_date <', $current_date);
	    	$this->db->where('archive_date <', $current_date);
	    }
		
		$query = $this->db->get('auction');
		if($query->num_rows() == 0){
			$results[] = array('id' => 0, );
		} else {
			$results = $query->result();
		}

		header('Content-Type: application/json');
		echo json_encode($results);
	}

	public function chk_pin(){
		if ($this->ion_auth->logged_in())
		{
			$users = $this->ion_auth->user()->result();				

			if ($users->row()->pin == 0){
				$users = 'undefined';
			} 
		}
		header('Content-Type: application/json');
		echo json_encode($users);		
	}

	public function pin_user(){
		$pin = $this->input->post('pin');

		$this->db->where('pin', $pin);
		$query = $this->db->get('users');
		$users = $query->result();
		header('Content-Type: application/json');
		echo json_encode($users);		
	}

	public function get_product_list(){
		$auction_id = $this->input->post('auction_id');
		$category_id = $this->input->post('category_id');

		$highestamount[] = $this->highestamount_data($auction_id, $category_id);
		$lowestamount[] = $this->lowestamount_data($auction_id, $category_id);

		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$query = $this->db->get('items');

		foreach ($query->result() as $value) {

			$this->db->where('id', $value->item_name);
			$quer = $this->db->get('filter');

			$this->db->where('item_id', $quer->row()->id);
			$this->db->from('product_images');
			$que = $this->db->count_all_results();

			$this->db->where('item_id', $quer->row()->id);
			$this->db->where('constraint_id', 'Weight');
			$q = $this->db->get('itemfilterrel');

			$data[] = array(
				'id' => $quer->row()->id,
				'name' => $quer->row()->filter_name,
				'image' => $value->image,
				'no_of_images' => $que,
				'weight' => $q->row()->constraint_value,
				'bid_amount' => $value->bid_amount,
				'floor_amount' => $value->floor_amount,
				'sorthighest' => $highestamount,
				'sortlowest' => $lowestamount
			);

		}

		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function highestamount_data($auction_id, $category_id){

		$this->db->where('bid_amount >', 5000);
		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$query = $this->db->get('items');

		$data = array('id' => 0);
		foreach ($query->result() as $value) {

			$this->db->where('id', $value->item_name);
			$quer = $this->db->get('filter');

			$this->db->where('item_id', $quer->row()->id);
			$this->db->from('product_images');
			$que = $this->db->count_all_results();

			$this->db->where('item_id', $quer->row()->id);
			$this->db->where('constraint_id', 'Weight');
			$q = $this->db->get('itemfilterrel');

			$data = array(
				'id' => $quer->row()->id,
				'name' => $quer->row()->filter_name,
				'image' => $value->image,
				'no_of_images' => $que,
				'weight' => $q->row()->constraint_value,
				'bid_amount' => $value->bid_amount,
				'floor_amount' => $value->floor_amount
			);

		}

		return ($data);
	}


	function lowestamount_data($auction_id, $category_id){

		$this->db->where('auction', $auction_id);
		$this->db->where('category', $category_id);
		$this->db->where('bid_amount <', 5000);
		$query = $this->db->get('items');

		$data = array('id' => 0);

		foreach ($query->result() as $value) {

			$this->db->where('id', $value->item_name);
			$quer = $this->db->get('filter');

			$this->db->where('item_id', $quer->row()->id);
			$this->db->from('product_images');
			$que = $this->db->count_all_results();

			$this->db->where('item_id', $quer->row()->id);
			$this->db->where('constraint_id', 'Weight');
			$q = $this->db->get('itemfilterrel');

			$data = array(
				'id' => $quer->row()->id,
				'name' => $quer->row()->filter_name,
				'image' => $value->image,
				'no_of_images' => $que,
				'weight' => $q->row()->constraint_value,
				'bid_amount' => $value->bid_amount,
				'floor_amount' => $value->floor_amount
			);

		}

		return ($data);
	}

	//send selected products info through JSON
	//working
	public function get_products_info(){

		$product_id = $this->input->post('product_id');
		
		$this->db->where('item_name', $product_id);
		$query = $this->db->get('items');

		$this->db->where('id', $product_id);
		$quer = $this->db->get('filter');

		$this->db->where('item_id', $product_id);
		$this->db->from('product_images');
		$que = $this->db->count_all_results();

		$this->db->where('item_id', $product_id);
		$this->db->where('constraint_id', 'Weight');
		$q = $this->db->get('itemfilterrel');

		$this->db->order_by('priority', 'asc');
		$this->db->where('item_id', $product_id);
		$res = $this->db->get('product_images');
		$results = $res->result();

		$this->db->where('item_id', $product_id);
		$r = $this->db->get('itemfilterrel');
		$r = $r->result();

		$bid_date = $query->row()->bid_end_date;
		$bid_time = $query->row()->bid_time;
		$bid_time = $bid_date.' '.$bid_time;



		$data[] = array(
			'id' => $product_id,
			'name' => $quer->row()->filter_name,
			'image' => $query->row()->image,
			'description' => $query->row()->description,
			'no_of_images' => $que,
			'weight' => $q->row()->constraint_value,
			'bid_amount' => $query->row()->bid_amount,
			'floor_amount' => $query->row()->floor_amount,
			'bid_time' => $bid_time,
			'product_images' => $results,
			'filters' => $r
		);

		header('Content-Type: application/json');
		echo json_encode($data);
	}

	public function get_product_images($id){
		$this->db->order_by('priority', 'asc');
		$this->db->where('item_id', $id);
		$query = $this->db->get('product_images');
		$results = $query->result();

		header('Content-Type: application/json');
		echo json_encode($results);
	}
	
	function get_single_image(){
		$id = $this->input->post('id');

	    $this->db->where('id',$id);
	    $query = $this->db->get('product_images');

	    $data[] = array(
	    	'image' => $query->row()->url
	    );

		header('Content-Type: application/json');
		echo json_encode($data);
	}

	//create new user and item relation in uirelation table
	public function insert_uirelation($user_id, $product_id){
		$data = array(
		   'user_id' => $user_id ,
		   'product_id' => $product_id ,
		   'favourite' => 0 ,
		   'viewed' => 1 ,
		   'notification' => 0 ,
		   'bidded_amount' => 0 
		);

		$this->db->insert('uirelation', $data); 
	}

	public function rel_exists(){
		$product_id = $this->input->post('product_id');
		$user_id = $this->input->post('user_id');

	    $this->db->where('user_id',$user_id);
	    $this->db->where('product_id', $product_id);
	    $query = $this->db->get('uirelation');
	    if ($query->num_rows() > 0){
			return true;		    	
	    }
	    else{
	        $this->insert_uirelation($user_id, $product_id);
	    }
	}

/*	function set_product_flag(){
		$product_id = $this->input->post('product_id');
		$user_id = $this->input->post('user_id');

		$this->db->set('favourite', 1);
		$this->db->where('product_id', $product_id);
		$this->db->where('user_id', $user_id);
		$this->db->update('uirelation');
	}*/


	//send status for the selected products bid status through JSON
	public function check_bid_status(){
		$user_id = $this->input->post('user_id');
		$product_id = $this->input->post('product_id');

		$this->db->where('user_id', $user_id);
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('uirelation');
 		$results = $query->result();

		header('Content-Type: application/json');
		echo json_encode($results);
	}
		
	public function get_products_single_image($item_id){

		$this->db->where('item_id', $item_id);
		$this->db->order_by('priority', asc);
		$query = $this->db->get('product_images', 1);
		$results = $query->result();
	}

	public function get_notification(){
		$user_id = $this->input->post('user_id');
		$this->db->where('user_id', $user_id);
		$this->db->where('notification', '1');
		$query = $this->db->get('uirelation');

		if($query->num_rows() == 0){
			$data['results'][] = array(
				'id' => 0
			);
		} else {

			$results = $query->result();
			
			foreach ($query->result() as $value){
				$this->db->where('id', $value->product_id);
				$q = $this->db->get('filter');

				$this->db->where('item_name', $value->product_id);
				$quer = $this->db->get('items');

				$this->db->where('item_id', $value->product_id);
				$this->db->from('product_images');
				$que = $this->db->count_all_results();

				$this->db->where('item_id', $value->product_id);
				$this->db->where('constraint_id', 'Weight');
				$res = $this->db->get('itemfilterrel');

			$data['results'][] = array(
						'id'         => $q->row()->id,
						'name'  	 => $q->row()->filter_name,
						'image'  		 => $quer->row()->image,
						'weight'  		 => $res->row()->constraint_value,
						'no_of_images' => $que,
						'bid_amount' => $quer->row()->bid_amount,
						'floor_amount' => $quer->row()->floor_amount,
					);
			}
		}
		header('Content-Type: application/json');
		echo json_encode($data);

	}

	public function get_flagged_products(){

		$user_id = $this->input->post('user_id');
		$filter = $this->input->post('filter');

		if ($filter == 'favourite') {
			$this->db->where('user_id', $user_id);
			$this->db->where('favourite', '1');
			$query = $this->db->get('uirelation');

			if($query->num_rows() == 0){
				$data['results'][] = array(
					'id' => 0
				);
			} else {

			$results = $query->result();
			
			foreach ($query->result() as $value){
				$this->db->where('id', $value->product_id);
				$q = $this->db->get('filter');

				$this->db->where('item_name', $value->product_id);
				$quer = $this->db->get('items');

				$this->db->where('item_id', $value->product_id);
				$this->db->from('product_images');
				$que = $this->db->count_all_results();

				$this->db->where('item_id', $value->product_id);
				$this->db->where('constraint_id', 'Weight');
				$res = $this->db->get('itemfilterrel');

			$data['results'][] = array(
						'id'         => $q->row()->id,
						'name'  	 => $q->row()->filter_name,
						'image'  		 => $quer->row()->image,
						'weight'  		 => $res->row()->constraint_value,
						'no_of_images' => $que,
						'bid_status' => $value->bid,
						'bid_amount' => $quer->row()->bid_amount,
						'floor_amount' => $quer->row()->floor_amount,
					);
			}
			}
		}	

		if ($filter == 'bid') {
			$this->db->where('user_id', $user_id);
			$this->db->where('bid', '2');
			$query = $this->db->get('uirelation');
			if($query->num_rows() == 0){
				$data['results'][] = array(
					'id' => 0
				);
			} else {
			$results = $query->result();

			foreach ($query->result() as $value){

					$this->db->where('id', $value->product_id);
					$q = $this->db->get('filter');

					$this->db->where('item_name', $value->product_id);
					$quer = $this->db->get('items');

					$this->db->where('item_id', $value->product_id);
					$this->db->from('product_images');
					$que = $this->db->count_all_results();

					$this->db->where('item_id', $value->product_id);
					$this->db->where('constraint_id', 'Weight');
					$res = $this->db->get('itemfilterrel');

					$data['results'][] = array(
						'id'         => $q->row()->id,
						'name'  	 => $q->row()->filter_name,
						'image'  		 => $quer->row()->image,
						'weight'  		 => $res->row()->constraint_value,
						'no_of_images' => $que,
						'bid_amount' => $quer->row()->bid_amount,
						'floor_amount' => $quer->row()->floor_amount,
					);
				}
			}
		}

		if ($filter == 'viewed') {
			$this->db->where('user_id', $user_id);
			$this->db->where('viewed', '1');
			$query = $this->db->get('uirelation');
			if($query->num_rows() == 0){
				$data['results'][] = array(
					'id' => 0
				);
			} else {

			$results = $query->result();

			foreach ($query->result() as $value){

					$this->db->where('id', $value->product_id);
					$q = $this->db->get('filter');

					$this->db->where('item_name', $value->product_id);
					$quer = $this->db->get('items');

					$this->db->where('item_id', $value->product_id);
					$this->db->from('product_images');
					$que = $this->db->count_all_results();

					$this->db->where('item_id', $value->product_id);
					$this->db->where('constraint_id', 'Weight');
					$res = $this->db->get('itemfilterrel');

					$data['results'][] = array(
						'id'         => $q->row()->id,
						'name'  	 => $q->row()->filter_name,
						'image'  		 => $quer->row()->image,
						'weight'  		 => $res->row()->constraint_value,
						'no_of_images' => $que,
						'bid_amount' => $quer->row()->bid_amount,
						'floor_amount' => $quer->row()->floor_amount,
					);
				}
			}
		}
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	function ckh_valid_bidamount(){
		$product_id = $this->input->post('product_id');
		$bid_amount = $this->input->post('bid_amount');
		$user_id = $this->input->post('user_id');

		$current_date = date("Y-m-d H:i:s");

		$this->db->where('item_name', $product_id);
		$query = $this->db->get('items');

		$bid_date = $query->row()->bid_end_date;
		$bid_time = $query->row()->bid_time;
		$bid_time = $bid_date.' '.$bid_time;

		if ($current_date < $bid_time){

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
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	function bid_execute(){
		$product_id = $this->input->post('product_id');
		$bid_amount = $this->input->post('bid_amount');
		$user_id = $this->input->post('user_id');

		$old_values = $this->get_old_user($product_id);

		$this->db->set('bid_amount', $bid_amount);
		$this->db->where('item_name', $product_id);
		$this->db->update('items');

		$this->db->set('amount','amount - '.$bid_amount, false);
		$this->db->where('id', $user_id);
		$this->db->update('users');

		$this->db->set('favourite', 1);
		$this->db->set('bid', 2);
		$this->db->set('notification', 0);
		$this->db->set('bidded_amount', $bid_amount);
		$this->db->where('user_id', $user_id);
		$this->db->where('product_id', $product_id);
		$this->db->update('uirelation');

		$this->db->set('amount','amount + '.$old_values['bidded_amount'], false);
		$this->db->where('id', $old_values['old_id']);
		$this->db->update('users');

		$logs = array(
		               'product_id' => $product_id,
		               'user_id' => $user_id,
		               'bid_amount' => $bid_amount
		            );

		$this->db->insert('log', $logs);

		$this->db->set('bid', 1);
		$this->db->set('notification', 1);
		$this->db->set('bidded_amount', 0);
		$this->db->where('user_id', $old_values['old_id']);
		$this->db->where('product_id', $product_id);
		$this->db->update('uirelation');


	}

	 public function get_old_user($product_id){

		$this->db->where('product_id', $product_id);
		$this->db->where('bid', 2);
		$query = $this->db->get('uirelation');
		$data = array(
			'old_id' => $query->row()->user_id,
			'bidded_amount' => $query->row()->bidded_amount,
		);
		return $data;
	}

	public function login_user(){
		$identity = $this->input->post('username');
		$password = $this->input->post('password');

		$identity = $identity;
		$password = $password;
		$remember = TRUE; // remember the user
		if($this->ion_auth->login($identity, $password, $remember)){
			$data = $this->ion_auth->user()->result();
		} else {
			$data[] = array('id' => '0', );
		}
		header('Content-Type: application/json');
		echo json_encode($data);		
	}

	function logout(){
		if($this->ion_auth->logout()){
			$data =  array( 'id' => 1 );
		} else {
			$data =  array( 'id' => 0 );
		}
	}

	function get_bid_amount(){
		$product_id = $this->input->post('product_id');
		$this->db->where('item_name', $product_id);
		$query = $this->db->get('items');

		$data[] = array(
			'bid_amount' => $query->row()->bid_amount 
		);
		header('Content-Type: application/json');
		echo json_encode($data);		
	}

	function get_notification_data(){
		$user_id = $this->input->post('user_id');
		$this->db->where('user_id', $user_id);
		$this->db->where('notification', 1);
		$query = $this->db->get('uirelation');

		if($query->num_rows() > 0){
			
			foreach ($query->result() as $value) {
				$this->db->where('id', $value->product_id);
				$name = $this->db->get('filter');

				$this->db->where('item_name', $value->product_id);
				$amount = $this->db->get('items');

				$data[] = array(
					'product_name' => $name->row()->filter_name,
					'bid_amount' => $amount->row()->bid_amount
				);

			}

		} else {
			$data[] = array(
				'id' => 0
			);
		}

	header('Content-Type: application/json');
	echo json_encode($data);
	}

	function remove_notification(){
/*		$user_id = $this->input->post('user_id');*/
		$this->db->set('notification', 0);
		$this->db->where('user_id', $user_id);
		$query = $this->db->get('uirelation');
	}

/*	public function download_file(){

		$file = 'assets/uploads/02a29-3.jpg';
	 	header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($file));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    readfile($file);
	}*/

}