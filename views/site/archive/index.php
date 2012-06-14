<div id="archive" class="corner-10">
	<div class="headerwrapper corner-10">
		<h2><a href="<?=$parent->url();?>"><?= $title ?></a></h2>
	</div>

	<ul>
		<? 
			foreach ($sections as $section):
				echo $section;
			endforeach;
		?>
	</ul>
</div>