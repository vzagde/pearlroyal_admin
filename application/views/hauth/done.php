<?php

// echo "<pre>";
// var_dump((array)$user_data);
// die();

$user_data = (array)$user_profile;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Team DVLM</title>
	<?php include(APPPATH."views/includes/head.php"); ?>
</head>
<body>
	<?php include(APPPATH."views/includes/menu.php"); ?>
<section class="main-part">
    <div class="container pad0">
        <div class="row grid-100 mobile-grid-100 pad0 marg0">
            <div class="site-main grid-100 mobile-grid 100 pad0">
                <div class="col-md-12 margb">
                    <div id="logo">
                        <a href="#"><img src="<?=base_url()?>assets/img/logo.png" class="sz" alt="" /></a>
                    </div>
                </div>

        <div class="row push-30 grid-40 mobile-grid-100 pad0 marg0" style="color: #fff; text-align: left;">
			<?=form_open("home/auth/$provider")?>
				<label for="">Name :</label>
				<input type="text" id="name" name="name" value="<?=$user_data['firstName'].' '.$user_data['lastName']?>" class="form-control" required> <br>

				Email ID : <br>
				<input type="email" name="email" id="email" value="<?php echo (isset($user_data['email'])) ? $user_data['email'] : ''; ?>" class="form-control" required> <br>

				Phone : <br>
				<input type="tel" name="phone" id="phone" class="form-control" required> <br>

				<input type="hidden" name="profile_picture" value="<?= $user_data['photoURL']?>">
				<input type="hidden" name="user_id" value="<?=$user_data['identifier']?>">
				<input type="hidden" name="provider" value="<?=$provider?>">
				

				<input type="checkbox"  name="terms-and-conditions" required data-validation-required-message=
					    "You must agree to the terms and conditions" id=""> I agree to all the terms and condotions. <br><br>
				<input type="checkbox"  name="terms-and-conditions" required data-validation-required-message=
					    "You must agree to the terms and conditions" id=""> I allow team DVLM to post on my behalf. <br><br>

				<button class="btn btn-primary">Submit</button>
			<br>
		</form>
        </div>
                <!-- logo end -->
                <div class="clear"></div>
			</div>
		</div>
    </div>
</section>
<?php include(APPPATH.'views/includes/footer.php'); ?>
</body>
</html>