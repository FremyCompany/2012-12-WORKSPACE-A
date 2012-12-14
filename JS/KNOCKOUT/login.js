var model = new LOGIN();
ko.applyBindings(model, document.getElementById('welcome'));

function LOGIN(specialMessage) {
	var self=this;
	self.inputEmail=ko.observable("");
	self.inputPassword=ko.observable("");
	self.inputConfirmPassword=ko.observable("");
	self.inputName=ko.observable("");
	self.loading=ko.observable(false);
	self.signin=ko.observable(true);
	self.signup=ko.observable(false);
	self.connect=function(){
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
