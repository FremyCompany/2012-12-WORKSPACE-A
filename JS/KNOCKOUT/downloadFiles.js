function DOWNLOADFILES(){
	var self=this;
	self.setFile=function(file){
		self.file=file;
	};
	self.close=function(){
		hideElem($("#downloadFile"));
	};
}