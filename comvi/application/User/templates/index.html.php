<h1><?php echo $title;?></h1>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Email</th>
		<th>Roles</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><?php echo $user->getId();?></td>
		<td><?php echo $user->getEmail();?></td>
		<td>
<?php foreach ($user->getRoles() as $role):?>
			<p><a href="<?php echo $this->build($this->url('?controller=User/Role/Controller&id='.$role->getId()));?>"><?php echo $role->getName();?></a></p>
<?php endforeach;?>
		</td>
	</tr>
	</tbody>
</table>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Edit&id='.$user->getId()));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Delete&id='.$user->getId()));?>"><i class="icon-trash"></i> <?php echo $this->_('Delete', 'default');?></a>
</div>
