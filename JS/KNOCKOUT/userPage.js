var model = new USERPAGE();
ko.applyBindings(model, document.getElementById('home'));

var user=getURLParameter("user");
if(user==null){
	//myPage
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
		Secloud.getMyFiles(function(ok,files){
			if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
			model.init(rep,true,files);
		});
	});
}
else{
	Secloud.getUserInfo(user,function(ok,rep){
		if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }	
			model.init(rep,false);
	});
}

function USERPAGE() {
	var self=this;
	
	
	self.loading=ko.observable(true);
	self.inputFirstName=ko.observable("");
	self.inputLastName=ko.observable("");
	self.inputMail= ko.observable("");
	self.inputAddress= ko.observable("");
	self.inputPhone= ko.observable("");
	
	self.userTitle=ko.observable("");
	self.titleFiles=ko.observable("");
	self.myPage=ko.observable(false);
	self.modif=ko.observable(false);
	self.textButton=ko.observable("Modif my profile");
	self.userInfo="";
	self.init=function(userInfo,isMe,files){
		self.userInfo=userInfo;
		self.files=files;
		self.inputFirstName(self.userInfo.firstName);
		self.inputLastName(self.userInfo.lastName);
		self.inputMail(self.userInfo.mail);
		self.inputAddress(self.userInfo.address);
		self.inputPhone(self.userInfo.phone);

		if(self.files!=undefined)
		{
			if(self.files.length==0)
			{
				self.isFiles=ko.observable(false);
			}
			else{
				self.isFiles=ko.observable(true);
			}
		}else{
			self.isFiles=ko.observable(false);
		}
		if(isMe){
			self.userTitle("My profile");
			self.titleFiles("My files");
			self.myPage(true);
		}
		else{
			self.userTitle("Profil of "+userInfo.firstName);
			self.titleFiles("Files shared with "+userInfo.firstName);
		}
		self.modifInfo=function(){
			if(self.modif()==false){
				self.textButton("Cancel");
				self.modif(true);
			}
			else{
				self.modif(false);
				self.textButton("Modif my profile");
			}
		};
		self.loading(false);
	};
	self.revocation=function(){
		showElem($("#revokeAccount"));
	};
	self.shareFiles=function(){
		alert("share");
	};
	self.saveModif=function(){
		alert("save modif");
		var info={
				"id" : self.userInfo.id,
				"firstName" : self.inputFirstName(),
				"lastLName" : self.inputLastName(),
				"mail" : self.inputMail(),
				"address" : self.inputAddress(),
				"phone" : self.inputPhone(),
		};
		self.userInfo=info;
		self.modif(false);
		self.textButton("Modif my profile");
		Secloud.setMyUserInfo(info,function(ok,rep){

		});
	};
	self.logout=function(){
		sCredentialsManager.logout();
	};
};