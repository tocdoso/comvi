<header id="header" class="clearfix">
<a id="logo" href="<?php echo $this->build($this->parse($this->url('')));?>"><img src="<?php echo $this->url('img/logo32.png');?>" alt="<?php echo $sitename;?>" width="101" height="32" /></a>
<?php $this->placeholder('menu')->setPrefix('<ul class="menu hidden-phone">')->setPostfix('</ul>');?>
<?php if (!$this->placeholder('menu')->isEmpty()):?><div class="toggleMenu"></div><?php endif;?>
<?php echo $this->placeholder('userbox');?>
<?php echo $this->placeholder('menu');?>
</header>
