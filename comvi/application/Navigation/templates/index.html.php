<h1><?php echo $title;?></h1>

<?php $print_items = function ($items) use (&$print_items) {
	foreach ($items as $item) {
		echo '<li><p><a'
			.' href="'.$this->build($this->url($item->getURL())).'"'
			.' title="'.$item->getName().'"'
			.'><strong>'.$item->getName()
			.'</strong></a><br/>'
			.'<a href="'.$this->build($this->url('?controller=Navigation/Module/Controller&task=Edit&item='.$item->getId())).'"><small>'.$this->_('Modules').' ('.count($item->getModules()).')</small></a>'
			.' · <a href="'.$this->build($this->url('?controller=Navigation/Privilege/Controller&task=Edit&item='.$item->getId())).'"><small>'.$this->_('Privileges').' ('.count($item->getPrivileges()).')</small></a>'
			.' · <a href="'.$this->build($this->url('?controller=Navigation/Module/Controller&task=Add&item='.$item->getId())).'"><small>'.$this->_('Add module').'</small></a>'
			.' · <a href="'.$this->build($this->url('?controller=Navigation/Privilege/Controller&task=Add&item='.$item->getId())).'"><small>'.$this->_('Add privilege').'</small></a></p>'
			."\n";

		if (!$item->getChildren()->isEmpty()) {
			echo '<ul>'."\n";
			$print_items($item->getChildren());
			echo '</ul>'."\n";
		}
	}

	$print_items->bindTo($this);
}?>
<p>
	<strong><?php echo $this->_('Root');?></strong><br/>
	<a href="<?php echo $this->build($this->url('?controller=Navigation/Module/Controller&task=Edit&item='));?>"><small><?php echo $this->_('Modules');?> (<?php echo count($root_modules);?>)</small></a>
	 · <a href="<?php echo $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Edit&item='));?>"><small><?php echo $this->_('Privileges');?> (<?php echo count($root_privileges);?>)</small></a>
	 · <a href="<?php echo $this->build($this->url('?controller=Navigation/Module/Controller&task=Add'));?>"><small><?php echo $this->_('Add module');?></small></a>
	 · <a href="<?php echo $this->build($this->url('?controller=Navigation/Privilege/Controller&task=Add'));?>"><small><?php echo $this->_('Add privilege');?></small></a>
</p>

<ul>
<?php $print_items($items);?>
</ul>
