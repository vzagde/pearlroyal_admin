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
</body>

</html>