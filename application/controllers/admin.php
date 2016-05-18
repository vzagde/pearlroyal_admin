<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
		// $arr = array();
		
		// $results = $this->db->query('Select * from filter inner join (SELECT id,product_id,user_id,bid_amount,item_name,image,auction,auction_id,auction_name,category_name,city,country,address,auction_start_date,auction_end_date,archive_date FROM log LEFT OUTER JOIN (SELECT item_name,image,auction FROM `items` WHERE auction in (select auction_id from auction)) as itm ON log.product_id = itm.item_name LEFT OUTER JOIN auction on itm.auction = auction.auction_id) `right_tab` on filter.id = `right_tab`.item_name order by `right_tab`.auction_name;');
		// $data['auction_data'] = $results->result();
		// $this->load->view('admin/dashboard', $data);
		// redirect('admin/category');
		redirect('admin/user_product');
	}

	public function dashboard(){
		$data['dashboard'] = $this->db->query('SELECT items.id, items.auction, auctions.auction_name, items.item_name, log.bid_amount, log.user_id FROM log JOIN items ON log.item_id = items.id JOIN auctions ON items.auction = auctions.id ORDER BY auctions.id')->result();
		$this->load->view('admin/dashboard', $data);
	}

	public function user_product(){
		$crud = new grocery_CRUD();
		$this->db->group_by('item_id');
		$this->db->order_by('id', 'desc');
        $crud->set_table('uirelation');
        $crud->columns('user_id', 'item_id', 'bidded_amount');
        $crud->set_relation('user_id', 'users', 'first_name');
        $crud->set_relation('item_id', 'items', 'item_name');
		$crud->set_subject('Product Records');
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	public function temp_dashboard($list){
		$crud = new grocery_CRUD();
		$crud->set_table('items');

		if($list=="auc"){
			$crud->order_by('auction','asc');
		}
		if($list=="lot"){
			$crud->order_by('item_key','asc');
		}

		$crud->set_subject('Items');

		$crud->columns('item_name','category','floor_amount','bid_amount','seller','auction','item_key');
		$crud->unset_edit();
		$crud->unset_add();
		$crud->unset_delete();

	    $crud->set_relation('seller','sellers','seller_name');
		$crud->set_relation('category','categories','category_name');
		$crud->set_relation('auction','auctions','auction_name');

		$crud->add_action('Item Bidding Data', '', '','fa fa-2x fa-table',array($this,'redirect_to_next_page'));

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	function redirect_to_next_page($primary_key,$row){
	    return site_url('admin/bid_details/').'/'.$row->id;
	}

	public function bid_details($id){
		$crud = new grocery_CRUD();
		$crud->where('uirelation.item_id',$id);
		$crud->set_table('uirelation');
		$crud->order_by('created_date','desc');
		$crud->set_subject('Biding Details');
		$crud->columns('item_id','user_id','bidded_amount','previous_biding','created_date');

		$crud->unset_edit();
		$crud->unset_add();
		$crud->unset_delete();

	    $crud->set_relation('item_id','items','item_name');
		$crud->set_relation('user_id','users','email');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	public function category()
	{
		$crud = new grocery_CRUD();
	    $crud->set_table('categories');
		$crud->set_subject('category');

		$crud->set_field_upload('image','assets/uploads/');
		$crud->callback_before_upload(array($this,'_file_validator_images'));
		
		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$crud->add_action('filters', '', '','fa fa-2x fa-tags fancybox fancybox.iframe',array($this,'filter_crud'));

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	function _file_validator_images($files_to_upload,$field_info) {
		$type = $files_to_upload[$field_info->encrypted_field_name]['type'];
		$types = array('image/png','image/jpg','image/jpeg');
		if (!in_array($type, $types)) {
			return 'Sorry, we can upload only jpeg/png here.';
		}
	}

	function filter_crud($primary_key,$row) {
	    return site_url('admin/catfilters/').'/'.$row->id;
	}
	
	public function get_autocompletevalues(){
		$this->db->where("filter_type", $this->input->post('catfiltername'));
		$result = $this->db->get('categoryfilterrel')->result();
		$ret_arr = array();
		foreach($result as $result_row){
			$ret_arr[] = $result_row->filter_name;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($ret_arr));
	}
	
	public function get_autocompletevalues_items(){
		//$this->db->query('select distinct filter_value from ');
		$this->db->distinct();
		$this->db->select('filter_value');
		$this->db->from('itemfilterrel');
		$this->db->where("filter_id", $this->input->post('filter_id'));
		$result = $this->db->get()->result();
		$ret_arr = array();
		foreach($result as $result_row){
			$ret_arr[] = $result_row->filter_value;
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($ret_arr));
	}

	public function catfilters($p_key){

		$crud = new grocery_CRUD();
		$this->tags_pk = $p_key;
		$crud->where('category_id',$p_key);
		$crud->set_table('categoryfilterrel');
	    $crud->set_subject('Filters');

		$state = $crud->getState();

		$crud->callback_add_field('category_id', function () {
			return '<input type="hidden" maxlength="50" value="'.$this->tags_pk.'" name="category_id" >';
		});
		$crud->field_type('category_id', 'hidden');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		$data = $crud->render();
		$this->load->view('admin/image_crud_view',$data);
	}

	public function seller()
	{
		$crud = new grocery_CRUD();
	    $crud->set_table('sellers');
		$crud->set_subject('Seller');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		
		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	public function auction($filter = 'all')
	{
		$config['grocery_crud_default_datetime_format'] = 'Y-m-d H:i';

		$crud = new grocery_CRUD('default'); 
     	$crud->set_table('auctions');
		$crud->set_subject('Auction');

		$current_date = date("Y-m-d");

		if($filter == 'archived'){
			$crud->where('auction_end_date <', $current_date);
	    	$crud->where('auction_archive_date <', $current_date);
	    }

		if($filter == 'online'){
			$crud->where('auction_start_date <=', $current_date);
			$crud->where('auction_end_date >', $current_date);
	    }

		if($filter == 'ended'){
			$crud->where('auction_end_date <', $current_date);
			$crud->where('auction_archive_date >', $current_date);
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

	public function users($filter = 'all') {

		$crud = new grocery_CRUD();
 
	    $crud->set_table('users');
	    $crud->set_subject('users');
	    $crud->order_by('id','desc');
		$crud->columns('id','username','email','first_name','last_name','company','phone','country','city','amount');
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

	public function items(){

		$crud = new grocery_CRUD();
		$crud->set_table('items');
		$crud->set_subject('Items');
		$crud->columns('item_name','category','floor_amount','bid_amount','seller','auction','item_key');
		$crud->set_field_upload('image','assets/uploads/');

		$crud->field_type('bid_amount', 'hidden');
		$crud->field_type('item_id', 'hidden');
	    
	    $crud->set_relation('seller','sellers','seller_name');
		$crud->set_relation('category','categories','category_name');
		$crud->set_relation('auction','auctions','auction_name');

		$crud->callback_before_insert(array($this,'edit_item_name'));
		$crud->callback_before_update(array($this,'edit_item_name'));

		$crud->add_action('Photos', '', '','fa fa-2x fa-picture-o',array($this,'redirect_to_image_crud'));
		$crud->add_action('filters_value', '', '','fa fa-2x fa-tags',array($this,'filter_value_crud'));

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	function edit_item_name($post_array) {
		$post_array['item_id'] = $post_array['item_name'].''.$post_array['auction'].''.$post_array['category'];

		return $post_array;
	}

	function filter_value_crud($primary_key,$row) {
	    return site_url('admin/itemfilters/').'/'.$row->id.'/'.$row->category;
	}

	public function itemfilters($p_key,$cat_id){
		$crud = new grocery_CRUD();
		$this->tags_pk = $p_key;
		$crud->where('item_id',$p_key);
		$crud->set_table('itemfilterrel');
	    $crud->set_subject('Filters values');

		$state = $crud->getState();

		$crud->callback_add_field('item_id', function () {
			return '<input type="hidden" maxlength="50" value="'.$this->tags_pk.'" name="item_id" >';
		});
		$crud->field_type('item_id', 'hidden');

		$crud->set_relation('filter_id','categoryfilterrel','filter_name',array('category_id' => $cat_id));

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
		$image_crud->set_table('item_images')
		->set_relation_field('item_id')
		->set_ordering_field('priority')
		->set_image_path('assets/uploads');

		$data = $image_crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	public function usd_text()
	{
		$crud = new grocery_CRUD();
	    $crud->set_table('usd_text');
		$crud->set_subject('USD Related Text');
		$crud->unset_delete();

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');

		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

	public function dashboard_1(){
		$data['category'] = $this->db->get('categories')->result();
		$data['title'] = 'Item Dashboard';
		$data['page_title'] = 'Categories';
		$this->load->view('admin/dashboard__view', $data);
	}

	public function auctions_dashboard($cat){
		$query = 'SELECT a.id, 
						 a.auction_name, 
						 a.auction_start_date, 
						 a.auction_end_date, 
						 a.address, 
						 a.city, 
						 a.country, 
						 a.auction_archive_date, 
						 c.id as cat_id 
					FROM auctions as a 
					JOIN items as i 
					ON a.id = i.auction 
					JOIN categories as c 
					ON i.category = c.id 
					WHERE c.id ='.$cat.'
					GROUP BY a.id';

		$data['auctions'] = $this->db->query($query)->result();
		$data['title'] = 'Item Dashboard';
		$data['page_title'] = 'Auctions';
		$this->load->view('admin/dashboard__view', $data);
	}

	public function item_dashboard($auc, $cat){
		$this->db->where('category', $cat);
		$this->db->where('auction', $auc);
		$data['items'] = $this->db->get('items')->result();
		$data['title'] = 'Item Dashboard';
		$data['page_title'] = 'Items';
		$this->load->view('admin/dashboard__view', $data);
	}

	public function item_details($item_id){
		$data['uirel'] = $this->db->query('SELECT uir.user_id, uir.item_id, uir.bidded_amount, uir.previous_biding, u.username, u.email, i.item_name, i.floor_amount, i.image, i.payment_status, i.paid_amount FROM uirelation as uir JOIN users as u ON uir.user_id = u.id JOIN items as i ON uir.item_id = i.id AND i.id ='.$item_id.' ORDER BY uir.bidded_amount DESC')->result();
		$data['paid_amount'] = $this->db->query('SELECT i.paid_amount FROM uirelation as uir JOIN users as u ON uir.user_id = u.id JOIN items as i ON uir.item_id = i.id AND i.id ='.$item_id.' ORDER BY uir.bidded_amount DESC')->result();
		$data['title'] = 'Item Dashboard';
		$data['page_title'] = 'UIItems1';
		$this->load->view('admin/dashboard__view', $data);
	}

	public function user_dashboard($auc, $cat){
		$data['uirelu'] = $this->db->query('SELECT users.email, users.id, users.amount FROM uirelation JOIN items ON uirelation.item_id = items.id AND items.auction = '.$auc.' AND items.category = '.$cat.' JOIN users ON uirelation.user_id = users.id GROUP BY uirelation.user_id ORDER BY users.id ASC')->result();
		$data['title'] = 'Item Dashboard';
		$data['page_title'] = 'UIUsers';
		$this->load->view('admin/dashboard__view', $data);
	}


	public function user_details($uid){
		$data['uirel'] = $this->db->query('SELECT uir.user_id, uir.item_id, uir.bidded_amount, uir.previous_biding, u.username, u.email, i.item_name, i.floor_amount, i.image, i.payment_status, i.paid_amount FROM uirelation as uir JOIN users as u ON uir.user_id = u.id JOIN items as i ON uir.item_id = i.id AND u.id ='.$uid.' ORDER BY uir.bidded_amount DESC')->result();
		$data['paid_amount'] = $this->db->query('SELECT SUM(i.paid_amount) AS paid_amount FROM uirelation as uir JOIN users as u ON uir.user_id = u.id JOIN items as i ON uir.item_id = i.id AND u.id ='.$uid.' ORDER BY uir.bidded_amount DESC')->result();
		$data['title'] = 'Item Dashboard';
		$data['page_title'] = 'UIItems2';
		$this->load->view('admin/dashboard__view', $data);
	}

	public function update_amount(){
		$this->input->post('user_id');
		$this->input->post('item_id');
		$this->input->post('amount');
		$status = "";
		$bid = "";

		$this->db->where('id', $this->input->post('item_id'));
		$amount = $this->db->get('items')->row()->bid_amount;

		$this->db->set('amount','amount + '.$this->input->post('amount'), false);
		$this->db->where('id', $this->input->post('user_id'));
		$this->db->update('users');

		if($amount > $this->input->post('amount')){
			$status = "Pending";
			$bid = "2";

		} elseif($amount == $this->input->post('amount')) {
			$status = "Amount Paid";
			$bid = "1";
		}

		if ($status != "") {
			$data = array(
					'paid_amount' => $this->input->post('amount'),
					'payment_status' => $status,
				);
			$this->db->where('id', $this->input->post('item_id'));
			$this->db->update('items', $data);
			$bid = array(
					'bid' => $bid
				);
			$this->db->where('item_id', $this->input->post('item_id'));
			$this->db->where('user_id', $this->input->post('user_id'));
			$this->db->update('uirelation', $bid);
			echo $amount - $this->input->post('amount');
		} else {
			echo "Unsuccess";
		}

	}

	public function about_content(){
		$crud = new grocery_CRUD();
	    $crud->set_table('about_us');
		$crud->set_subject('About Us Content');

		$crud->unset_add();
		$crud->unset_delete();

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		
		$data = $crud->render();
		$this->load->view('admin/crud_view',$data);
	}

/*

	function resize_614x300($uploader_response,$field_info, $files_to_upload) {
	    $this->load->library('image_moo');
	    $this->image_moo->set_background_colour($html_colour='#000000');
	    $file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name;  
	    $this->image_moo->load($file_uploaded)->resize(576,300,true)->save($file_uploaded,true);
	    return true;
	}
	
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

	*/

/*	public function catfilters($p_key){

		$crud = new grocery_CRUD();
		$this->tags_pk = $p_key;
		$crud->where('category_id',$p_key);
		$crud->set_table('test');
	    $crud->set_subject('Filters');

	    $crud->set_relation('filter_name','filters','filter_name');
	    $crud->callback_column('filter_name',array($this,'chk_filtertype'));

		$state = $crud->getState();

		$crud->callback_add_field('category_id', function () {
			return '<input type="hidden" maxlength="50" value="'.$this->tags_pk.'" name="category_id" >';
		});


		$crud->field_type('category_id', 'hidden');

		$crud->unset_columns('created_date');
		$crud->unset_add_fields('created_date');
		$crud->unset_edit_fields('created_date');
		$data = $crud->render();
		$this->load->view('admin/image_crud_view',$data);
	}

	function chk_filtertype($value, $row){
		$this->db->where('filter_name', $value);
		$qu = $this->db->get('filters');

		if ($qu->row()->type == 'dropdown'){
			$value = $qu->row()->values;
			$data = (explode(",",$value));
			echo $data;
			die();
			$crud->field_type('filter_value','dropdown',$data);
		}
	}*/
}