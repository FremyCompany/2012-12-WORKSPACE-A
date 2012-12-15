userInfo={
		"id" : "myId",
		"Firstname" : "Nicolas",
		"Lastname" : "Bernier",
		"Email" : "berniernico@hotmail.com",
		"Address" : "11, rue Goffart",
		"Phone" : "0474/023291",
		"myFiles" : []
};
var model = new USERPAGE(userInfo);
ko.applyBindings(model, document.getElementById('home'));


function USERPAGE(userInfo) {
	var self=this;
	self.userInfo=userInfo;

	self.userTitle=ko.observable("");
	self.titleFiles=ko.observable("");
	self.myPage=ko.observable(false);
	self.modif=ko.observable(false);
	self.textButton=ko.observable("Modif");
	if(self.userInfo.myFiles.length==0)
	{
		self.isFiles=ko.observable(false);
	}
	else{
		self.isFiles=ko.observable(true);
	}
	if(userInfo.id=="myId"){
		self.userTitle("My profile");
		self.titleFiles("My files");
		self.myPage(true);
	}
	else{
		self.userTitle("Profil of "+userInfo.Firstname);
		self.titleFiles("Files shared with "+userInfo.Firstname);
	}
	self.modifInfo=function(){
		if(self.modif()==false){
			self.textButton("Cancel");
			self.modif(true);
		}
		else{
			self.modif(false);
			self.textButton("Modif");
		}
	};
	self.revocation=function(){
		alert("revocation");
	};
	self.sendPublicKey=function(){
		alert("send public key");
	};
	self.saveModif=function(){
		alert("save modif");
	};
};