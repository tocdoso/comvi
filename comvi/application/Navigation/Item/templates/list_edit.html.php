<?php
	$data = <<<JS
$(function () {
    $("[data-toggle='tooltip']").tooltip({container: 'body'});
});
JS;
	//$this->document->addScriptText($data);
?>
<h1><?php echo $title;?></h1>
<?php $print_items = function ($items) use (&$print_items) {
	foreach ($items as $item) {
		echo '<tr>'
			.'<td>'.$item['id'].'</td>'
			.'<td>'.str_repeat('&nbsp;', $item['level']*8).$item['name'].'</td>'
			.'<td><div class="btn-group">'
			.'<a class="btn" href="#"><i class="icon-chevron-up"></i></a>'
			.'<a class="btn" href="#"><i class="icon-chevron-down"></i></a>'
			.'<a class="btn" href="'.$this->build($this->url('?controller=Navigation/Item/Controller&id='.$item['id'].'&task=Edit')).'"><i class="icon-pencil"></i> '.$this->_('Edit', 'default').'</a>'
			.'<a class="btn" href="'.$this->build($this->url('?controller=Navigation/Item/Controller&id='.$item['id'].'&task=Delete')).'"><i class="icon-trash"></i> '.$this->_('Delete', 'default').'</a>'
			.'</div></td>'
			.'</tr>'."\n";

		if (!empty($item['__children'])) {
			$print_items($item['__children']);
		}
	}

	$print_items->bindTo($this);
}?>
<table class="table table-hover">
  <thead>
	<tr>
	  <th>#</th>
	  <th><?php echo $this->_('Name', 'default');?></th>
	  <th><?php echo $this->_('Action', 'default');?></th>
	</tr>
  </thead>
  <tbody>
<?php $print_items($items);?>
  </tbody>
</table>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Item/Controller&task=Add'.($root ? '&parent='.$root->getId() : '')));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
