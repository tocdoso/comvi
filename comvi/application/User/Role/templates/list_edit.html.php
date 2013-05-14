<h1><?php echo $title;?></h1>
<?php $print_roles = function ($roles) use (&$print_roles) {
	foreach ($roles as $role) {
		echo '<tr>'
			.'<td>'.$role['id'].'</td>'
			.'<td>'.str_repeat('&nbsp;', $role['level']*8).$role['name'].'</td>'
			.'<td><div class="btn-group">'
			.'<a class="btn" href="#"><i class="icon-chevron-up"></i></a>'
			.'<a class="btn" href="#"><i class="icon-chevron-down"></i></a>'
			.'<a class="btn" href="'.$this->build($this->url('?controller=User/Role/Controller&id='.$role['id'].'&task=Edit')).'"><i class="icon-pencil"></i> '.$this->_('Edit', 'default').'</a>'
			.'<a class="btn" href="'.$this->build($this->url('?controller=User/Role/Controller&id='.$role['id'].'&task=Delete')).'"><i class="icon-trash"></i> '.$this->_('Delete', 'default').'</a>'
			.'</div></td>'
			.'</tr>'."\n";

		if (!empty($role['__children'])) {
			$print_roles($role['__children']);
		}
	}

	$print_roles->bindTo($this);
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
<?php $print_roles($roles);?>
  </tbody>
</table>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Role/Controller&task=Add'.($root ? '&parent='.$root->getId() : '')));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
