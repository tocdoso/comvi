<!DOCTYPE html>
<html<?php if (isset($language)) echo ' lang="'.$language.'"';?>>
<head>
<?php $this->display('head')?>
</head>
<body>
<!-- Wrap all page content here -->
<div id="wrap">
 
<!-- Begin page header -->
<?php $this->display('header')?>
<?php echo $this->placeholder('breadcrumbs');?>

<!-- Begin page content -->
<section id="container">
<?php echo $this->placeholder('slideshow');?>

<?php echo $this->placeholder('content');?>
</section>

<div id="push"></div>

</div>

<!-- Begin page footer -->
<?php $this->display('footer')?>

<?php $this->document->flushHeadElements();?>
<script type="text/javascript">
$(function() {
	$('.toggleMenu').click(function () {
		$('.menu').toggleClass('hidden-phone');
	});
});
</script>
</body>
</html>