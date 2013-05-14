<h1><?php echo $title;?></h1>
<ul>
<?php foreach ($users as $user):?>
<li><a href="<?php echo $this->build($this->url('?controller=User/Controller&id='.$user->getId()));?>"><?php echo $user->getEmail();?></li>
<?php endforeach;?>
</ul>

<?php echo $pagination;?>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Add'));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
	<a class="btn" href="<?php echo $this->build($this->url('?controller=User/Controller&task=Edit'));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
</div>
