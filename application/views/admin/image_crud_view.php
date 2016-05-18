<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once( 'includes/head.php'); ?>

    <?php foreach($css_files as $file): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />

    <?php endforeach; ?>
    <?php foreach($js_files as $file): ?>

    <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>
</head>

<body>

    <section id="container">
        <section class="wrapper site-min-height">
            <h1><?=ucfirst($this->uri->segment(2, 0))?></h1>
            <hr>
            <div class="row mt">
                <div class="col-lg-12">
                    <?php echo $output; ?>
                </div>
            </div>
        </section>
    </section>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script>
	    $(document).ready(function(){
		    var availableTags2 = [];
		    $("#field-filter_type").change(function(){
			    availableTags2 = [];
			    var cat_filtername = $("#field-filter_type").val();
				$.ajax({
				    url: '<?=base_url()?>index.php/admin/get_autocompletevalues',
				    data: 'catfiltername='+cat_filtername,
				    type: 'post',
				    success: function(response){
					    availableTags2 = response;
					    try{
							console.log(availableTags2);
							$( "#field-filter_name" ).autocomplete({
								source: availableTags2
							});
						}catch(err){
							$.getScript('//code.jquery.com/ui/1.11.4/jquery-ui.js', function() {
								$( "#field-filter_name" ).autocomplete({
									source: availableTags2
								});
							});
						}
				    }
			    });
		    });
		    
		    //filter autocomplete for itemfilter.
		    var availableTags_items = [];
		    $("#field-filter_id").change(function(){
			    availableTags_items = [];
			    var cat_filtername = $("#field-filter_id").val();
				$.ajax({
				    url: '<?=base_url()?>index.php/admin/get_autocompletevalues_items',
				    data: 'filter_id='+cat_filtername,
				    type: 'post',
				    success: function(response){
					    availableTags_items = response;
					    try{
							console.log(availableTags_items);
							$( "#field-filter_value" ).autocomplete({
								source: availableTags_items
							});
						}catch(err){
							$.getScript('//code.jquery.com/ui/1.11.4/jquery-ui.js', function() {
								$( "#field-filter_value" ).autocomplete({
									source: availableTags_items
								});
							});
						}
				    }
			    });
		    });
	    });
    </script>
</body>

</html>