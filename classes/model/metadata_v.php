<?php

/**
* @package Model
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class metadata_Model extends ORM {

	# This is overridden so we can generate tsvectors for the stuff object and all associated metadata
	public function save() {
		parent::save();
		if ($this->item_tablename=='stuff') {
			$stuff_v = O::fa('stuff',$this->item_rid);
			if ($stuff_v->rid) {
				$stuff_v->generate_tsvector();
			}
		}
	}
}

