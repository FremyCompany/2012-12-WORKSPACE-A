var model = new LOGIN();
ko.applyBindings(model, document.getElementById('welcome'));

window.addEventListener('DOMContentLoaded', function() {
	if(sCredentials && sCredentials.login) {
		window.location.href="?page=home";
	}
});

function LOGIN() {
	var self=this;
	//
	//Sign in
	//
	self.inputLogin=ko.observable("");
	self.inputPassword=ko.observable("");

	//
	//Sign up
	//
	self.inputEmail=ko.observable("");
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
		if(self.inputLogin()=="" || self.inputPassword()==""){
			self.showMessage(true);
			self.message("Fill all fields to login");
		}
		else{
			
			// too see the result
			watchCustomEvent('connectedUserChanged',function(){
				if(sCredentials && sCredentials.login) {
					window.location.href="?page=home";
				}
				else{
					self.loading(false);
					self.showMessage(true);
					self.message("Wrong password or login");
				}
			});
			// start login
			sCredentialsManager.login(self.inputLogin(),SHA512(self.inputPassword()))
			self.loading(true);
			
		}
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
			var info={ "mail" : self.inputEmail(),
					"firstName" : self.inputFirstName(), 
					"lastName" : self.inputLastName(),
					"address" : self.inputAddress(),
					"phone" : self.inputPhone(),
					"idNumber" : self.inputRegister
			};
			Secloud.register(info,self.inputPassphrase(),function(ok,rep){
				if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }
				self.loading(false);
				if(!rep){
					self.showMessage(true);
					self.message("Some information are wrong or the login is already use !");
				}
				else{
					self.showSuccess(true);
				}
			});
			self.showMessage(false);
			self.loading(true);
		}
	};
	self.returnSignIn=function(){
		self.showMessage(false);
		self.signup(false);
		self.signin(true);
	};
};
