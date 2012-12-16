function FILE_TO_SHARE(modelUser,files,FileSave){
	self=this;
	self.myFiles=files;
	self.txt="File to share with "+modelUser.userInfo.login;
	self.modelUser=modelUser;
	self.selectFile=function(data){
		//console.log(data);
		//console.log(modelUser);
		FileSave.FileName=data.name;
		showElem($("#addFile"));
	};
	self.close=function(){
		hideElem($("#myFiles"));
	};
}