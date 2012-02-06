<?php
/**
* Form to add a new page.
* Gives the option to chose the parent page and template of the new page.
*
* Rendered by: Controller_Cms_Page::action_add()
*
*********************** Variables **********************
*	$page		****	Instance of Model_Page				****	The page from which the add page button was clicked. 
*	$templates	****	Array of Model_Template instances	****	Array of available templates.
********************************************************
*
*/
?>
<form id="sledge-page-add-form">	
	<table>
		<tr>
			<td>
				<label for="parent-page">
					Parent page
				</label>
			</td>
			<td>
				<select name="parent_id" style="width:24em">
					<option value="0">No parent</option>
					<?
						foreach( $page->mptt->fulltree() as $node ):
							echo "<option value='", $node->page_id, "'";
							
							if ($node->page_id == $page->id)
								echo " selected='selected'";
								
							echo ">", $node->page->title, "</option>";
						endforeach;
					?>
					</select>
				</select>
			</td>
		</tr>
		
		<?
		//if ($p['attributes'][$template_change_required_perm]):?>
			<tr>
				<td>Template</td>
				<td>
					<select name="template_id" style="width: 24em">
						<option value="">Inherit from parent</option>
						<?
							foreach ($templates as $tpl):
								if ($tpl->id == $page->default_child_template_id):
									echo "<option selected='selected' value='", $tpl->id, "'>", $tpl->name, "</option>";
								else:
									echo "<option value='", $tpl->id, "'>". $tpl->name, "</option>";
								endif;
							endforeach;
						?>
					</select>
				</td>
			</tr>
	</table>
</form>
