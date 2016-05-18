<?php
	                //$old_arr = array(
					//      "a" => array ( "id" => 20, "name" => "chimpanzee" ),
					//      "b" => array ( "id" => 40, "name" => "meeting" ),
					//      "c" => array ( "id" => 20, "name" => "dynasty" ),
					//      "d" => array ( "id" => 50, "name" => "chocolate" ),
					//      "e" => array ( "id" => 10, "name" => "bananas" ),
					//      "f" => array ( "id" => 50, "name" => "fantasy" ),
					//      "g" => array ( "id" => 50, "name" => "football" )
					//  );
					//  //print_r($old_arr);
					//  //echo "<br/>";
					//  //echo "<br/>";
					//	$arr = array();
					//	foreach($old_arr as $key => $item)
					//	{
					//	   $arr[$item['id']][$key] = $item;
					//	}
					//	ksort($arr, SORT_NUMERIC);
					//	//print_r($arr);
					//	//exit;
					//
					//	$old_arr1 = $auction_data;
					//	//print_r($old_arr1);
					//	$arr1 = array();
					//	foreach($old_arr1 as $key1 => $item1){
					//		$arr1[$item1['id']][$key1] = $item1;
					//	}
					//	ksort($arr1, SORT_NUMERIC);
					//	print_r($arr1);
					//	exit;
	            ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once( 'includes/head.php'); ?>

</head>
<body>
    <section id="container">
        <header class="header black-bg">
            <div class="sidebar-toggle-box">
                <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
            </div>
            <a href="javascript:void(0);" class="logo"><b>menu</b></a>
            <div class="nav notify-row" id="top_menu">
                <ul class="nav top-menu">
                </ul>
            </div>
            <div class="top-menu">
                <ul class="nav pull-right top-menu">
                    <li><a class="logout" href="<?=base_url()?>auth/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </header>
        <aside>
            <?php include_once( 'includes/sidebar.php'); ?>
        </aside>
        <section id="main-content">
            <section class="wrapper site-min-height">
                <h1 class="page-header">Dashboard</h1>
                <hr>
				<div class="container" style="margin-top: 50px;">
        <ul id="myTab" class="nav nav-tabs" style="margin-bottom: 15px;">
            <?php
	        $prev_val = "";
	        $counter = 1;
            foreach($dashboard as $auction_data_row){
	            if($prev_val != $auction_data_row->auction){
                    if($counter == 1){ ?>
                        <li class="active"><a href="#tab<?=$counter?>" data-toggle="tab"><?=$auction_data_row->auction_name?></a></li>
                    <?php    } else {
                ?>
					<li class=""><a href="#tab<?=$counter?>" data-toggle="tab"><?=$auction_data_row->auction_name?></a></li>
					<?php
                    }
	            }
		        $prev_val = $auction_data_row->auction;
		        $counter++;
            }
            ?>

        </ul>
        <div id="myTabContent" class="tab-content">
	        <?php
		        $counter1 = 1;
		        $prev_val1 = "";
		        foreach($dashboard as $auction_data_row1){
			        if($prev_val1 != $auction_data_row1->auction){
				        ?>
				        <div class="tab-pane fade in active" id="tab<?=$counter1?>">
                <div class="panel-group" id="accordion">
                     <?php
                        $var_product = "";
	                    $auction_counter = 0;
	                    foreach($dashboard as $auction_data_row2){
		                    $auction_counter++;
    		                    if($auction_data_row1->auction == $auction_data_row2->auction and $var_product != $auction_data_row2->item_name){
                                    $var_product = $auction_data_row2->item_name;
			                   ?> 
			                   <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$auction_counter?>"><?=$auction_data_row2->item_name?> <?=$auction_data_row2->bid_amount?>
								</a>
								<i class="indicator fa fa-angle-down  pull-right"></i>
							</h4>
                        </div>
                        <div id="collapse<?=$auction_counter?>" class="panel-collapse collapse in">
                            <div class="panel-body">
                                 <div class="table-responsive">
                                      <table class="table">
                                        <thead>
                                          <tr>
                                            <th>Product ID</th>
                                            <th>user ID</th>
                                            <th>Bid Amount</th>
                                          </tr>
                                        </thead>
                                        <?php
                                            $auction_counter = 0;
                                            foreach($dashboard as $auction_data_row2){
                                                $auction_counter++;
                                                if($auction_data_row1->auction == $auction_data_row2->auction){
                                                   ?>
                                        <tbody>
                                          <tr>
                                            <td><?=$auction_data_row2->item_name?></td>
                                            <td><?=$auction_data_row2->user_id?></td>
                                            <td><?=$auction_data_row2->bid_amount?></td>
                                          </tr>
                                        </tbody>
                                                   <?php 
                                                }
                                            }
                                        ?>
                                      </table>
                               </div>
                            </div>
                        </div>
                    </div>
 			                   <?php 
		                    }
	                    }
	                ?>
                </div>
            </div>
				        <?php
			        }
				    $counter1++;
		        }
		    ?>
            
        </div>
    </div>
            </section>
        </section>
        <footer class="site-footer">
            <div class="text-center">
                Pearl Royal - 2015
                <a href="blank.html#" class="go-top">
                    <i class="fa fa-angle-up"></i>
                </a>
            </div>
        </footer>
    </section>
    <?php include_once( 'includes/site_bottom_scripts.php'); ?>
        <!-- Bootstrap TabCollapse-->
    <script type="text/javascript" src="http://pearlroyale.com/pearlroyale_admin/assets/js/bootstrap-tabcollapse.js"></script>
    <script type="text/javascript">
        function toggleChevron(e) {
            $(e.target)
                .prev('.panel-heading')
                .find("i.indicator")
                .toggleClass('fa fa-angle-down fa fa-angle-up');
        }
        $('#accordion').on('hidden.bs.collapse', toggleChevron);
        $('#accordion').on('shown.bs.collapse', toggleChevron);
    </script>
</body>

</html>