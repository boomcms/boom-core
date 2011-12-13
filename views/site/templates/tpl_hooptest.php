<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div id="wrapper" style="text-align:left;align:left;padding-left:5em;">
<?
		# setup
			$id_tag = O::fa('tag')->find_by_name('Tags')->rid;
			$id_tag_stuff = O::fa('tag')->find_by_name_and_parent_rid('Stuff',$id_tag)->rid;
			$id_tag_system = O::fa('tag')->find_by_name_and_parent_rid('System',$id_tag)->rid;
			$id_tag_metadata = O::fa('tag')->find_by_name_and_parent_rid('Metadata',$id_tag)->rid;
				
			$id_tag_stuff_shippinginfo = O::fa('tag')->find_by_name_and_parent_rid('Shipping information',$id_tag_stuff);
			$id_tag_metadata_shippinginfo = O::fa('tag')->find_by_name_and_parent_rid('Shipping information',$id_tag_metadata);

		# use case one: give me the groups
			?><h1>Use case one: Give me the groups</h1><?
			foreach (Stuff::getstuff($id_tag_stuff_shippinginfo) as $thing_shipping_info) {
				?><p><?
				foreach (Metadata::getmetadata('stuff', $thing_shipping_info,'',$id_tag_metadata_shippinginfo) as $meta) {
					?><?=$meta->key?> is <?=$meta->value?><br /><?
				}
				?></p><?
			}
		# use case two: give me one key/value pair for everything
			?><h1>Use case two: Give me one key/value pair for everything</h1><?
			foreach (Stuff::getstuff($id_tag_stuff_shippinginfo) as $thing_shipping_info) {
				?><?=Metadata::getmetadata('stuff', $thing_shipping_info, 'Country', $id_tag_metadata_shippinginfo)->value;?><br /><?
			}

		# use case three: where key=this and value=that
			?><h1>Use case three: Where key=this and value=that (EG: Where country is albania, give me the other metadata), give me the stuff entry back so i can get all key/value pairs</h1><?


		## use case three, approach one
			# retrieve the relevant stuff record
			$thing_shipping_info = Stuff::getstuff($id_tag_stuff_shippinginfo,'Country','Albania');

			# pull back a subset of metadata for that stuff record
			$metadatum_shipping_info = Metadata::getmetadata('stuff',$thing_shipping_info, 'Small', $id_tag_metadata_shippinginfo);	

			# display the part of that metadata you're interested in
			?><p>Small cost for Albania is &pound;<?=$metadatum_shipping_info->value?></p><?

		## use case three, approach two
			?><p>OR</p><?

			$metadatum_shipping_info = Metadata::getmetadata('stuff',Stuff::getstuff($id_tag_stuff_shippinginfo,'Country','Albania'), 'Small', $id_tag_metadata_shippinginfo);
			?><p>Small cost for Albania is &pound;<?=$metadatum_shipping_info->value?></p><?
	
			?><p>OR</p><?

		## use case three, approach three
			?><p>Small cost for Albania is: &pound;<?=Metadata::getmetadata('stuff',Stuff::getstuff($id_tag_stuff_shippinginfo,'Country','Albania'), 'Small', $id_tag_metadata_shippinginfo)->value;?></p>
			
</div>
