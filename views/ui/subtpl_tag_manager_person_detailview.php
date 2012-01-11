<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
$c = isset($this->c) ? $this->c : 0;
$person_groups = array();
foreach (Relationship::find_partners('tag', 'person', $this->item->rid)->find_all() as $group) {
	$person_groups[] = $group->rid;
}
?>

<div id="sledge-person-detailview">

	<form onsubmit="return false;">
		
		<input type="hidden" name="person_rid" value="<?=$this->item->rid;?>" />
		<input type="hidden" name="groups" value="<?=implode(',', $person_groups)?>" />

		<div class="sledge-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#sledge-person-detailview-information<?=$c;?>">Information</a></li>
				<li><a href="#sledge-person-detailview-activity<?=$c;?>">Activity</a></li>
				<li><a href="#sledge-person-detailview-groups<?=$c;?>">Groups</a></li>
				<li><a href="#sledge-person-detailview-permissions<?=$c;?>">Permissions</a></li>
			</ul>

			<div class="ui-tabs-panel ui-widget-content ui-helper-left">

				<a href="/_ajax/call/Person/get_image/<?=$this->item->rid;?>/600" 
					title="<?= $this->item->firstname.' '.$this->item->lastname?>" 
					title="Click for larger view" 
					class="ui-helper-left sledge-asset-preview">
					<img class="ui-state-active ui-corner-all" src="/_ajax/call/Person/get_image/<?=$this->item->rid;?>/160">
				</a>

			</div>

			<div id="sledge-person-detailview-information<?=$c;?>" class="ui-helper-left">
				<table width="100%">
					<tbody>
						<tr>
							<td><label for="person-firstname">First name:</label></td>
							<td><input type="text" id="person-firstname" name="firstname" class="sledge-input" value="<?=$this->item->firstname?>" /></td>
						</tr>
						<tr>
							<td><label for="person-lastname">Surname:</label></td>
							<td><input type="text" id="person-lastname" name="surname" class="sledge-input" value="<?=$this->item->lastname?>" /></td>
						</tr>
						<tr>
							<td><label for="person-email">Email:</label></td>
							<td><input type="text" id="person-email" name="email" class="sledge-input" value="<?=$this->item->emailaddress?>" /></td>
						</tr>
						<tr>
							<td><label for="person-password">New Password:</label></td>
							<td><input id="person-password" class="sledge-input" type="text" name="password"/></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="sledge-person-detailview-activity<?=$c;?>" class="ui-helper-left">
				<?
					$count = 0;
					$activity = O::fa('activitylog')->orderby('timestamp','desc')->limit(50)->find_all_by_audit_person((string)$this->item->rid);
					if (count($activity)) {?>
						<table width="100%">
							<thead>
								<th>Time</th>
								<th>Activity</th>
								<th>Note</th>
							</thead>
							<tbody>
								<?foreach ($activity as $al) {?>
									<tr class="sledge-row-<?if (($count%2)==0) echo 'odd'; else echo 'even';?>">
										<td><?=date('d F Y H:i:s', strtotime($al->audit_time));?></td>
										<td><?=$al->activity;?></td>
										<td><?=$al->note;?></td>
									</tr>
									<?$count++;?>
								<?}?>
							</tbody>
						</table>
					<?} else {?>
						<p>
							(No activity logged)
						</p>
					<?}
				?>
			</div>

			<div id="sledge-person-detailview-groups<?=$c;?>" class="ui-helper-left">


			</div>

			<div id="sledge-person-detailview-permissions<?=$c;?>" class="ui-helper-left">
				<table width="100%">
					<tbody>
					<?
						$i=0;
						foreach (Permissions::get_permission_counts($this->item->rid,false) as $iperm) {
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
						}
					?>
					</tbody>
				</table>

				<p>
					<button class="sledge-button sledge-detailview-person-add-permission" rel="<?=$this->item->rid?>">Add permission</button>
				</p>
			</div>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-person-save" rel="<?=$this->item->rid?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
					Save
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-person-delete" rel="<?=$this->item->rid?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
					Delete
				</button>
			</div>
		</div>
	</form>
</div>
