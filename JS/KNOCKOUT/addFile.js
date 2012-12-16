function ADDFILE(){
	self=this;
	self.showLoadingFile=ko.observable(false);
	self.closeAdd=function(){
		hideElem($("#addFile"));
	};
	self.newFile=function(){
		alert("save");
	};
	self.showMessage=ko.observable(false);
	self.showSucces=ko.observable(false);
	self.loadingFile=function(){
		if(self.showLoadingFile())
			self.showLoadingFile(false);
		else
			self.showLoadingFile(true);
	};
}