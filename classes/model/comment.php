<?php

/**
*
* @package Comment
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo This should probably extend tag, metadata, or stuff. Not sure which at the moment though.
* @todo Or, it could extend a metatable model! That would be cool.
* @todo I'm going with a metatable model, it's gonna be so sweet. That means I can't change these methods at the moment.
* @todo For now this is a direct copy and page from the old Comment library (now removed) so these methods now need to rewritten as a model.
* 
*/
class comment_Model extends metatable_Model {
	protected $table_name = 'Comments';
	protected $table_columns = array( 'id', 'person_rid', 'name', 'email', 'comment', 'status' );

	/*public function __construct($id, $person_rid, $name, $email, $comment, $status, $date) {
		$this->input = Kohana::instance()->input;
		$this->id = $id;
		$this->person_rid = $person_rid;
		$this->name = $this->input->xss_clean(strip_tags($name));
		$this->email = $this->input->xss_clean($email);
		$this->comment = $this->input->xss_clean(strip_tags($comment));
		$this->status = $status;
		$this->date = $date;
		$this->datetime = $date ? substr($date,8,2).".".substr($date,5,2).".".substr($date,0,4)." ".substr($date,11,8) : time();
	}*/

	public function delete_comment($page, $comment_id=0) {
		foreach (Comment::get_comments($page) as $comment) {
			if ($comment->id == $comment_id) {
				$comment->delete();
			}
		}
	}

	public function approve(){
		$comment_status = O::f('metadata_v')->find_by_item_tablename_and_item_rid_and_key('stuff', $this->id, 'status');
		$comment_status->value = 'approved';
		$comment_status->save();
	}

	public function approve_comment($page, $comment_id=0) {
		foreach(Comment::get_comments($page) as $comment) {
			if ($comment->id == $comment_id) {
				$comment->approve();
			}
		}
	}

	public function save($page, $send_message=false, $message_groups=array(), $send_email=false, $from_address='info@website', $approved_status=false) {
		$message_groups = is_array($message_groups) ? $message_groups : array($message_groups);

		$stuff = Tag::find_or_create_tag(1, 'Stuff');
		$comment_stuff_tag = Tag::find_or_create_tag($stuff->rid, 'Comments');
		$metadata = Tag::find_or_create_tag(1, 'Metadata');
		$comment_meta = Tag::find_or_create_tag($metadata->rid, 'Comments');

		$stuff_v = Stuff::create($comment_stuff_tag);

		# Associate the comment stuff with this page
		Relationship::create_relationship(array($stuff_v,$page));

		# Save the comment metadata
		Metadata::create_metadata('stuff',$stuff_v->rid,'name',$this->name,true,false,$comment_metadata_tag);
		Metadata::create_metadata('stuff',$stuff_v->rid,'email',$this->email,true,false,$comment_metadata_tag);
		Metadata::create_metadata('stuff',$stuff_v->rid,'comment',$this->comment,true,false,$comment_metadata_tag);
		Metadata::create_metadata('stuff',$stuff_v->rid,'person_rid',$this->person_rid,true,false,$comment_metadata_tag);

		if ($approved_status) {
			$status = 'approved';
		} else {
			$status = Kohana::config('core.comments_moderate') ? 'awaiting_approval' : 'approved';
		}
		Metadata::create_metadata('stuff',$stuff_v->rid,'status',$status,true,false,$comment_metadata_tag);

		if ($send_message) {

			$uri = str_replace('http:','https:',$page->absolute_uri());

			foreach (O::fa('tag')->where("name = '".implode("' or name = '",$message_groups)."'")->find_all() as $group) {
				foreach (Relationship::find_partners('person',$group)->find_all() as $person) {
					$m = O::f('message_v');
					$m->subject = 'Comment posted on page '.$page->title;
					$body = "A new comment was posted on the \"{$page->title}\" page:<br><br>";
					$body .= "From: {$this->name} {$this->email}<br><br>";
					$body .= "\"".$this->comment."\"<br><br>";
					if (!$approved_status and Kohana::config('core.comments_moderate')) {
						$body .= 'Please click on the link below to moderate the comment.<br><br>';
					}
					$body .= "<a href='$uri' target='_new'>$uri</a>";
					$m->body = $body;
					$m->sender_rid = $person->rid;
					$m->person_rid = $person->rid;
					$m->ref_message_status_rid = 1;
					$m->save_activeversion();

					// send email notification
					if ($send_email) {
						$headers = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= "From: {$from_address}\r\n";
						mail($person->emailaddress, 'New comment posted', $m->body, $headers);
					}
				}
			}
		}

		return $stuff_v;
	}

	public function get_comments($page) {
		$ki = Kohana::Instance();

		$comments = array();
		$stuff = Tag::find_or_create_tag(1, 'Stuff');
		$comment_stuff = Tag::find_or_create_tag($stuff->rid, 'Comments');
		$metadata = Tag::find_or_create_tag(1, 'Metadata');
		$comment_metadata = Tag::find_or_create_tag($metadata->rid, 'Comments');
		$can_write = Permissions::may_i('Write', $ki->page);

		foreach (Relationship::find_partners('stuff',array($page,$comment_stuff))->orderby('stuff_v.audit_time','asc')->find_all() as $stuff_v) {
			$person_rid = Metadata::getmetadata('stuff',$stuff_v,'person_rid',$comment_metadata)->value;
			$name = Metadata::getmetadata('stuff',$stuff_v,'name',$comment_metadata)->value;
			$email = Metadata::getmetadata('stuff',$stuff_v,'email',$comment_metadata)->value;
			$comment = Metadata::getmetadata('stuff',$stuff_v,'comment',$comment_metadata)->value;
			$status = Metadata::getmetadata('stuff',$stuff_v,'status',$comment_metadata)->value;
			if ((Kohana::config('core.comments_moderate') and ($status != 'awaiting_approval' or $can_write)) or !Kohana::config('core.comments_moderate')) {
				$comments[] = new Comment($stuff_v->rid, $person_rid, $name, $email, $comment, $status, $stuff_v->audit_time);
			}
		}
		return $comments;
	}

}

?>
