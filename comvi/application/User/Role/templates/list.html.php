<h1><?php echo $title;?></h1>
<ul>
<?php foreach ($roles as $role):?>
<li><a href="<?php echo $this->build($this->url('?controller=User/Role/Controller&id='.$role->getId()));?>"><?php echo $role->getName();?></li>
<?php endforeach;?>
</ul>

<?php echo $pagination;?>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Role/Controller&task=Add'));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Role/Controller&task=Edit'));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
</div>
