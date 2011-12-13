<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
	
	if (isset($_GET['page']) && $_GET['page'] != 0) {
	  $this->pagen = $_GET['page'];
	}
?>
<ul>
	<?if ($this->pagen >1) {?>
		<li class="previous"><a href="<?=$this->pagination_uri?>/<?=$this->pagen-1?>">Previous</a></li>
	<?}?>

	<?for ($i=1; $i<=$this->pages; $i++) {?>
		<li<?if ($i == $this->pagen){?> class="current"<?}?>>
			<a href="<?=$this->pagination_uri?>/<?=$i?>"><?=$i?></a>
		</li>
	<?}?>

	<?if ($this->pagen <$this->pages) {?>
		<li class="previous"><a href="<?=$this->pagination_uri?>/<?=$this->pagen+1?>">Next</a></li>
	<?}?>
</ul>
