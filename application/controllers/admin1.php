<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin1 extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login');
		}
		$this->load->library('image_CRUD');
	}

	public function index()
	{
		$arr = array();
		//$results = $this->db->query('SELECT * FROM log LEFT OUTER JOIN (SELECT item_name,image,auction FROM `items` WHERE auction in (select auction_id from auction)) as itm ON log.product_id = itm.item_name LEFT OUTER JOIN auction on itm.auction = auction.auction_id');
		
		// $results = $this->db->query('Select * from filter inner join (SELECT id,product_id,user_id,bid_amount,item_name,image,auction,auction_id,auction_name,category_name,city,country,address,auction_start_date,auction_end_date,archive_date FROM log LEFT OUTER JOIN (SELECT item_name,image,auction FROM `items` WHERE auction in (select auction_id from auction)) as itm ON log.product_id = itm.item_name LEFT OUTER JOIN auction on itm.auction = auction.auction_id) `right_tab` on filter.id = `right_tab`.item_name order by `right_tab`.auction_name;');
		// $data['auction_data'] = $results->result();
		// $this->load->view('admin/dashboard', $data);
		$data['dashboard'] = $this->db->query('SELECT * FROM auctions JOIN items ON auctions.id = items.auction JOIN log ON items.id = log.item_id')->result();
		echo $data;
		die();
		redirect('admin/dashboard');
	}

	public function dashboard(){
		// $query = $this->db->get('auction');
		// foreach ($query->result() as $value) {
		// 	$this->db->where('auction', $value->auction_id);
		// 	$quer = $this->db->get('items');
		// 	foreach ($quer->result() as $valu) {
		// 		$this->db->where('id', $valu->item_name);
		// 		$que = $this->db->get('filter');
		// 		foreach ($que->result() as $val) {
		// 			$this->db->where('product_id', $val->id);
		// 			$q = $this->db->get('log');
		// 			if ($q->num_rows() == 0){
		// 				$log[] = array(
		// 					'product_id' => 0,
		// 					'user_id' => 0,
		// 					'bid_amount' => 0
		// 				);						
		// 			} else {
		// 				foreach ($q->result() as $v) {
		// 					$log[] = array(
		// 						'product_id' => $v->product_id,
		// 						'user_id' => $v->user_id,
		// 						'bid_amount' => $v->bid_amount
		// 					);
		// 				}
		// 			}
		// 			$product[] = array(
		// 				'item_name' => $val->filter_name,
		// 				'bid_amount' => $valu->bid_amount,
		// 				'logs' => $log
		// 			);						
		// 		}
		// 	}
		// 	$product_data[] = array(
		// 		'auction_id' => $value->auction_id,
		// 		'auction_name' => $value->auction_name,
		// 		'product' => $product
		// 	);
		// }

		// $data['products'] = $product_data;

		$arr = array();
		//$results = $this->db->query('SELECT * FROM log LEFT OUTER JOIN (SELECT item_name,image,auction FROM `items` WHERE auction in (select auction_id from auction)) as itm ON log.product_id = itm.item_name LEFT OUTER JOIN auction on itm.auction = auction.auction_id');
		
		$results = $this->db->query('Select * from filter inner join (SELECT id,product_id,user_id,bid_amount,item_name,image,auction,auction_id,auction_name,category_name,city,country,address,auction_start_date,auction_end_date,archive_date FROM log LEFT OUTER JOIN (SELECT item_name,image,auction FROM `items` WHERE auction in (select auction_id from auction)) as itm ON log.product_id = itm.item_name LEFT OUTER JOIN auction on itm.auction = auction.auction_id) `right_tab` on filter.id = `right_tab`.item_name order by `right_tab`.auction_name;');
		$data['auction_data'] = $results->result();
		$this->load->view('admin/dashboard', $data);
		//foreach ($data as $value) {
		//	print_r($value);
		//}


/*		foreach($data as $key => $item)
		{
		   $arr[$item['auction']][$key] = $item;
		}
		print_r($arr);
		ksort($arr, SORT_NUMERIC);	*/
	}

	// Sellers Section
	public function seller()
	{
		$crud = new grocery_CRUD();
	    $crud->set_table('sellers');
		$crud->set_subject('sellers');

	    $crud->set_rules('seller_name','Seller name','required');
	    $crud->set_rules('email_id','Email id','required');
	    $crud->set_rules('contact_no','Contact no','required');
	    $crud->set_rules('location','Location','required');
	    $crud->set_rules('city','City','required');
	    $crud->set_rules('country','Country','required');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		
		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	// Categories Section
	public function category()
	{
		$crud = new grocery_CRUD();
	    $crud->set_table('category');
		$crud->set_subject('category');

		$crud->set_field_upload('image','assets/uploads/');
		$crud->callback_before_upload(array($this,'_file_validator_images'));
		$crud->callback_after_upload(array($this,'resize_614x300'));
		
		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	//Auction Section
	public function auction($filter = 'all')
	{
		$config['grocery_crud_default_datetime_format'] = 'Y-m-d H:i';

		$crud = new grocery_CRUD('default'); 
	    $crud->set_table('auction');
		$crud->set_subject('auction');

		$crud->field_type('archive_date', 'datetime');

	    $crud->set_relation('category_name','category','category_name');

		$crud->set_rules('auction_name','Auction name','required');
		$crud->set_rules('city','City','required');
		$crud->set_rules('country','Country','required');
		$crud->set_rules('location','Location','required');
		$crud->set_rules('auction_start_date','Auction start date','required');
		$crud->set_rules('auction_end_date','Auction end date','required');
		$crud->set_rules('archive_date','Archive date','required');
		$crud->callback_add_field('default_value', function () {
	        return '<input name="date" type="text" value="'. current_time() . '" maxlength="19" class="datetime-input">';
		});


		$current_date = date("Y-m-d H:i:s");


		if($filter == 'archived'){
			$crud->where('auction_end_date <', $current_date);
	    	$crud->where('archive_date <', $current_date);
	    }

		if($filter == 'online'){
			$crud->where('auction_start_date <', $current_date);
			$crud->where('auction_end_date >', $current_date);
	    }

		if($filter == 'ended'){
			$crud->where('auction_end_date <', $current_date);
			$crud->where('archive_date >', $current_date);
	    }

		if($filter == 'upcoming'){
			$crud->where('auction_start_date >', $current_date);
	    }

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	//Additional Functions
	function _file_validator_images($files_to_upload,$field_info) {
		$type = $files_to_upload[$field_info->encrypted_field_name]['type'];
		$types = array('image/png','image/jpg','image/jpeg');
		if (!in_array($type, $types)) {
			return 'Sorry, we can upload only jpeg/png here.';
		}
	}

	function resize_614x300($uploader_response,$field_info, $files_to_upload) {
	    $this->load->library('image_moo');
	    $this->image_moo->set_background_colour($html_colour='#000000');
	    $file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name;  
	    $this->image_moo->load($file_uploaded)->resize(576,300,true)->save($file_uploaded,true);
	    return true;
	}

/*	public function constraints()
	{
		$crud = new grocery_CRUD();
	    $crud->set_table('constraints');
		$crud->set_subject('constraints');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}*/


	//Filter Section	
	public function filter()	
	{
		$crud = new grocery_CRUD('default'); 
	    $crud->set_table('filter');
		$crud->set_subject('filter');

		$crud->callback_add_field('default_value', function () { return '<input type="text" maxlength="50" value="" name="default_value" disabled>';});

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$crud->add_action('Photos', '', '','fa fa-2x fa-picture-o fancybox fancybox.iframe',array($this,'redirect_to_image_crud'));
		$crud->add_action('Tags', '', '','fa fa-2x fa-tags fancybox fancybox.iframe',array($this,'tag_crud'));

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	//Products Section
	public function items(){

		$crud = new grocery_CRUD();
		$crud->set_table('items');
		$crud->set_subject('items');

		$crud->set_field_upload('image','assets/uploads/');

	    $crud->set_rules('item_name','Item name','required');
	    $crud->set_rules('description','Description','required');
	    $crud->set_rules('bid_time','Bid time','required');
	    $crud->set_rules('floor_amount','Floor amount','required');
	    $crud->set_rules('seller','Seller','required');
	    $crud->set_rules('category','Category','required');

	    $crud->set_relation('item_name','filter','filter_name');
	    $crud->set_relation('seller','sellers','seller_name');
		$crud->set_relation('category','category','category_name');
		$crud->set_relation('auction','auction','auction_name');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	function redirect_to_image_crud($primary_key,$row) {
	    return site_url('admin/images/').'/'.$row->id;
	}

	public function images()
	{
		$image_crud = new image_CRUD();
		$image_crud->set_primary_key_field('id');
		$image_crud->set_url_field('url');
		$image_crud->set_table('product_images')
		->set_relation_field('item_id')
		->set_ordering_field('priority')
		->set_image_path('assets/uploads');

		$data = $image_crud->render();
		$this->load->view('admin/image_crud_view',$data);
	}

	function tag_crud($primary_key,$row) {
	    return site_url('admin/tags/').'/'.$row->id;
	}	

	public function tags($p_key){

		$crud = new grocery_CRUD();
		$this->tags_pk = $p_key;
		$crud->where('item_id',$p_key);
		$crud->set_table('itemfilterrel');
	    $crud->set_subject('tags');

		$state = $crud->getState();

		$crud->callback_add_field('item_id', function () {
			return '<input type="hidden" maxlength="50" value="'.$this->tags_pk.'" name="item_id" >';
		});
		$crud->field_type('item_id', 'hidden');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		$data = $crud->render();
		$this->load->view('admin/image_crud_view',$data);
	}

	public function users($filter = 'all') {

		$crud = new grocery_CRUD();
 
	    $crud->set_table('users');
	    $crud->set_subject('users');
	    $crud->set_rules('username','Username','required');
	    $crud->set_rules('email','Email','required');
	    $crud->set_rules('phone','Phone','required');
	    $crud->set_rules('country','Country','required');
	    $crud->set_rules('city','City','required');
	    $crud->set_rules('verified','Verified','required');
	    $crud->set_rules('amount','Amount','required');


	    if($filter == 'verified'){
	    	$crud->where('verified',1);
			$crud->add_action('Block', '', '','fa-2x fa fa-times',array($this,'verify_user'));
	    }

	    if($filter == 'unverified'){
	    	$crud->where('verified', 0);
			$crud->add_action('Verify', '', '','fa-2x fa fa-check',array($this,'verify_user'));
	    }

		$crud->callback_after_update(array($this,'insert_ulog'));

		$crud->unset_columns('created_on','ip_address','password','salt','activation_code','forgotten_password_code','forgotten_password_time','pin','active','verified','remember_code','last_login');
		$crud->unset_edit_fields('created_on','ip_address','password','salt','activation_code','forgotten_password_code','forgotten_password_time','pin','active','remember_code','last_login');
		$crud->unset_add();
 
		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	function insert_ulog($post_array, $primary_key){
		$this->db->where('user_id', $primary_key);
		$user = $this->db->get('user_log');

		if ($user->num_rows() == 0){
			
			$data = array(
				'user_id' => $primary_key,
				'username' => $post_array['username'],
				'first_name' => $post_array['first_name'],
				'last_name' => $post_array['last_name'],
				'amount' => $post_array['amount']
			);

			$this->db->insert('user_log', $data);

		}
	}
	
	function verify_user($primary_key , $row) {

		if ($row->verified){
			return site_url('admin/unverify/').'/'.$primary_key;	
		} else {
			return site_url('admin/verify/').'/'.$primary_key;
		}
	    
	}

	public function verify($pk){
		$this->db->where('id',$pk);
		$this->db->update('users',array('verified'=>1));
		redirect('admin/users/unverified');
	}

	public function unverify($pk){
		$this->db->where('id',$pk);
		$this->db->update('users',array('verified'=>0));
		redirect('admin/users/verified');
	}
}