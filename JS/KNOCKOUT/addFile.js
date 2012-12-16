function ADDFILE(){
	self=this;
	self.showLoadingFile=ko.observable(false);
	self.closeAdd=function(){
		$("body").css('overflow','scroll');
		$("#addFile").find('.content').css('position','relative').css('top','0px').animate({top: '-1000px'},500,function(){
		});
		$("#addFile").fadeOut(500);
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