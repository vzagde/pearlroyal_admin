          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              
                  <p class="centered"><a href="javascript:void(0);"><img src="<?=base_url()?>assets/img/logo.png" class="" width="120px"></a></p>
                  <h5 class="centered"></h5>
<!--                   <li class="mt">
                      <a href="<?=base_url()?>index.php/admin/dashboard" class="active" id="menu_home">
                          <i class="fa fa-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li> -->
                  <li>
                      <a href="<?=base_url()?>index.php/admin/user_product" id="menu_categories">
                          <i class="fa fa-desktop"></i>
                          <span>Product Dashboard</span>
                      </a>
                  </li>
<!--                   <li>
                      <a href="<?=base_url()?>index.php/admin/temp_dashboard" id="menu_categories">
                          <i class="fa fa-desktop"></i>
                          <span>Temporary Dashboard</span>
                      </a>
                  </li>
 -->
                  <li class="sub-menu">
                      <a href="javascript:void(0);" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Item Dashboard</span>
                      </a>
                        <ul class="sub">
                          <li><a href="<?=base_url()?>index.php/admin/temp_dashboard/all">All Items</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/temp_dashboard/auc">Sort By Auctions</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/temp_dashboard/lot">Sort By Lot No.</a></li>
                      </ul>
                  </li>
                  <li>
                      <a href="<?=base_url()?>index.php/admin/category" id="menu_categories">
                          <i class="fa fa-desktop"></i>
                          <span>Categories</span>
                      </a>
                  </li>
                  <li>
                      <a href="<?=base_url()?>index.php/admin/seller" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Seller</span>
                      </a>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:void(0);" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Auctions</span>
                      </a>
                        <ul class="sub">
                          <li><a href="<?=base_url()?>index.php/admin/auction">All Auctions</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/auction/archived">Archived Auctions</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/auction/online">Online Auctions</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/auction/ended">Ended Auctions</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/auction/upcoming">Upcoming Auctions</a></li>
                      </ul>
                  </li>
                  <li>
                      <a href="<?=base_url()?>index.php/admin/items" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Items</span>
                      </a>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:void(0);" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Users</span>
                      </a>

                        <ul class="sub">
                          <li><a href="<?=base_url()?>index.php/admin/users">All users</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/users/verified">Verified Users</a></li>
                          <li><a href="<?=base_url()?>index.php/admin/users/unverified">Unverified Users</a></li>
                      </ul>
                  </li>
                  <li>
                      <a href="<?=base_url()?>index.php/admin/usd_text" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>USD Related Text</span>
                      </a>
                  </li>
                  <li>
                      <a href="<?=base_url()?>index.php/admin/about_content" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>About Content</span>
                      </a>
                  </li> 
                  <li>
                      <a href="<?=base_url()?>index.php/admin/dashboard_1" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Dashboard New</span>
                      </a>
                  </li> 
<!--
                   <li>
                      <a href="<?=base_url()?>index.php/admin/filter" id="menu_orders" >
                          <i class="fa fa-book"></i>
                          <span>Filters Master</span>
                      </a>
                  </li>
-->
              </ul>
              <!-- sidebar menu end-->
          </div>
          
          <script>
            $('.sidebar-menu li a').removeClass('active');
            $('#menu_<?=strtolower($this->uri->segment(2, 0))?>').addClass('active');
          </script>