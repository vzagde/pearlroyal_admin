<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <title>Pearlroyale</title>
    <link href="<?=base_url()?>assets/admin/css/bootstrap.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?=base_url()?>assets/admin/css/style.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/css/style-responsive.css" rel="stylesheet">
    <style>
      .Pending{
        background: red !important;
        color: #f1f1f1 !important;
      }

      .Paid{
        background: green !important;
        color: #f1f1f1 !important;
      }
    </style>
  </head>
  <body>
  <section id="container" >
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <a href="javascript:void(0);" class="logo"><b>Menu</b></a>
            <div class="nav notify-row" id="top_menu">
                <ul class="nav top-menu">
                   
                </ul>
            </div>
            <div class="top-menu">
              <ul class="nav pull-right top-menu">
                    <li><a class="logout" href="<?=base_url()?>auth/logout">Logout</a></li>
              </ul>
            </div>
        </header>
        <aside>
        <?php include_once('includes/sidebar.php'); ?>
        </aside>
        <section id="main-content">
          <section class="wrapper">
          	<h3><i class="fa fa-angle-right"></i><?=@$title?></h3>
              <div class="row mt">
                  <div class="col-md-12">
                      <div class="content-panel">
                        <?php
                          if ($page_title == 'Categories') {
                        ?>
                          <table class="table table-striped table-advance table-hover">
	                  	  	  <h4><i class="fa fa-angle-right"></i><?=@$page_title?></h4>
	                  	  	  <hr>
                              <thead>
                              <tr>
                                  <th><i class="fa fa-bullhorn"></i> Categories</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Image</th>
                                  <th><i class=" fa fa-edit"></i> View Auctions</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                foreach ($category as $cat) {
                              ?>
                              <tr>
                                  <td><a href="#"><?=$cat->category_name?></a></td>
                                  <td class="hidden-phone"><img src="<?=base_url()?>assets/uploads/<?=$cat->image?>" style="height: 50px;" class="img-responsive" alt=""></td>
                                  <td><span class="label label-info label-mini"><a href="<?=base_url()?>index.php/admin/auctions_dashboard/<?=$cat->id?>">View Auctions</a></span></td>
                              </tr>
                              <?php
                                }
                              ?>
                            </tbody>
                          </table>
                        <?php
                          } elseif ($page_title == 'Auctions') {
                        ?>
                          <table class="table table-striped table-advance table-hover">
                            <h4><i class="fa fa-angle-right"></i><?=@$page_title?></h4>
                            <hr>
                              <thead>
                              <tr>
                                  <th><i class="fa fa-bullhorn"></i> Auctions</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Address</th>
                                  <th><i class="fa fa-bullhorn"></i> City</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Country</th>
                                  <th><i class="fa fa-bullhorn"></i> Start Date</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> End Date</th>
                                  <th><i class="fa fa-bullhorn"></i> Archive Date</th>
                                  <th><i class=" fa fa-edit"></i> View By Items</th>
                                  <th><i class=" fa fa-edit"></i> View By Users</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                foreach ($auctions as $cat) {
                              ?>
                              <tr>
                                  <td><a href="#"><?=$cat->auction_name?></a></td>
                                  <td class="hidden-phone"><?=$cat->address?></td>
                                  <td class="hidden-phone"><?=$cat->city?></td>
                                  <td class="hidden-phone"><?=$cat->country?></td>
                                  <td class="hidden-phone"><?=$cat->auction_start_date?></td>
                                  <td class="hidden-phone"><?=$cat->auction_end_date?></td>
                                  <td class="hidden-phone"><?=$cat->auction_archive_date?></td>
                                  <td><span class="label label-info label-mini"><a href="<?=base_url()?>index.php/admin/item_dashboard/<?=$cat->id?>/<?=$cat->cat_id?>">View Auctions</a></span></td>
                                  <td><span class="label label-info label-mini"><a href="<?=base_url()?>index.php/admin/user_dashboard/<?=$cat->id?>/<?=$cat->cat_id?>">View Auctions</a></span></td>
                              </tr>
                              <?php
                                }
                              ?>
                            </tbody>
                          </table>
                        <?php
                          } elseif ($page_title == 'Items') {
                        ?>
                          <table class="table table-striped table-advance table-hover">
                            <h4><i class="fa fa-angle-right"></i><?=@$page_title?></h4>
                            <hr>
                              <thead>
                              <tr>
                                  <th><i class="fa fa-bullhorn"></i> Items Name</th>
                                  <th><i class="fa fa-bullhorn"></i> Image</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i>Category</th>
                                  <th><i class="fa fa-bullhorn"></i> Auction</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Floor Amount (USD)</th>
                                  <th><i class="fa fa-bullhorn"></i> Bid Amount (USD)</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Item Lot No.</th>
                                  <th><i class=" fa fa-edit"></i> View Auctions</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                foreach ($items as $cat) {
                              ?>
                              <tr>
                                  <td><a href="#"><?=$cat->item_name?></a></td>
                                  <td class="hidden-phone"><img src="<?=base_url()?>assets/uploads/<?=$cat->image?>" style="height: 50px;" class="img-responsive" alt=""></td>
                                  <td class="hidden-phone"><?=$cat->category?></td>
                                  <td class="hidden-phone"><?=$cat->auction?></td>
                                  <td class="hidden-phone"><?=$cat->floor_amount?></td>
                                  <td class="hidden-phone"><?=$cat->bid_amount?></td>
                                  <td class="hidden-phone"><?=$cat->item_key?></td>
                                  <td><span class="label label-info label-mini"><a href="<?=base_url()?>index.php/admin/item_details/<?=$cat->id?>">View Auctions</a></span></td>
                              </tr>
                              <?php
                                }
                              ?>
                            </tbody>
                        <?php
                          } elseif ($page_title == 'UIItems1' || $page_title == 'UIItems2') {

                        ?>
                          <table class="table table-striped table-advance table-hover">
                            <h4><i class="fa fa-angle-right"></i> <?=@$page_title?></h4>
                              <?php
                                foreach ($uirel as $cat) {
                              ?>
                              <div class="col-md-6">
                                <h4><i class="fa fa-angle-right"></i> Item Name : <?=@$cat->item_name?></h4>
                                <h4><i class="fa fa-angle-right"></i> Highest Bid User Email : <?=@$cat->email?></h4>
                                <h4><i class="fa fa-angle-right"></i> Highest Bid : $<?=@$cat->bidded_amount?></h4>
                                <h4><i class="fa fa-angle-right"></i> Paid Amount : $<?=@$paid_amount[0]->paid_amount?></h4>
                              </div>
                              <?php
                                if ($page_title == 'UIItems1') {
                              ?>
                                <div class="col-md-4 form-group">
                                  <div class="form-group">
                                    <input type="hidden" class="form-control" id="item_id" name="item_id" value="<?=$cat->item_id?>">
                                  </div>
                                  <div class="form-group">
                                    <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?=$cat->user_id?>">
                                  </div>
                                  <div class="form-group">
                                    <input type="text" class="form-control" id="amount" name="amount">
                                  </div>
                                  <button class="btn btn-primary" id="makepayment">Do Payments</button>
                                  <p id="dangertxt" style="color: red; display: none"><b>There is some problem while saving data Or please check amount you entered.</b></p>
                                  <p><b id="successtxt" style="color: green; display: none"></b></p>
                                </div>
                              <?php
                                }
                                break;
                                }
                                if ($page_title == 'UIItems1') {
                              ?>
                              <thead>
                              <tr>
                                  <th><i class="fa fa-bullhorn"></i> Items Name</th>
                                  <th><i class="fa fa-bullhorn"></i> User Email ID</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i>Bidded Amount</th>
                                  <th><i class="fa fa-bullhorn"></i> Previous Biddings</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Floor Amount</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                foreach ($uirel as $cat) {
                                  if ($cat->previous_biding != 0 || $cat->bidded_amount != 0) {
                              ?>
                              <tr>
                                  <td class="hidden-phone"><?=$cat->item_name?></td>
                                  <td class="hidden-phone"><?=$cat->email?></td>
                                  <td class="hidden-phone">$<?=$cat->bidded_amount?></td>
                                  <td class="hidden-phone">$<?=$cat->previous_biding?></td>
                                  <td class="hidden-phone">$<?=$cat->floor_amount?></td>
                              </tr>
                              <?php
                                    }
                                  }
                                } else {
                              ?>
                              <thead>
                              <tr>
                                  <th><i class="fa fa-bullhorn"></i> Items Name</th>
                                  <th><i class="fa fa-bullhorn"></i> User Email ID</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i>Bidded Amount</th>
                                  <th><i class="fa fa-bullhorn"></i> Previous Biddings</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Floor Amount</th>
                                  <th><i class="fa fa-bullhorn"></i> Paid Amount</th>
                                  <th class="hidden-phone"><i class="fa fa-question-circle"></i> Payment Status</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                foreach ($uirel as $cat) {
                                  if ($cat->previous_biding != 0 || $cat->bidded_amount != 0) {
                              ?>
                              <tr>
                                  <td class="hidden-phone"><?=$cat->item_name?></td>
                                  <td class="hidden-phone"><?=$cat->email?></td>
                                  <td class="hidden-phone">$<?=$cat->bidded_amount?></td>
                                  <td class="hidden-phone">$<?=$cat->previous_biding?></td>
                                  <td class="hidden-phone">$<?=$cat->floor_amount?></td>
                                  <td class="hidden-phone">$<?=$cat->paid_amount?></td>
                                  <td class="hidden-phone"><span class="label label-info label-mini <?=$cat->payment_status?>"><?=$cat->payment_status?></span></td>
                              </tr>
                              <?php
                                    }
                                  }
                                }
                              ?>
                            </tbody>
                        <?php
                          } elseif ($page_title == 'UIUsers') {

                        ?>
                          <table class="table table-striped table-advance table-hover">
                            <h4><i class="fa fa-angle-right"></i> <?=@$page_title?></h4>
                            <hr>
                              <thead>
                              <tr>
                                  <th><i class="fa fa-bullhorn"></i> User Email ID</th>
                                  <th><i class="fa fa-bullhorn"></i> Amount In Account</th>
                                  <th><i class=" fa fa-edit"></i> View Auctions</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php
                                foreach ($uirelu as $cat) {
                              ?>
                              <tr>
                                  <td class="hidden-phone"><?=$cat->email?></td>
                                  <td class="hidden-phone"><?=$cat->amount?></td>
                                  <td><span class="label label-info label-mini"><a href="<?=base_url()?>index.php/admin/user_details/<?=$cat->id?>">View Auctions</a></span></td>
                              </tr>
                              <?php
                                }
                              ?>
                            </tbody>
                            <?php
                              }
                            ?>
                          </table>
                      </div>
                  </div>
        		</section>
          </section>
        </section>
      <script src="<?=base_url()?>assets/admin/js/jquery.js"></script>
      <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
      <script src="<?=base_url()?>assets/admin/js/bootstrap.min.js"></script>
      <script class="include" type="text/javascript" src="<?=base_url()?>assets/admin/js/jquery.dcjqaccordion.2.7.js"></script>
      <script src="<?=base_url()?>assets/admin/js/jquery.scrollTo.min.js"></script>
      <script src="<?=base_url()?>assets/admin/js/jquery.nicescroll.js" type="text/javascript"></script>
      <script src="<?=base_url()?>assets/admin/js/common-scripts.js"></script>
    <script>
      $(function(){
          $('select.styled').customSelect();
      });

      $("#makepayment").click(function(){
          $.ajax({
            url:'http://pearlroyale.com/pearlroyale_admin/index.php/admin/update_amount/',
            type: 'post',
            data: { user_id : $("#user_id").val(), item_id : $("#item_id").val(), amount : $("#amount").val() },
            success: function(data){
              $("#successtxt").hide(1000);
              $("#dangertxt").hide(1000);
              // alert(data);
              if(data == 'Unsuccess'){
                $("#dangertxt").show(1000);
              } else {
                $("#successtxt").append('Transaction Successful Amount Paid $'+$("#amount").val()+' Pending Amount $'+ data);
                $("#successtxt").show(1000);
              }
            }
          });
      })
  </script>
  </body>
</html>
