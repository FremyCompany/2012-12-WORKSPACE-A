function REVOKE(){
	self=this;
	self.close=function(){
		hideElem($("#revokeAccount"))
	};
	self.revoke=function(){
		alert("revoke");
	};
}