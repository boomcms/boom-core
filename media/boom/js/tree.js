/*
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
*/
var taghtml = '';

var Tree = {

	// appends ths expand/shrink elements & bind and handles on related events 
	bind : function(el){
		// first remove any traces of tree elements
		$(".nochild, .expand, .collapse", el).remove();
		// prepend expand/collapse elements, handle click event on this object
		var span, li;
		$(el).each(
		function(){
       			if ($("ul:first", this).length && !$("> .toplevel", this).length) {
   				var 
       					cl = $("ul:first", this).css("display") != "none" ? "collapse" : "expand", 
					li = this; 
				if ($(".asset", this).length) {
					$(".asset:first", this).before('<span class="'+cl+'"></span>');
				} else {
					$("table tr:first", li).prepend('<td><span class="'+cl+'"></span></td>');
				}
					
				$("span:first", this).unbind().click(function(e){
					span = this;
					$("ul:first", li).animate({
						height: 'toggle'
					}, 200,	function(){
						span.className = this.style.display == "none" ? "expand" : "collapse";
						(typeof AssetManager == "object") && AssetManager.separator();
					});
					return false;
				});
			} else if (!$(".asset", this).length && !$(".toplevel", this).length) {
					$("table tr:first", this).prepend('<td><span class="nochild"></span></td>');
			} else if ($("> .toplevel", this).length) {
				$(".toplevel", this).unbind().click(function(e){
					if ($("ul", $(this).parent()).length && !/load-tag-contents/.test(this.className)) {
						e.preventDefault();
					}
					if (/cms/i.test(window.location) && e.target.nodeName.toLowerCase() == 'img' && typeof TagManager == "object" && !TagManager.modal) {
						TagManager.Tree.Tags.add(e);
					} else {
						var
							self = this,
							$a = $(this);

						$(".toplevel", $(this).parents('.tags')).each(function(){
							var tl = this;
							if (self.href !== this.href) {
								$("ul:first", $(this).parent()).animate({
									height : 'hide'
								}, 200, function(){
									$(tl).removeClass("open").addClass("close");
								});
							}
						});
						($("ul:first", $a.parent()).css("display") == "none") && $("ul:first", $a.parent()).animate({
							height: 'toggle'
						}, 200, function(){
							if ($a.hasClass("open")) {
								$a.removeClass("open").addClass("close");
								$a.attr("href", "#tag/"+$a[0].id.replace(/tag_/, '')+"/");
							} else {
								$a.removeClass("close").addClass("open");
								$a.attr("href", "#tag/"+$a[0].id.replace(/tag_/, '')+"/");
							}
							(typeof TagManager == "object") && TagManager.separator();
						});
					}
				});
			}
		});
	},

	// select/deselect tree items
	bindSelect : function(el, action, multiple) {
		
		(action == undefined) && (action = "move");
		(multiple == undefined) && (multiple = false);
		
		// anchor click
		$(el).click(function(e){
			if (!e.target || (e.target.nodeName.toLowerCase() != 'span' && e.target.className != 'edit' && e.target.className != 'delete')){
				e.preventDefault();
				var $this = $(this);
				if ($this.hasClass("current")) {
					$this.removeClass("current");
				} else {
					if (!multiple || action == "copy") {
						// move up the tree and de-select any selected tags
						var $parent = $this, i = 1;
						do {
							$parent = $parent.parent().parent().parent();
							if ($parent.hasClass("tags")) {
								break;
							}
							$parent = $("a:first", $parent.parent()).removeClass("current");
							i++;
						} while (i<10); 
						// prevent selecting this tag if any tags down the tree are selected
						if (!$("a.current", $this.parent().parent()).length) {
							$this.addClass("current");
						}
					} else {
						// move up the tree and determine if any tags have been selected
						var $parent = $this, i = 1, noSelect = false;
						do {
							$parent = $parent.parent().parent().parent();
							if ($parent.hasClass("tags")) {
								break;
							}
							$parent = $("a:first", $parent.parent());
							if ($parent.hasClass("current")) {
								noSelect = true;
							}
							i++;
						} while (i<10); 
						
						// remove any selected tags down the tree
						$("ul li a", $(this).parent().parent()).each(function(){
							//this.className = "";
						});
						if (!noSelect) {
							$this.addClass("current");
						}
					}
				}
			}
		});
	},

	// this function will only ever be called in the page manager view
	bindManage : function(el) {
		var 
			$edit = $('<span class="edit" title="edit">&nbsp;</span>'),
			$delete = $('<span class="delete" title="delete">&nbsp;</span>');

		$(el).hover(
			function(e){
				$(this).append(' ').append($edit).append($delete);
			},
			function(e){
				$("span", this).remove();
			}
		).click(function(e){
			var tag_rid = this.id.replace(/tag_/, '');
			if (e.target && e.target.className == 'edit') {
				$("#TB_window #label-cms-add-tags").html('Edit tag:');
				$("#TB_window #cms-add-tags").val($.trim($(this).text()));
				$("#TB_window #add-tag-id").val(this.id.replace(/tag_/, ''));
				// find the tag's parent in the tree
				var parent_tag_id = $("a:first", $(this).parents("li").get(1))[0].id.replace(/tag_/, '');
				if (parent_tag_id) {
					$("#TB_window #new_tag_parent_rid")[0].value = parent_tag_id;
				}
				$("#TB_window .cms-add-tags-add .center").html("Update");
			} else if (e.target && e.target.className == 'delete') {
				var xhr = $.ajax({
					type : "GET",
					// get_tag_tree_html/$basepath=false, $basetag=false, $selectedtag=false, $depth=NULL, $selected=NULL, $modal=0, $multiple=0, $toplevel=true
					url : "/_ajax/call/tag/get_tag_tree_html/cms/NULL/"+tag_rid+"/NULL/NULL/NULL/0/0/0/ajax",
					data : "modal=true",
					cache : false,
					error:function(xhr, textStatus, error){
						if (xhr.status != 0) {
							alert("Sorry, there was an error processing the request.<br/>Please try again.");
						}
					},
					success : function(data){
						Page.abortRequests();
						var msg = '';
						if ($("li", data).length) {
							msg += 'All ('+$("li", data).length+") child tags will also be deleted.\n";
						}
						msg += 'Are you sure you want to delete this tag?';
						if (confirm(msg)) {
							var xhr2 = $.ajax({
								type : "GET",
								url : "/_cms_tag_manager/remove_tag/"+tag_rid,
								error : function(xhr, textStatus, error) {
									if (xhr.status != 0) {
										alert("Sorry, there was an error processing the request.<br/>Please try again.");
									}
								},
								success : function(data) {
									setTimeout(function(){
										$.jGrowl("Tag successfully removed!");
									}, 200);
									$("#tab6").html('<div style="margin:1em 0em 2em 0em;" class="loading">loading, please wait..</div>');
									Modal.position();
									Page.loadTagCategories();
								}
							});
							Page.requestStack.push(xhr2);
						}
					}
				});
				Page.requestStack.push(xhr);
			}
		});
	},

	// $edition='cms', $basepath=false, $basetag=false, $selectedtag=false, $depth=NULL, $selected=NULL, $modal=0, $multiple=0, $toplevel=true

	get_tag_tree_html : function(edition, basepath, basetag, selectedtag, depth, selected, modal, multiple, toplevel, editionController, callback, cache) {

		(depth == undefined) && (depth = "NULL");
		(selected == undefined) && (selected = "NULL");
		(editionController == undefined) && (editionController = "ajax");
		(cache == undefined) && (cache = true);
		(!selectedtag) && (selectedtag = "NULL");

		if (taghtml != '' && cache) {
			callback(taghtml);
			return;
		}
	
		var xhr = $.ajax({
			type : "GET",
			url : "/_ajax/call/tag/get_tag_tree_html/"
			+ edition + "/"
			+ basepath + "/"
			+ basetag + "/"
			+ selectedtag + "/"
			+ depth + "/"
			+ selected + "/"
			+ modal + "/"
			+ multiple + "/"
			+ toplevel,
			cache : false,
			error:function(xhr, textStatus, error){
				if (xhr.status != 0) {
					Modal.alert("Sorry, there was an error processing the request.<br/>Please try again.");
				}
			},
			success : function(msg){
				(typeof TagManager == "object") && TagManager.abortRequests();
				taghtml = msg;
				(callback != undefined) && callback(msg);
			}
		});
		(typeof TagManager == "object") && TagManager.requestStack.push(xhr); 
	}
};
