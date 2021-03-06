//GET ALL USERS
var users=new Array();
for(var i=0;i<20;i++){
	users[i]={ "firstname" : "Nicolas", "lastname" : "Bernier", "address" : "11, rue Goffart", "url" : "?page=userPage" };
}
var model = new HOME();
ko.applyBindings(model, document.getElementById('home'));
Secloud.getAllUsersInfo(function(ok,users){
	if(!ok) { Dialogs.showMessage('Une erreur est survenue lors du téléchargement de luser.','Erreur'); throw new Error([].join.call(arguments,"\n")); }	
	model.setUser(users);
});

function HOME() {
	var self=this;
	self.users=ko.observableArray();
	self.setUser=function(users){
		for(var i=0;i<users.length;i++){
			self.users.push(users[i]);
		}
	};
	self.addFile=function(){
		$.ajax({
			url : "/TEMPLATE/HTML/addFile.html",
			cache : false
		}).done(function(html) {
			$("#addFile").html(html);
			var modelAddFile=new ADDFILE(true);
			ko.applyBindings(modelAddFile, document.getElementById('addFile'));
			if (typeof FileReader == "undefined") alert ("Sorry your browser does not support the File API and this demo will not work for you");
			fileSave = new FileSave(
					[document.getElementById("File"),
					 document.getElementById("Sign"),
					 document.getElementById("Key"),
					 document.getElementById("KeySign")],
					 modelAddFile,
					 null
			);
			fileSave.init();
			var upload = document.getElementById("upload");
			upload.onclick = fileSave.uploadQueue;
			showElem($("#addFile"));
		});
	};
}