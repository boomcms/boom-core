<?
/**
* This template displays a list of years and months with a count of pages posted in that date.
* This is used by both the blog and work archives.
*
*********************** Variables **********************
*	$parent			****	Instance of Model_Page **** The parent page which we're counting pages under.
********************************************************
*
* @uses Sledge_ORM::count_by_date()
* @uses Inflector::plural()
* @see http://kohanaframework.org/3.2/guide/api/Inflector#plural
*/
?>
<li>
	<h3 class="title">Date</h3>
	<ul class='hidden'>
		<?	
			foreach (ORM::factory( 'page' )->count_by_date( $parent ) as $year => $months):
				echo "<li class='year'><span class='title'>$year</span>";
				echo "<ul class='hidden'>";
				
				foreach ($months as $month => $data):
					echo "<li>";
					echo "<a href='/blog/date/", $year, "/", $month, "'>", $data['name'];
					echo " <span class='number'>(", $data['posts'], " ", Inflector::plural(	'post', $data['posts'] ); 
					echo ")</span>&nbsp;&raquo;</a></li>";
				endforeach;
				echo "</ul></li>";
			endforeach;
		?>
	</ul>
</li>