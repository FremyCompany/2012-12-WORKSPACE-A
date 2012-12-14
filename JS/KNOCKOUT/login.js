var model = new LOGIN();
ko.applyBindings(model, document.getElementById('welcome'));

function LOGIN(specialMessage) {
	var self=this;
	//
	//Sign in
	//
	self.inputEmail=ko.observable("");
	self.inputPassword=ko.observable("");
	
	//
	//Sign up
	//
	self.inputFirstName=ko.observable("");
	self.inputLastName=ko.observable("");
	self.inputAddress=ko.observable("");
	self.inputPhone=ko.observable("");
	self.inputRegister=ko.observable("");
	self.inputPassphrase=ko.observable("");

	self.showSuccess=ko.observable(false);
	self.showMessage=ko.observable(false);
	self.message=ko.observable("");
	self.loading=ko.observable(false);
	self.signin=ko.observable(true);
	self.signup=ko.observable(false);
	self.connect=function(){
		Actions.verifConnection(self.inputEmail(),self.inputPassword(),function(ok,rep){
			if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
			self.loading(false);
			if(rep){
				//ok
				alert("next page");
			}
			else{
				self.showMessage(true);
				self.message("Wrong email or password");
			}
		});
		self.loading(true);
	};
	self.createAccount=function(){
		self.signin(false);
		self.signup(true);
		self.showMessage(false);
	};
	self.newAccount=function(){
		if(self.inputPassphrase()=="" || self.inputRegister()=="" || self.inputPhone()=="" || self.inputAddress()=="" || self.inputLastName()=="" || self.inputFirstName()=="" || self.inputEmail()=="")
		{
			self.showMessage(true);
			self.message("Fill all information !!");
		}
		else{
			Actions.verifInformation(self.inputPassphrase(),self.inputRegister(),self.inputPhone(),self.inputAddress(),self.inputLastName(),self.inputFirstName(),self.inputEmail(),function(ok,rep){
				if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
				self.loading(false);
				if(!rep){
					self.showMessage(true);
					self.message("Some information are wrong !");
				}
				else{
					self.showSuccess(true);
				}
			});
			self.showMessage(false);
			self.loading(true);
			alert("create");
		}
	};
	self.returnSignIn=function(){
		self.showMessage(false);
		self.signup(false);
		self.signin(true);
	};
};
