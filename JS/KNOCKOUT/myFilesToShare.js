$.ajax({
	url : "/TEMPLATE/HTML/addFile.html",
	cache : false
}).done(function(html) {
	$("#addFile").html(html);
});

function FILE_TO_SHARE(modelUser,files){
	self=this;
	self.myFiles=files;
	self.txt="File to share with "+modelUser.userInfo.login;
	self.modelUser=modelUser;
	self.selectFile=function(data){
		console.log(data);
		console.log(modelUser);
		var modelAddFile=new ADDFILE(false);
		ko.applyBindings(modelAddFile, document.getElementById('addFile'));
		if (typeof FileReader == "undefined") alert ("Sorry your browser does not support the File API and this demo will not work for you");
		FileSave = new FileSave(
				[null,
				 null,
				 document.getElementById("Key"),
				 document.getElementById("KeySign")],
				 modelAddFile,
				 document.getElementById("uploading_info"),
				 modelUser.userInfo.login,
				 data.name
		);
		FileSave.init();
		var upload = document.getElementById("upload");
		upload.onclick = FileSave.uploadQueue;
		showElem($("#addFile"));
	};
	self.close=function(){
		hideElem($("#myFiles"));
	};
}