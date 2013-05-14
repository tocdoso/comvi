<h1><?php echo $title;?></h1>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Name</th>
		<th>Parent</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><?php echo $role->getId();?></td>
		<td><?php echo $role->getName();?></td>
		<td><?php if ($role->getParent()):?><a href="<?php echo $this->build($this->url('?controller=User/Role/Controller&id='.$role->getParent()->getId()));?>"><?php echo $role->getParent();?></a><?php else:?>NULL<?php endif;?></td>
	</tr>
	</tbody>
</table>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Role/Controller&task=Edit&id='.$role->getId()));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Role/Controller&task=Delete&id='.$role->getId()));?>"><i class="icon-trash"></i> <?php echo $this->_('Delete', 'default');?></a>
</div>
