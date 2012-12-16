/*userInfo={
		"id" : "myId",
		"Firstname" : "Nicolas",
		"Lastname" : "Bernier",
		"mail" : "berniernico@hotmail.com",
		"Address" : "11, rue Goffart",
		"Phone" : "0474/023291",
		"myFiles" : []
};*/

$.ajax({
	url : "/TEMPLATE/HTML/revokeAccount.html",
	cache : false
}).done(function(html) {
	$("#revokeAccount").html(html);
	var modelRevoke=new REVOKE();
	ko.applyBindings(modelRevoke, document.getElementById('revokeAccount'));
});
Secloud.getMyUserInfo(function(ok,rep){
	if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
	alert(JSON.stringify(rep));
	var model = new USERPAGE(rep);
	ko.applyBindings(model, document.getElementById('home'));
});

function USERPAGE(userInfo) {
	var self=this;
	self.userInfo=userInfo;
	self.userInfoObservable={
			"id" : ko.observable(self.userInfo.id),
			"Firstname" : ko.observable(self.userInfo.Firstname),
			"Lastname" : ko.observable(self.userInfo.Lastname),
			"Email" : ko.observable(self.userInfo.mail),
			"Address" : ko.observable(self.userInfo.Address),
			"Phone" : ko.observable(self.userInfo.Phone),
	};
	
	self.userTitle=ko.observable("");
	self.titleFiles=ko.observable("");
	self.myPage=ko.observable(false);
	self.modif=ko.observable(false);
	self.textButton=ko.observable("Modif");
	/*if(self.userInfo.myFiles.length==0)
	{
		self.isFiles=ko.observable(false);
	}
	else{
		self.isFiles=ko.observable(true);
	}*/
//	if(userInfo.id=="myId"){
		self.userTitle("My profile");
		self.titleFiles("My files");
		self.myPage(true);
	/*}
	else{
		self.userTitle("Profil of "+userInfo.Firstname);
		self.titleFiles("Files shared with "+userInfo.Firstname);
	}*/
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
		showElem($("#revokeAccount"));
	};
	self.sendPublicKey=function(){
		alert("send public key");
	};
	self.saveModif=function(){
		alert("save modif");
	};
	self.logout=function(){
		sCredentialsManager.logout();
	};
};