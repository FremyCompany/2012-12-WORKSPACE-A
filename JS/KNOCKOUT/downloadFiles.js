function DOWNLOADFILES(){
	var self=this;
	self.urlFile=ko.observable("");
	self.urlFileSign=ko.observable("");
	self.urlKey=ko.observable("");
	self.urlKeySign=ko.observable("");

	self.setFile=function(fileName,userLogin){
		self.file=fileName;
		self.urlFile("/PHP/download.php?file="+escape(fileName)+"&mode=file&submode=data&user="+escape(userLogin));
		self.urlFileSign("/PHP/download.php?file="+escape(fileName)+"&mode=file&submode=sign&user="+escape(userLogin));
		self.urlKey("/PHP/download.php?file="+escape(fileName)+"&mode=skey&submode=data&user="+escape(userLogin))
		self.urlKeySign("/PHP/download.php?file="+escape(fileName)+"&mode=skey&submode=sign&user="+escape(userLogin))
		
	};
	self.downloadFile=function(){
		window.open(self.urlFile());

	};
	self.downloadFileSign=function(){
		window.open(self.urlFileSign());
	};
	self.downloadKey=function(){
		window.open(self.urlKey());
	};
	self.downloadKeySign=function(){
		window.open(self.urlKeySign());
	};
	self.close=function(){
		hideElem($("#downloadFile"));
	};
}