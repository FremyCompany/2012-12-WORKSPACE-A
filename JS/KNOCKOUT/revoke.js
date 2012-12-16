function REVOKE(){
	self=this;
	self.inputPassword=ko.observable("");
	self.close=function(){
		hideElem($("#revokeAccount"));
	};
	self.revoke=function(){
		if(self.inputPassword()!="")
		{
			Secloud.unregister(self.inputPassword(),function(ok,rep){
				sCredentialsManager.logout();
			});
		}
		console.log("wrong password");
	};
}