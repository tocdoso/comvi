<?php $print_items = function ($items) use (&$print_items) {
	foreach ($items as $item) {
		echo '<li'
			.(!$item->getChildren()->isEmpty() ? ' class="drop"' : '')
			.'><a'
			.($item->isActive() ? ' class="active"' : '')
			.' href="'.($item->getURL() ? $this->build($this->url($item->getURL())) : 'javascript:;').'"'
			.' title="'.$item->getName().'"'
			.'>'.$item->getName()
			.(!$item->getChildren()->isEmpty() ? '<!--[if gte IE 7]><!-->' : '')
			.'</a>'
			.(!$item->getChildren()->isEmpty() ? '<!--<![endif]-->' : '')
			."\n";

		if (!$item->getChildren()->isEmpty()) {
			echo '<!--[if lte IE 6]><table><tr><td><![endif]-->'."\n"
				.'<ul>'."\n";
			$print_items($item->getChildren());
			echo '</ul>'."\n"
				.'<!--[if lte IE 6]></td></tr></table></a><![endif]-->'."\n";
		}
	}

	$print_items->bindTo($this);
}?>
<?php $print_items($items);?>
