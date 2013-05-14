<h1><?php echo $title;?></h1>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Item</th>
		<th>Module</th>
		<th>Assign</th>
		<th>Enable</th>
		<th>Position</th>
		<th><?php echo $this->_('Action', 'default');?></th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($modules as $module):?>
	<tr>
		<td><?php echo $module->getId();?></td>
		<td><?php echo $module->getItem() ? $module->getItem() : 'NULL';?></td>
		<td><?php echo $module->getModule();?></td>
		<td><?php echo $module->getAssign();?></td>
		<td><?php echo $module->isEnable();?></td>
		<td><?php echo $module->getPosition();?></td>
		<td><div class="btn-group">
			<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Module/Controller&task=Edit&id='.$module->getId()));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
			<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Module/Controller&task=Delete&id='.$module->getId()));?>"><i class="icon-trash"></i> <?php echo $this->_('Delete', 'default');?></a>
		</div></td>
	</tr>
<?php endforeach;?>
	</tbody>
</table>

<?php echo $pagination;?>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=Navigation/Module/Controller&task=Add'.($item ? '&item='.$item : '')));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
