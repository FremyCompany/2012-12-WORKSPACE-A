var user=getURLParameter("user");
var modelDownload=null;
$.ajax({
	url : "/TEMPLATE/HTML/downloadFiles.html",
	cache : false
}).done(function(html) {
	$("#downloadFile").html(html);
	modelDownload=new DOWNLOADFILES();
	ko.applyBindings(modelDownload, document.getElementById('downloadFile'));
	var model = new USERPAGE(modelDownload);
	ko.applyBindings(model, document.getElementById('home'));

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
			$.ajax({
				url : "/TEMPLATE/HTML/shareFile.html",
				cache : false
			}).done(function(html) {
				Secloud.getFilesFor(user,function(ok,myfiles){
					if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
					rep.login=user;
					model.init(rep,false,myfiles);
				});
				Secloud.getMyFiles(function(ok,files){
					if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
					$("#myFiles").html(html);
					$.ajax({
						url : "/TEMPLATE/HTML/addFile.html",
						cache : false
					}).done(function(html) {
						$("#addFile").html(html);
						var modelAddFile=new ADDFILE(false);
						modelAddFile.setTitle("Share a file")
						ko.applyBindings(modelAddFile, document.getElementById('addFile'));
						if (typeof FileReader == "undefined") alert ("Sorry your browser does not support the File API and this demo will not work for you");
						fileSave = new FileSave(
								[null,
								 null,
								 document.getElementById("Key"),
								 document.getElementById("KeySign")],
								 modelAddFile,
								 user
						);
						fileSave.init();
						var modelMyFiles=new FILE_TO_SHARE(files,fileSave);
						ko.applyBindings(modelMyFiles, document.getElementById('myFiles'));
					});
				});
			});
		});
	}
});
function USERPAGE(modelDownload) {
	var self=this;
	self.modelDownload=modelDownload;

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
		console.log(userInfo);
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
			self.titleFiles("Files that "+userInfo.firstName+" shares with you");
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
		showElem($("#myFiles"));
	};
	self.download=function(data){
		console.log(data);
		modelDownload.setFile(data.name,self.userInfo.login);
		showElem($("#downloadFile"));
	};
	self.saveModif=function(){
		var info={
				"login" : self.userInfo.login,
				"firstName" : self.inputFirstName(),
				"lastName" : self.inputLastName(),
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
	self.downloadKey=function() {
		window.open("/PHP/download-key.php?user="+escape(self.userInfo.login));
	};
};