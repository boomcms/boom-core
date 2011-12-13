<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
	
	$uri = explode('/',$this->page->uri);
	
	$base_uri = "/languages";
	
	if(isset($uri[1])) {
	  switch ($uri[1]) {
        case "permanent-rented-housing":
        case "leasehold":
        case "temporary-housing":
		case "supported-residents":
            $base_uri = "/customers/".$uri[1];
            break;
        case "settled-accommodation":
            $base_uri = "/customers/temporary-housing";
            break;
    }  
	}

?>
<div id="language-widget" class="box">
	<div class="box-ne">
		<div class="box-se">
			<div class="box-nw">
				<div class="box-sw">
					<div class="wrapper">
						<ul>
						  <? if ($base_uri == '/languages') {?>
							<li lang="ar" dir="rtl"><a href="/languages">معلومات بلغتك</a></li>
							<li lang="so"><a href="/languages">Macluumaadka ku qoran luqaddaada hooyo.</a></li>
						    <li lang="fa" dir="rtl"><a href="/languages">ارائه اطلاعات به زبان ملی شما</a></li>
  							<li lang="es"><a href="/languages">Información en su propio idioma.</a></li>
  							<li lang="pt"><a href="/languages">Informações no seu próprio idioma.</a></li>
  							<li lang="pa"><a href="/languages">ਤੁਹਾਡੀ ਆਪਣੀ ਭਾਸ਼ਾ ਵਿੱਚ ਜਾਣਕਾਰੀ</a></li>
  						</ul>
						  <? } else {?>
							<li lang="ar" dir="rtl"><a href="<?=$base_uri?>/arabic">معلومات بلغتك</a></li>
							<li lang="so"><a href="<?=$base_uri?>/somali">Macluumaadka ku qoran luqaddaada hooyo.</a></li>
						    <li lang="fa" dir="rtl"><a href="<?=$base_uri?>/farsi">ارائه اطلاعات به زبان ملی شما</a></li>
  							<li lang="es"><a href="<?=$base_uri?>/spanish">Información en su propio idioma.</a></li>
  							<li lang="pt"><a href="<?=$base_uri?>/portuguese">Informações no seu próprio idioma.</a></li>
  							<li lang="pa"><a href="<?=$base_uri?>/punjabi">ਤੁਹਾਡੀ ਆਪਣੀ ਭਾਸ਼ਾ ਵਿੱਚ ਜਾਣਕਾਰੀ</a></li>
						  <? } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	//<![CDATA[
		$('#language-widget .box-sw').anythingSlider({
		});
	//]]>
</script>