function ADDFILE(isNew){
	var self=this;
	self.showUpload=ko.observable(false);
	self.showError=ko.observable(false);
	self.showSuccess=ko.observable(false);

	self.infoUpload=ko.observable("");
	self.text=ko.observable("Add a new file");
	self.showLoadingFile=ko.observable(false);
	
	self.closeAdd=function(){
		hideElem($("#addFile"));
	};
	self.isNew=isNew;
	self.showMessage=ko.observable(false);
	self.showSucces=ko.observable(false);
	self.loadingFile=function(){
		if(this.showLoadingFile()==true)
			this.showLoadingFile(false);
		else
			this.showLoadingFile(true);
	};
	self.setTitle=function(txt){
		self.text(txt);
	};
	self.showFail=function(txt){
		this.showUpload(false);
		this.showError(true);
		this.showSuccess(false);
		this.infoUpload(txt);
		
	};
	self.showComplete=function(){
		this.showUpload(false);
		this.showError(false);
		this.showSuccess(true);
		this.infoUpload("Upload complete");
	};
	self.showUploading=function(){
		this.showUpload(true);
		this.showError(false);
		this.showSuccess(false);
		this.infoUpload("Uploading...");
	};
}