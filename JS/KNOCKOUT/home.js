//GET ALL USERS
var users=new Array();
for(var i=0;i<20;i++){
	users[i]={ "firstname" : "Nicolas", "lastname" : "Bernier", "address" : "11, rue Goffart", "url" : "?page=userPage" };
}

var model = new HOME(users);
ko.applyBindings(model, document.getElementById('home'));
$.ajax({
	url : "/TEMPLATE/HTML/addFile.html",
	cache : false
}).done(function(html) {
	$("#addFile").html(html);
	var modelAddFile=new ADDFILE();
	ko.applyBindings(modelAddFile, document.getElementById('addFile'));
});

function HOME() {
	var self=this;
	self.users=users;
	self.addFile=function(){
		showElem($("#addFile"));
	};
}