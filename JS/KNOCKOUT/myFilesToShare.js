function FILE_TO_SHARE(files,fileSave){
	self=this;
	self.myFiles=files;
	self.selectFile=function(data){
		fileSave.FileName=data.name;
		console.log(fileSave);
		var upload = document.getElementById("upload");
		upload.onclick = fileSave.uploadQueue;
		showElem($("#addFile"));
	};
	self.close=function(){
		hideElem($("#myFiles"));
	};
}