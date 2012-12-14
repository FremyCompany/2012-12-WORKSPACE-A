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

	
	self.loading=ko.observable(false);
	self.signin=ko.observable(true);
	self.signup=ko.observable(false);
	self.connect=function(){
		Actions.verifConnection()
		self.loading(true);
	};
	self.createAccount=function(){
		self.signin(false);
		self.signup(true);
	};
	self.newAccount=function(){
		alert("create");
	};
	self.returnSignIn=function(){
		self.signup(false);
		self.signin(true);
	};
};
