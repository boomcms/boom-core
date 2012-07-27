<li>
	<h3 class="title">Date</h3>
	<ul class='hidden'>
		<?	
			foreach ($dates as $year => $months):
				echo "<li class='year'><span class='title'>$year</span>";
				echo "<ul class='hidden'>";
				
				foreach ($months as $month => $data):
					echo "<li>";
					echo "<a href='/blog/date/", $year, "/", $month, "'>", $data['name'];
					echo " <span class='number'>(", $data['count'], " ", Inflector::plural(	'page', $data['count']); 
					echo ")</span>&nbsp;&raquo;</a></li>";
				endforeach;
				echo "</ul></li>";
			endforeach;
		?>
	</ul>
</li>