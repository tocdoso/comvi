<h1><?php echo $title;?></h1>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Title</th>
		<th>Featured</th>
		<th>Status</th>
		<th>Category</th>
		<th><?php echo $this->_('Action', 'default');?></th>
	</tr>
	</thead>
	<tbody>
<?php foreach ($contents as $content):?>
	<tr>
		<td><?php echo $content->getId();?></td>
		<td><?php echo $content->getTitle();?></td>
		<td><?php echo $content->isFeatured();?></td>
		<td><?php echo $content->getStatus();?></td>
		<td><?php echo $content->getCategory();?></td>
		<td><div class="btn-group">
			<a class="btn" href="<?php echo $this->build($this->url('?controller=Content/Controller&task=Edit&id='.$content->getId()));?>"><i class="icon-pencil"></i> <?php echo $this->_('Edit', 'default');?></a>
			<a class="btn" href="<?php echo $this->build($this->url('?controller=Content/Controller&task=Delete&id='.$content->getId()));?>"><i class="icon-trash"></i> <?php echo $this->_('Delete', 'default');?></a>
		</div></td>
	</tr>
<?php endforeach;?>
	</tbody>
</table>

<?php echo $pagination;?>

<div class="btn-group">
	<a class="btn" href="<?php echo $this->build($this->url('?controller=Content/Controller&task=Add'.($category ? '&category='.$category : '')));?>"><i class="icon-plus"></i> <?php echo $this->_('Add', 'default');?></a>
</div>
