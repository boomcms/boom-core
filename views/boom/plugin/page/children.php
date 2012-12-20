<ul>
	<? foreach ($pages as $p): ?>
		<li>
			<a
			<? 
				if (! $p->is_visible()):
					echo " class='leftnav-page-invisible'";
				elseif ( ! $p->visible_in_nav):
					echo " style='color: #ff0000'";
				endif;
			?>
			 href="<?= $p->link() ?>"><?= $p->version()->title ?></a>
		</li>
	<? endforeach; ?>
</ul>