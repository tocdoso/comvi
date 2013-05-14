<?php if (!empty($items)):?>

<div class="breadcrumbs">
<?php foreach ($items as $i => $item):?>
<?php if ($item->getURL()):?><a href="<?php echo $this->build($this->url($item->getURL()));?>"><?php endif;?><?php echo $item->getName();?><?php if ($item->getURL()):?></a><?php endif;?><?php if ($i !== count($items) - 1):?> › <?php endif;?>
<?php endforeach;?>

</div>
<?php endif;?>
