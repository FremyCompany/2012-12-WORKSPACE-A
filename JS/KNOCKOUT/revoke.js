function REVOKE(){
	self=this;
	self.inputPassword=ko.observable("");
	self.close=function(){
		hideElem($("#revokeAccount"))
	};
	self.revoke=function(){
		alert("revoke");
	};
}