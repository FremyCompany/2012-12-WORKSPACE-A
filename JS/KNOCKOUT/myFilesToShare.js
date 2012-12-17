function FILE_TO_SHARE(files){
	self=this;
	self.myFiles=files;
	self.txt="Choose one file";
	self.selectFile=function(data){
		$.ajax({
			url : "/TEMPLATE/HTML/addFile.html",
			cache : false
		}).done(function(html) {
			$("#addFile").html(html);
			var modelAddFile=new ADDFILE(false);
			modelAddFile.setTitle("Share the file \""+data.name+"\"");
			ko.applyBindings(modelAddFile, document.getElementById('addFile'));
			if (typeof FileReader == "undefined") alert ("Sorry your browser does not support the File API and this demo will not work for you");
			fileSave = new FileSave(
					[null,
					 null,
					 document.getElementById("Key"),
					 document.getElementById("KeySign")],
					 modelAddFile,
					 user
			);
			fileSave.init();
			fileSave.FileName=data.name;
			var upload = document.getElementById("upload");
			upload.onclick = fileSave.uploadQueue;
			showElem($("#addFile"));
		});
	};
	self.close=function(){
		hideElem($("#myFiles"));
	};
}