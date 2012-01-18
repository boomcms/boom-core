<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<p class="information">
	We're sorry you're experiencing difficulties with your CMS. This form is designed to help you give us the information we need to correct the problem for you.
</p>
<div style="margin-top: 20px;">
	<iframe name="reportproblemuploadiframe" class="hidden" src="javascript:false"></iframe>
	<form id="report-problem-form" method="post" action="/cms/report_problem" enctype="multipart/form-data" target="reportproblemuploadiframe">
		<input type="hidden" name="report_problem_page-url" id="report_problem_page-url" value="" />
		<input type="hidden" name="report_problem_extra_data" id="report_problem_extra_data" value="" />
		<table class="issue-tracker">
			<tbody id="issue-tracker-page1">
				<tr style="border: none;">
					<td>
						<p>Please tell us which area of the CMS you're having trouble with.</p>
					</td>
				</tr>
				<tr>
					<td>
						<select name="report_problem_component" id="report_problem_component">
							<option value="">- Please select -</option>
							<option value="1"<?if ($area==1){?> selected="selected"<?}?>>Page editor</option>
							<option value="2"<?if ($area==2){?> selected="selected"<?}?>>Asset manager</option>
							<option value="3"<?if ($area==3){?> selected="selected"<?}?>>People manager</option>
							<option value="4"<?if ($area==4){?> selected="selected"<?}?>>Pages of the website</option>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<p>Please sum the problem up in one sentence (this will be the subject line of your support request).</p>
					</td>
				</tr>
				<tr>
					<td><?=form::input('report_problem_subject','','size="55"')?></td>
				</tr>
				<tr>
					<td>
						<span id="report_problem_subject-error" style="color: #f00;">&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td>
						<p>Please describe the problem you're having giving as much detail as you can.</p>
						<p>The more detail you provide the more likely we will be able to fix the problem quickly.</p>
					</td>
				</tr>
				<tr>
					<td><?=form::textarea('report_problem_problem','','rows="8" cols="55"')?></td>
				</tr>
				<tr>
					<td>
						<span id="report_problem_problem-error" style="color: #f00;">&nbsp;</span>
					</td>
				</tr>
			</tbody>
			<tbody id="issue-tracker-page2" style="display: none;">
				<tr>
					<td>
						<p>If your problem relates to a specific page of the website, please select it here.</p>
					</td>
				</tr>
				<tr>
					<td>
						<select name="report_problem_site-page" id="report_problem_site-page">
							<option value="">- Please select a page -</option>
							<?
							$r = new Recursion_Page_Combo;
							$r->recurse(O::f('site_page')->get_homepage(),$this->page->rid,true,false,false,false,false,false,false,false,false);
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<p>Please provide step-by-step instructions that will enable us to reproduce the problem.  It is extremely difficult and often impossible to fix problems if we are not able to reproduce them ourselves.</p>
					</td>
				</tr>
				<tr>
					<td><?=form::textarea('report_problem_reproduce','','rows="8" cols="55"')?></td>
				</tr>
				<tr>
					<td>
						<span id="report_problem_reproduce-error" style="color: #f00;">&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td>
						<p>Optionally you can attach a screenshot image showing the problem.</p>
					</td>
				</tr>
				<tr>
					<td><input type="file" name="report_problem_screenshot" /></td>
				</tr>
				<tr>
					<td>
						<span id="report_problem_screenshot-error" style="color: #f00;">&nbsp;</span>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<div style="margin: 0; width: 480px;" id="report_problem_controls-page1">
	<a id="cancel-issue-tracker" href="#" class="button left" style="margin-left: 20px; margin-bottom: 20px;">
		<span class="left">&nbsp;</span>
		<span class="center">Cancel</span>
		<span class="right">&nbsp;</span>
	</a>
	<a id="issue-tracker-next" href="#" class="button right">
		<span class="left">&nbsp;</span>
		<span class="center">Next &raquo;</span>
		<span class="right">&nbsp;</span>
	</a>
</div>
<div style="margin: 0; width: 480px; display: none;" id="report_problem_controls-page2">
	<a id="issue-tracker-back" href="#" class="button left" style="margin-left: 20px;">
		<span class="left">&nbsp;</span>
		<span class="center">&laquo; Back</span>
		<span class="right">&nbsp;</span>
	</a>
	<img style="display:none;" class="cmsloader" id="report_problem_loader" src="/sledge/img/ajax-loader.gif" alt="wait" />
	<span id="report_problem_loader_text" style="display: none;">Please wait...</span>
	<a id="issue-tracker-send" href="#" class="button right">
		<span class="left">&nbsp;</span>
		<span class="center">Send problem report</span>
		<span class="right">&nbsp;</span>
	</a>
</div>
