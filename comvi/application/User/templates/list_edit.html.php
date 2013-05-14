<h1><?php echo $title;?></h1>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Email</th>
		<th>Roles</th>
		<th><?php echo $this->_('Action', 'default');?></th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($users as $user):?>
	<tr>
		<td><?php echo $user->getId();?></td>
		<td><?php echo $user->getEmail();?></td>
		<td>
<?php foreach ($user->getRoles() as $role):?>
			<p><a href="<?php echo $this->build($this->url('?controller=User/Role/Controller&task=Edit&id='.$role->getId()));?>"><?php echo $role->getName();?></a></p>
<?php endforeach;?>
		</td>
		<td><div class="btn-group">
			<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Edit&id='.$user->getId()));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
			<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Delete&id='.$user->getId()));?>"><i class="icon-trash"></i> <?php echo $this->_('Delete', 'default');?></a>
		</div></td>
	</tr>
<?php endforeach;?>
	</tbody>
</table>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Add'));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
