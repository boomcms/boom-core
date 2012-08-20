<li>
	<h3 class="title"><?= $name ?></h3>
		<ul class='hidden'>
		<?
			foreach ($tags as $tag):
				$name = htmlentities($tag['name'], ENT_QUOTES);
				echo "<li>";
				echo "<a href='/blog/$section/$name'>$name";
				echo " <span class='number'>(", $tag['count'], " ", Inflector::plural('page', $tag['count']);
				echo ")</span>&nbsp;&raquo;</a></li>";
			endforeach;
		?>
	</ul>
</li>