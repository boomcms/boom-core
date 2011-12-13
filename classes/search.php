<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php defined('SYSPATH') or die('No direct script access.');

/*** Search class for slege ***/

/*	$query is an array of:

	'field'
	'query'
	'type'	 ('equal' || 'tsvector')

		$join is one of:

	'table'
	'clause' 

		$orderby is an arary of:

	'field'
	'asc' || 'desc'
*/
	
class Search {

	public	$table;
	public	$query;
	public	$join;
	public	$where;
	public	$orderby;
	public	$page;
	public	$tsquery_op;
	public	$items_per_page;
	public	$offset;
	public	$results;
	public	$pagination;
	public $fromlevel;
	public $where_operator = ' and ';

	public function __construct($table, $query, $join, $orderby, $page = 0, $items_per_page = 30, $search_and = 0, $fromlevel = null, $where_or = false, $extra_where=false) {
		$query_string = '';
		foreach ($query as $q) {
			if (isset($q['query'])) {
				if ($query_string) $query_string .= ' ';
				$query_string .= $q['query'];
			}
		}
		$this->log($query_string);
		$this->init($table, $query, $join, $orderby, $page, $items_per_page, $search_and, $fromlevel, $where_or, $extra_where);
	}

	public function log($query) {
		$ki = Kohana::Instance();

		$log = O::f('log');
		$log->ref_log_type_id = 1;
		$log->user_agent = @$_SERVER['HTTP_USER_AGENT'];
		$log->ip = @$_SERVER['REMOTE_ADDR'];
		$log->person_rid = $ki->person->rid;
		$log->log_entry = $query;
		$log->http_referer = @$_SERVER['HTTP_REFERER'];
		$log->server_name = @$_SERVER['SERVER_NAME'];
		$log->save();
	}

	public function init($table, $query, $join, $orderby, $page = 1, $items_per_page = 30, $search_and = 0, $fromlevel = null, $where_or = false, $extra_where=false) {
		$this->table		= $table;
		$this->query		= $query;
		$this->join		= $join;
		$this->orderby		= $orderby;
		$this->page		= $page;
		$this->tsquery_op	= ($search_and)?'&':'|';
		$this->items_per_page	= $items_per_page;
		$this->fromlevel = $fromlevel;
		$this->where_operator = ($where_or) ? ' or ' : ' and ';
		$this->extra_where = $extra_where;

		$this->count();

		if(!$this->page || $this->page > (floor($this->nr/$this->items_per_page)+1) )	$this->page = 1;
		if(!$this->items_per_page || (!ctype_digit($this->items_per_page) && !is_int($this->items_per_page)))		$this->items_per_page = 30;

		$this->offset		= ($this->page-1) * $this->items_per_page;

		$this->results();
	}

	private function count() {

		$this->base_query = O::f($this->table);

		/* this doesn't work because we can't call ORM functions one by one :( */
		/*if(is_array($this->join) && count($this->join)>0) {
			foreach($this->join as $table => $clause) {
				$this->base_query = $this->base_query->join($table, $clause, '', 'INNER');
			}
		}*/

		$this->where = $this->extra_where;

		$has_query = false;

		foreach($this->query as $query) {
			if($this->where) $this->where .= $this->where_operator;

			if ($query['query']) $has_query = true;

			if($query['type'] == 'tsvector') {
				$query['query'] = str_replace(',',' ',$query['query']);
				while (preg_match('/	/',$query['query'])) $query['query'] = str_replace('	',' ',$query['query']);
				$query['query'] =  Database::instance()->escape(trim($query['query']));
				$querystr = pg_escape_string(str_replace(' ',$this->tsquery_op,str_replace('+',$this->tsquery_op,$query['query'])));
				$this->where .= "to_tsquery('$querystr')::tsquery @@ ".$query['field']."::tsvector";
			} else if($query['type'] == 'equal') {
				$this->where .= $query['field']." = '".pg_escape_string($query['query'])."'";
			} else if ($query['type'] == 'ilike') {
				$this->where .= $query['field']." ilike '%".pg_escape_string($query['query'])."%'";
			} else {
				die($query['type']);
			}
		}

		if (!$has_query) {
			$this->nr = 0;
			return;
		}

		if ($this->fromlevel) {
			# This should be set to the internal_name of a page that we want to search under (i.e. return only children of that page)
			$page_from = O::f('site_page')->find_by_internal_name($this->fromlevel);
			if ($this->where) {
				$this->where .= " AND mptt_left BETWEEN ".($page_from->mptt_left+1)." AND $page_from->mptt_right";
			} else {
				$this->where = " mptt_left BETWEEN ".($page_from->mptt_left+1)." AND $page_from->mptt_right";
			}
		}

		# Exclude search and 404 pages
		if ($this->where) {
			$this->where .= " AND uri !='search/results' AND uri != '404'";
		} else {
			$this->where = " uri !='search/results' AND uri != '404'";
		}

		$this->where = "(".$this->where.") AND $this->table.hidden_from_search_results != 't'";

		if(is_array($this->join)) {
			$this->nr = ($r=$this->base_query->join($this->join['table'],$this->join['clause'],'','INNER')->where($this->where)->find_all())?$r->count():0;
		} else {
			$this->nr = ($r=$this->base_query->where($this->where)->find_all())?$r->count():0;
		}
	}

	public function get_count() {
		return $this->nr;
	}

	private function results() {
		if(is_array($this->join)) {
			$this->results = $this->base_query->join($this->join['table'],$this->join['clause'],'','INNER')->where($this->where)->orderby($this->orderby);
		} else {
			$this->results = $this->base_query->where($this->where)->orderby($this->orderby);
		}

		$has_query = false;

		/* if there was a tsvector query then we need to add a select clause to get the rank */
		$select = null;
		foreach($this->query as $query) {
			if ($query['query']) $has_query = true;

			if($query['type'] == 'tsvector') {
				if(!$select) {
					$select .= $this->table.".*, ";
				} else {
					$select .= ", ";
				}

				$query['query'] = Database::instance()->escape(str_replace(',',' ',$query['query']));
				while (preg_match('/	/',$query['query'])) $query['query'] = str_replace('	',' ',$query['query']);
				$query['query'] = trim($query['query']);
				$querystr = pg_escape_string(str_replace(' ',$this->tsquery_op,str_replace('+',$this->tsquery_op,$query['query'])));
				$select .= "ts_rank_cd(".$query['field'].", to_tsquery('$querystr')) as rank";
			}
		}

		if (!$has_query) {
			$this->results = array();
			return;
		}

		if($select)	$this->results = $this->results->select($select);
		$this->results = $this->results->limit($this->items_per_page, $this->offset)->find_all();
	}

	public function get_results() {
		return $this->results;
	}

	public function get_offset() {
		return $this->offset;
	}
}

?>