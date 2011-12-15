<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Person tagging.
* @todo Make the stuff copy and pasted from the Person library look nice.
* @todo Finish saving - including handling versioning. i.e. when we save create a new version. This is going to be a common problem across our versioned models.
* 
*/
class Model_Person extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person';
	protected $_has_one = array( 
		'version'	=> array( 'model' => 'version_person', 'foreign_key' => 'id' )
	);
	protected $_has_many = array( 
		'versions'			=> array( 'model' => 'version_person', 'foreign_key' => 'id' )
		//'sent_messages'		=> array( 'model' => 'message' ),
		//'received_messages'	=> array( 'model' => 'message' )
	);
	protected $_belongs_to = array( 'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'audit_person' ) );
	protected $_load_with = array( 'version' );
	
	/**
	* Set the user's password.
	*
	* @param string $text_password Plain text password which will be encrypted and set as the user's password.
	* @return void
	*/
	public function setPassword( $text_password ) {
		$this->current_version->password = '{SHA}' . base64_encode(sha1($_POST['password'],true));
		
	}
	
	/**
	* Set the user's email address. Ensures that email addresses are lowercase.
	*
	* @param string $emailaddress
	* @return void
	*/
	public function setEmailAddress( $emailaddress ) {
		$this->emailaddress = strtolower( $this->emailaddress );
		
		// Copy and pasted from old Person library. Needs prettying.
		if (!preg_match('/@hoopassociates\.co\.uk$/',$ki->person->emailaddress) && preg_match('/@hoopassociates\.co\.uk$/',$_POST['email'])) {
			if ($ki->unit_testing) {
				return 'set_user_email_denied';
			}
			throw new Kohana_Exception('permissions.set_user_email_denied',$_POST['email'].' (by user '.$ki->person->rid);
		}
		
	}
	
	// Another copy and past job. Pretty it up, boy.
	public function setProfileImage() {
		if ($_POST['profile_asset_rid'] == 'remove') {
			foreach (O::f('relationship')->join("relationship_partner as r1","r1.relationship_id","relationship.id")->join('relationship_partner as r2','r1.relationship_id','r2.relationship_id')->where("r1.item_tablename='person' and r1.item_rid = $person_v->rid and r2.description='profilepic' and r2.item_tablename='asset'")->find_all() as $rel) {
				O::q("delete from relationship where id = $rel->id");
			}
		} else {
			$asset = O::fa('asset',$_POST['profile_asset_rid']);
			if ($asset->rid) {
				foreach (O::f('relationship')->join("relationship_partner as r1","r1.relationship_id","relationship.id")->join('relationship_partner as r2','r1.relationship_id','r2.relationship_id')->where("r1.item_tablename='person' and r1.item_rid = $person_v->rid and r2.description='profilepic' and r2.item_tablename='asset'")->find_all() as $rel) {
					O::q("delete from relationship where id = $rel->id");
				}

				Relationship::create_relationship(array('person','asset'),array($person_v->rid,$asset->rid),array('','profilepic'));
			}
		}
	}
	
	/**
	* Determine whether the person is a Hoop user.
	*
	* @return boolean True if they're Hoop, false if a guest or some other user.
	*/
	public function isHoop()
	{
		return true;
	}
	
	public function save( Validation $validation = NULL ) {
		// Copy and pasted from old Person library. Needs prettying.
		$uploadedby_tag = O::fa('tag')->find_by_name('Uploaded by');
		$this_uploadedby_tag = O::fa('tag');
		$this_uploadedby_tag->parent_rid = $uploadedby_tag->rid;
		$this_uploadedby_tag->name = $_POST['firstname'] . " " . $_POST['surname'];
		$this_uploadedby_tag->save_activeversion();
		
	}
	
}

?>