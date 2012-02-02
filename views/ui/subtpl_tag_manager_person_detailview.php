<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<div id="sledge-person-detailview">

	<form action="/cms/people/save/<?=$person->id;?>" method="post" enctype='multipart/formdata' <?//onsubmit="return false;"?>>
		
		<input type="hidden" name="person_id" id='person_id' value="<?=$person->id;?>" />
		<input type="hidden" name="groups" value="<?=implode(',', $person->groups->find_all()->as_array())?>" />

		<div class="sledge-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#sledge-person-detailview-information<?=$person->id;?>">Information</a></li>
				<li><a href="#sledge-person-detailview-activity<?=$person->id;?>">Activity</a></li>
				<li><a href="#sledge-person-detailview-groups<?=$person->id;?>">Groups</a></li>
				<li><a href="#sledge-person-detailview-permissions<?=$person->id;?>">Permissions</a></li>
			</ul>

			<div class="ui-tabs-panel ui-widget-content ui-helper-left">
				<?
					if ($person->image->loaded()):
						?>
							<a href="/asset/view/<?=$person->image->id;?>/600" 
								title="<?= $person->getName() ?>" 
								title="Click for larger view" 
								class="ui-helper-left sledge-asset-preview">
								<img class="ui-state-active ui-corner-all" src="/asset/view/<?=$person->image->id;?>/160">
							</a>
						<?
					endif;
				?>
			</div>

			<div id="sledge-person-detailview-information<?=$person->id;?>" class="ui-helper-left">
				<table width="100%">
					<tbody>
						<tr>
							<td><label for="person-firstname">First name:</label></td>
							<td><input type="text" id="person-firstname" name="firstname" class="sledge-input" value="<?=$person->firstname?>" /></td>
						</tr>
						<tr>
							<td><label for="person-lastname">Surname:</label></td>
							<td><input type="text" id="person-lastname" name="surname" class="sledge-input" value="<?=$person->lastname?>" /></td>
						</tr>
						<tr>
							<td><label for="person-email">Email:</label></td>
							<td><input type="text" id="person-email" name="email" class="sledge-input" value="<?=$person->emailaddress?>" /></td>
						</tr>
						<tr>
							<td><label for="person-password">New Password:</label></td>
							<td><input id="person-password" class="sledge-input" type="text" name="password"/></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="sledge-person-detailview-activity<?=$person->id;?>" class="ui-helper-left">
				<?
					if ($person->activities->count_all() > 0):
						$i = 0;
						?>
						<table width="100%">
							<thead>
								<th>Time</th>
								<th>Activity</th>
								<th>Note</th>
							</thead>
							<tbody>
								<?foreach ($person->activities->find_all() as $al):?>
									<tr class="sledge-row-<?if (($i%2)==0) echo 'odd'; else echo 'even';?>">
										<td><?=date('d F Y H:i:s', strtotime($al->time));?></td>
										<td><?=$al->activity;?></td>
										<td><?=$al->note;?></td>
									</tr>
									<?$i++;?>
								<?endforeach;?>
							</tbody>
						</table>
					<?else:?>
						<p>
							(No activity logged)
						</p>
					<?endif;
				?>
			</div>

			<div id="sledge-person-detailview-groups<?=$person->id;?>" class="ui-helper-left">
					User is a member of these groups:
			
						<?
							foreach( $person->groups->find_all() as $group ):
								echo $group->name, "<input type='checkbox' name='group_id' value='", $group->id, "'><br />";
							endforeach;
						?>
				
						<button class="sledge-button ui-button-text-icon sledge-tagmanager-person-groups-add" onclick="document.location='/cms/people/add_group/<?= $person->id ?>'; return false;">
							<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
							Add Group
						</button>
				</form>
			</div>

			<div id="sledge-person-detailview-permissions<?=$person->id;?>" class="ui-helper-left">
				<table width="100%">
					<tbody>
					<?
						/*$i=0;
						foreach (Permissions::get_permission_counts($person->rid,false) as $iperm) {
							foreach ($iperm as $page_rid=>$perm) {
								if (isset($perm['page']['can']) || isset($perm['page']['cant'])) {
									if (!isset($perm['page']['can'])) $perm['page']['can'] = 0;
									if (!isset($perm['page']['cant'])) $perm['page']['cant'] = 0;?>

									<tr class="sledge-row-<?=!($i%2)?'even':'odd'?>">
										<td valign="top" width="30%">
											<a href="#" rel="<?=$page_rid?>" class="sledge-edit-permission-where-page">
												<span>
													<?=$perm['title']?>
												</span>
											</a>
										</td>
										<td>
											<a href="#" class="sledge-edit-permission-what" rel="<?=$page_rid?>">
												<span>
													<?=$perm['page']['can']?> can<?if($perm['page']['can']!=1) echo 's';?>,
													<?=$perm['page']['cant']?> cant<?if($perm['page']['cant']!=1) echo 's';?>
												</span>
											</a>
										</td>
									</tr>
								<?}else{?>
									<tr class="sledge-row-<?=!($i%2)?'even':'odd'?>">
										<td valign="top" width="30%">
											<a href="#" rel="<?=$page_rid?>" class="sledge-edit-permission-where-page">
												<span>
													<?=$perm['title']?>
												</span>
											</a>
										</td>
										<td>
											<a href="#" class="sledge-edit-permission-what" rel="<?=$page_rid?>">
												<span>
													0 cans, 0 cants
												</span>
											</a>
										</td>
									</tr>
								<?}
								if (isset($perm['slot'])) {
									foreach ($perm['slot'] as $slottype=>$slotnames) {
										if (is_array($slotnames)) {
											foreach ($slotnames as $slotname=>$access) {
												if ($slottype == null) {
													$slottype = 'any';
												}
												if ($slotname == null) {
													$slotname = 'any';
												}
												if (!isset($access['can'])) {
													$access['can'] = 0;
												}
												if (!isset($access['cant'])) {
													$access['cant']=0;
												}
												?>
												<tr class="sledge-row-<?=!($i%2)?'even':'odd'?>">
													<td valign="top" width="30%">
														<a href="#" rel="<?=$page_rid?>-<?=$slottype?>-<?=$slotname?>" class="sledge-edit-permission-where-slot">
															<span>
																&nbsp;&nbsp;Slot permissions: <?=ucfirst($slottype)?>, <?=ucfirst($slotname)?>
															</span>
														</a>
													</td>
													<td>
														<a href="#" class="sledge-edit-permission-what slot" rel="<?=$page_rid?>-<?=$slottype?>-<?=$slotname?>">
															<span>
																<?=$access['can']?> can<?if($access['can']!=1) echo 's';?>,
																<?=$access['cant']?> cant<?if($access['cant']!=1) echo 's';?>
															</span>
														</a>
													</td>
												</tr>
											<?}
										}
									}
								}
								$i++;
							}
						}*/
					?>
					</tbody>
				</table>
			</div>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-person-save" rel="<?=$person->rid?>" id='sledge-tagmanager-save-person'>
					<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
					Save
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-person-delete" id="sledge-tagmanager-delete-person" rel="<?=$person->rid?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
					Delete
				</button>
			</div>
		</div>
	</form>
</div>