<ul class="pull-left">
<?php foreach ($items as $item) :?>
<li><?php if (!isset($item['active'])) :?><a href="<?php echo $item['href'];?>"><?php endif;?><?php echo $item['name'];?><?php if (!isset($item['active'])) :?></a><?php endif;?></li>
<?php endforeach;?>
</ul>
