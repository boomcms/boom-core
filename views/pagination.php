<?php
/**
* Pagination template for search results.
* It's a horrible template, don't blame Rob.
*
* Rendered by views/site/templates/tp_search.php
* Configured in Controller_Form::action_search()
*
*********************** Variables **********************
*	$base_url		****	string		****	Err. http://www.example.com/search I assume?
*	$previous_page	****	integer		****	The number of the previous page, if we're on page > 1.
*	$total_pages	****	integer		****	The total number of pages available...
*	$current_page	****	integer		****	Number of the current page.
*	$next_page		****	integer		****	I reckon you can get this one by yourself.
********************************************************
*
*/
?>
<?php
/**
 * Digg pagination style
 * 
 * @preview  « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next »
 */
?>
<?if (strstr($base_url,'?')) { $base_url .= '&amp;'; }
	else { $base_url .= '?'; } ?>

	<?php if ($previous_page): ?>
		<a href="<?=$base_url?>page=<?=$previous_page?>">&lt;</a>
	<?php endif ?>


	<?php if ($total_pages < 13): /* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */ ?>

		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong class="current"><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?=$base_url?>page=<?=$i?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

	<?php elseif ($current_page < 9): /* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */ ?>

		<?php for ($i = 1; $i <= 10; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong class="current"><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?=$base_url?>page=<?=$i?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

		&hellip;
		<a href="<?=$base_url?>page=<?=($total_pages-1)?>"><?php echo $total_pages - 1 ?></a>
		<a href="<?=$base_url?>page=<?=$total_pages?>"><?php echo $total_pages ?></a>

	<?php elseif ($current_page > $total_pages - 8): /* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */ ?>

		<a href="<?=$base_url?>page=1?>">1</a>
		<a href="<?=$base_url?>page=2?>">2</a>
		&hellip;

		<?php for ($i = $total_pages - 9; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong class="current"><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?=$base_url?>page=<?=$i?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

	<?php else: /* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */ ?>

		<a href="<?=$base_url?>page=1">1</a>
		<a href="<?=$base_url?>page=2">2</a>
		&hellip;

		<?php for ($i = $current_page - 5; $i <= $current_page + 5; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong class="current"><?php echo $i ?></strong>
			<?php else: ?>
				<a href="<?=$base_url?>page=<?=$i?>"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor ?>

		&hellip;
		<a href="<?=$base_url?>page=<?=($total_pages-1)?>"><?php echo $total_pages - 1 ?></a>
		<a href="<?=$base_url?>page=<?=$total_pages?>"><?php echo $total_pages ?></a>

	<?php endif ?>


	<?php if ($next_page): ?>
		<a href="<?=$base_url?>page=<?=$next_page?>">&gt;</a>
	<?php endif ?>
