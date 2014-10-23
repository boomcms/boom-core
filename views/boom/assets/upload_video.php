<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Uploading video</title>
	<link media="all" href="/media/boom/css/cms.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="/media/boom/js/jquery.uploadProgress.js"></script>
</head>
<body id="b-assets-upload-video">

<form id="uploadForm" action="<?=$url?>" method="POST" enctype="multipart/form-data">
	<fieldset>
		<label>Select video</label>
		<input id="uploadToken" type="hidden" value="<?=$token?>" />
		<input id="uploadFile" type="file" name="file" />
		<div id="uploadBar" style="width:480px; float:left; display:none; background:#FFF; margin:5px 0;">
        <div id="uploadProgress" style="background:#46800d; width:0px; height:18px;"></div></div>
		<small id="uploadText">You can upload any video format (WMV, AVI, MP4, MOV, FLV, ...)</small>
	</fieldset>
</form>


<script type="text/javascript">
$(document).ready(function() {
	// Attach an uploadProgress instance to the form. This tool will poll the server for progress.
	$('#uploadForm').uploadProgress({
		// The javascript paths are needed because uploadProgress builds an iframe that sits on top of the page.
		jqueryPath: "/media/boom/js/jquery.js",
		uploadProgressPath: "/media/boom/js/jquery.uploadProgress.js",
		// The uploadProgress bar had just been inserted into the form.
		progressBar: '#uploadProgress',
		// This is the BOTR callback for upload progress.
		progressUrl: '//upload.bitsontherun.com/progress',
		// The token is needed to request fallback. It is pulled from the form.
		uploadToken: $('#uploadToken').val(),
		interval:1000,
		// When the upload starts, we hide the input, show the progress and disable the button.
		start: function() {
			filename = $("#uploadFile").val().split(/[\/\\]/).pop();
			$("#uploadFile").css('display','none');
			$("#uploadBar").css('display','block');
		},
		// During upload, we update both the progress div and the text below it.
		uploading: function(upload) {
			if (upload.percents == 100) {
				window.clearTimeout(this.timer);
			} else {
				$("#uploadText").html('Uploading ' + filename + ' (' + upload.percents + '%) ...');
			}
		}
	});
});
</script>


</body>
</html>