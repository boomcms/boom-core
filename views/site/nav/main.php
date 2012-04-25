<ul class="ui-sortable">
	<? foreach ($pages as $i => $p):
		
		$classes = '';
		if ($i == 0):
			$classes .= 'first';
		endif;
		
		if ($i == $count - 1):
			$classes .= ' last';
		endif;
		
		$class = trim( $classes );
		
		?>
		<li id="p<?= $p['id'] ?>"
			<? 
				if ($classes != ''):
					echo " class='$classes'";
				endif; 
			?>
			><a href="<?=URL::site($p['uri']);?>"><?=ucfirst($p['title']);?></a>
		</li>
	<? endforeach; ?>
</ul>