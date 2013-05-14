<h1><?php echo $title;?></h1>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Item</th>
		<th>Role</th>
		<th>Task</th>
		<th>Allowed</th>
		<th><?php echo $this->_('Action', 'default');?></th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($privileges as $privilege):?>
	<tr>
		<td><?php echo $privilege->getId();?></td>
		<td><?php echo $privilege->getItem() ? $privilege->getItem() : 'NULL';?></td>
		<td><?php echo $privilege->getRole() ? $privilege->getRole() : 'NULL';?></td>
		<td><?php echo $privilege->getTask();?></td>
		<td><?php echo $privilege->isAllowed();?></td>
		<td><div class="btn-group">
			<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Edit&id='.$privilege->getId()));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
			<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Delete&id='.$privilege->getId()));?>"><i class="icon-trash"></i> <?php echo $this->_('Delete', 'default');?></a>
		</div></td>
	</tr>
<?php endforeach;?>
	</tbody>
</table>

<?php echo $pagination;?>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Add'.($item ? '&item='.$item : '')));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
