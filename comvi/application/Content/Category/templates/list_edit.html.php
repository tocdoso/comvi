<h1><?php echo $title;?></h1>
<?php $print_categories = function ($categories) use (&$print_categories) {
	foreach ($categories as $category) {
		echo '<tr>'
			.'<td>'.$category['id'].'</td>'
			.'<td>'.str_repeat('&nbsp;', $category['level']*8).$category['title'].'</td>'
			.'<td><div class="btn-group">'
			.'<a class="btn" href="#"><i class="icon-chevron-up"></i></a>'
			.'<a class="btn" href="#"><i class="icon-chevron-down"></i></a>'
			.'<a class="btn" href="'.$this->build($this->url('?controller=Content/Category/Controller&id='.$category['id'].'&task=Edit')).'"><i class="icon-pencil"></i> '.$this->_('Edit', 'default').'</a>'
			.'<a class="btn" href="'.$this->build($this->url('?controller=Content/Category/Controller&id='.$category['id'].'&task=Delete')).'"><i class="icon-trash"></i> '.$this->_('Delete', 'default').'</a>'
			.'</div></td>'
			.'</tr>'."\n";

		if (!empty($category['__children'])) {
			$print_categories($category['__children']);
		}
	}

	$print_categories->bindTo($this);
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
<?php $print_categories($categories);?>
  </tbody>
</table>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=Content/Category/Controller&task=Add'.($root ? '&parent='.$root->getId() : '')));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
