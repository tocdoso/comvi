		<h1>Contents List</h1>
<?php foreach ($list as $content) {?>
		<li>
			<h2><?php echo $content['Title']?><h2>
			<p><?php echo htmlspecialchars($content['Content'])?></p>
		</li>
<?php }?>
