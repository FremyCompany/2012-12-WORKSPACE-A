function REVOKE(){
	self=this;
	self.inputPassword=ko.observable("");
	self.showMessage=ko.observable(false);
	self.close=function(){
		hideElem($("#revokeAccount"));
	};
	self.revoke=function(){
		if(self.inputPassword()!="")
		{
			console.log(SHA_ENCODE(self.inputPassword()));
			Secloud.unregister(SHA_ENCODE(self.inputPassword()),function(ok,rep){
				if(!ok) { Dialogs.showMessage('Une erreur est survenue lors de la revocation.','Erreur'); throw new Error([].join.call(arguments,"\n")); }	
				if(rep){
					self.showMessage(false);
					sCredentialsManager.logout();
				}
				else{
					self.showMessage(true);
				}
			});
		}
		else
		{
			self.showMessage(true);
		}
	};
}