<?php
/**
* Child settings tab of page settings.
*
* Rendered by:	Controller_Cms_Page_Settings::action_children()
* Submits to:	Controller_Cms_Page::action_save()
*
*********************** Variables **********************
*	$person		****	Instance of Model_Person			****	The active user.
*	$page		****	Instance of Model_Page				****	The page being edited.
*	$templates	****	Array of Model_Template instances	****	Available templates.
********************************************************
*
*/
?>
<form id="sledge-form-pagesettings-childsettings" name="pagesettings-childsettings">
	<div id="child-settings" class="sledge-tabs">
		<ul>
			<li><a href="#child-settings-basic">Basic</a></li>
			<li><a href="#child-settings-advanced">Advanced</a></li>
		</ul>
		<div id="child-settings-basic">
			<table width="100%">
				<?if ($person->can( 'view', $page, 'pagetype_parent_id' )):
					?>
						<tr>
							<td>Children parent page</td>
							<td>
								<?
									if ($person->can( 'edit', $page, 'pagetype_parent_id' )):
										?>
											<select name="pagetype_parent_id" id="pagetype_parent_id" style="width:14em">
											<option value="">Default (this page)</option>
												<?
													foreach( $page->mptt->fulltree() as $node ):
														if( $node->page_id != $page->id ):
															echo "<option value='", $node->page_id, "'";
															echo ">", $node->page->title, "</option>";
														endif;
													endforeach;
												?>				
											</select><br/>
										<?
									endif;
								?>
							</td>
						</tr>
					<?
				endif;
				if ($person->can( 'view', $page, 'default_child_template_id' )):
					?>
						<tr>
							<td>Default child template</td>
							<td>
								<?
									if ($person->can( 'edit', $page, 'default_child_template_id' )):
										?>
											<select name="default_child_template_id">
												<option value="$page->template_id">Same as this page</option><?
													foreach ($templates as $tpl):
														if ($tpl->id == $page->default_child_template_id):
															?><option selected="selected" value="<?=$tpl->id?>"><?=$tpl->name?></option><?
														else:
															?><option value="<?=$tpl->id?>"><?=$tpl->name?></option><?
														endif;
													endforeach;
												?>
											</select>
										<?
									else:
										echo $page->default_child_template->name;
									endif;
								?>
							</td>
						</tr>
					<?
				endif;
				
				if ($person->can( 'edit', $page, 'child_ordering_policy' )):
					?>
						<tr>
							<td>Child ordering policy</td>
							<td>
								<?
									if ($person->can( 'edit', $page, 'child_ordering_policy' )):
										?>
											<select name="child_ordering_policy">
												<option value='<?= Model_Page::CHILD_ORDER_MANUAL ?>'
												<? 
													if ($page->child_ordering_policy & Model_Page::CHILD_ORDER_MANUAL) 
														echo " selected='selected'"; 
												?>
												>Manual</option>
								
												<option value='<?= Model_Page::CHILD_ORDER_DATE ?>'
												<? 
													if ($page->child_ordering_policy & Model_Page::CHILD_ORDER_DATE) 
														echo " selected='selected'"; 
												?>
												>Date</option>
								
												<option value='<?= Model_Page::CHILD_ORDER_ALPHABETIC ?>'
												<? 
												if ($page->child_ordering_policy & Model_Page::CHILD_ORDER_ALPHABETIC) 
													echo " selected='selected'";
												?>
												>Alphabetic</option>
											</select>
											<select name="child_ordering_direction">
												<option value='asc'
												<?
													if ($page->child_ordering_policy & Model_Page::CHILD_ORDER_ASC) 
														echo " selected='selected'"; 
												?>
												>Asc</option>
								
												<option value='desc'
												<? 
													if ($page->child_ordering_policy & Model_Page::CHILD_ORDER_DESC)
														echo " selected='selected'"; 
												?>
												>Desc</option>
											</select>
										<?
									else:
										echo $page->child_ordering_policy;
									endif;
								?>
							</td>
						</tr>
				<? endif; ?>
			</table>
		</div>
		<div id="child-settings-advanced">
			<table width="100%">
				<?
					if ($person->can( 'view', $page, 'children_visible_in_leftnav' )):
						?>
							<tr>
								<td>Children visible in leftnav?</td>
								<td>
									<?
										if ($person->can( 'edit', $page, 'children_visible_in_leftnav' )):
											?>
												<select name="children_visible_in_leftnav">
													<option <?if ($page->children_visible_in_leftnav == null) echo "selected='selected'"; ?> value="">Inherit from my parent</option>
													<option <?if ($page->children_visible_in_leftnav == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
													<option <?if ($page->children_visible_in_leftnav == false) echo "selected=\"selected\" ";?> value="0">No</option>
												</select>
											<?
										else:
											echo $page->children_visible_in_leftnav;
										endif;
									?>
								</td>
							</tr>
						<?
				endif;
				if ($person->can( 'view', $page, 'children_visible_in_leftnav_cms' )):
					?>
						<tr>
							<td>Children visible in CMS leftnav?</td>
							<td>
								<?
									if ($person->can( 'edit', $page, 'children_visible_in_leftnav_cms' )):
										?>
											<select name="children_visible_in_leftnav_cms">
												<option <?if ($page->children_visible_in_leftnav_cms == null) echo "selected='selected'"; ?> value="">Inherit from my parent</option>
												<option <?if ($page->children_visible_in_leftnav_cms == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
												<option <?if ($page->children_visible_in_leftnav_cms == false) echo "selected=\"selected\" ";?> value="0">No</option>
											</select>
										<?
									else:
										echo $page->children_visible_in_leftnav_cms;
									endif;
								?>
							</td>
						</tr>
					<?
				endif;
				
				if ($person->can( 'view', $page, 'default_child_uri_prefix')):
					?>
						<tr>
							<td>Default child URI prefix</td>
							<td>
								<?
									if ($person->can( 'edit', $page, 'default_child_uri_prefix' )):
										echo "<input type='text' class='sledge-input' name='default_child_uri_prefix' value='$page->default_child_uri_prefix' />";
									else:
										echo $page->default_child_uri_prefix;
									endif;
								?>
							</td>
						</tr>
					<?
				endif;

				if ($person->can( 'view', 'default_child_default_child_template_id' )):
					?>
						<tr>
							<td>Default grandchild template</td>
							<td>
								<?
									if ($person->can( 'edit', 'default_child_default_child_template_id' )):
										?>
											<select name="default_child_default_child_template_id">
												<option value="">Inherit from childs parent</option>
												<?
													foreach ($templates as $tpl):
														if ($tpl->id == $page->template_id):
															?><option selected="selected" value="<?=$tpl->id?>"><?=$tpl->name?></option><?
														else:
															?><option value="<?=$tpl->id?>"><?=$tpl->name?></option><?
														endif;
													endforeach;
												?>
											</select>
										<?
									else:
										echo $page->default_child_default_child_template_id;
									endif;
								?>
							</td>
						</tr>
					<?
				endif; ?>
			</table>
		</div>
	</div>
</form>
