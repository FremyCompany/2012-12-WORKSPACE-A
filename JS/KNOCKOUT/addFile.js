function ADDFILE(isNew,fileName){
	self=this;
	self.showUpload=ko.observable(false);
	self.showError=ko.observable(false);
	self.showSuccess=ko.observable(false);

	self.infoUpload=ko.observable("");
	self.text=ko.observable("Add a new file");
	self.showLoadingFile=ko.observable(false);
	self.closeAdd=function(){
		hideElem($("#addFile"));
	};
	self.newFile=function(){
		alert("save");
	};
	self.isNew=isNew;
	self.showMessage=ko.observable(false);
	self.showSucces=ko.observable(false);
	self.loadingFile=function(){
		if(self.showLoadingFile())
			self.showLoadingFile(false);
		else
			self.showLoadingFile(true);
	};
	self.setTitle=function(txt){
		self.text(txt);
	}
	self.showFail=function(txt){
		self.showUpload(false);
		self.showError(true);
		self.showSuccess(false);
		self.infoUpload(txt);
		
	};
	self.showComplete=function(){
		self.showUpload(false);
		self.showError(false);
		self.showSuccess(true);
		self.infoUpload("Upload complete");
	};
	self.showUploading=function(){
		self.showUpload(true);
		self.showError(false);
		self.showSuccess(false);
		self.infoUpload("Uploading...");
	};
}