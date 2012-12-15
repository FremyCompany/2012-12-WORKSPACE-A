function ADDFILE(){
	self=this;
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
}