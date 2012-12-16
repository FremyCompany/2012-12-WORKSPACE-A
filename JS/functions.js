
function showElem(elem){
	$("body").css('overflow','hidden');
	elem.css('display','block');
	elem.find('.content').css('position','relative').css('top','-1000px').animate({top: '0px'},200);
}
function hideElem(elem){
	$("body").css('overflow','scroll');
	elem.find('.content').css('position','relative').css('top','0px').animate({top: '-1000px'},500,function(){
	});
	elem.fadeOut(500);
}
function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}
function FileSave  (f,addFileVModel,uploadInfo,user_info) {

	var File = f[0],
	Sign = f[1],
	Key	= f[2],
	KeySign = f[3],
	AddFileVModel=addFileVModel,
	UploadInfo=uploadInfo,
	showComplete=true,
	//user with whom we share
	User_login=user_info,
	FileName=null,
	fileQueue = new Array();


	this.init = function () {
		//type of the file
		if(File!=null){
			File.name="File";
			File.onchange = this.addFile;
		}
		if(Sign!=null){
			Sign.name="FileSign";
			Sign.onchange = this.addSign;
		}
		if(Key!=null){
			Key.name="Key";
			Key.onchange = this.addKey;
		}
		if(KeySign!=null){
			KeySign.name="KeySign";
			KeySign.onchange = this.addKeySign;
		}
	};

	this.addFile = function () {
		addFileListItems(this.files,File);
		File.has=true;
	};
	this.addSign = function () {
		addFileListItems(this.files,Sign);
		Sign.has=true;
	};
	this.addKey = function () {
		alert("add key");
		addFileListItems(this.files,Key);
		Key.has=true;
	};
	this.addKeySign = function () {
		addFileListItems(this.files,KeySign);
		alert("add KeySign")
		KeySign.has=true;
	};
	this.checkHasAll=function(){
		if(User_login==null)
		{
			//New file
			if(KeySign.has==true && Key.has==true && Sign.has==true && File.has==true)
				return true;
		}
		else{
			//New share
			if(KeySign.has==true && Key.has==true)
			{
				return true;
			}
		}
		return false;

	};

	this.uploadQueue = function (ev) {
		if(FileSave.checkHasAll())
		{
			ev.preventDefault();
			AddFileVModel.loadingFile();
			var txtComplete=document.getElementById("uploadComplete");
			if(txtComplete!=null)
				UploadInfo.removeChild(txtComplete);
			var txt=document.getElementById("loader");
			var p = document.createElement("p");
			if(showComplete){
				if(txt!=null)
					UploadInfo.removeChild(txt);
				var p = document.createElement("p");
				p.id = "loader";
				var pText = document.createTextNode("Uploading...");
				p.appendChild(pText);
				UploadInfo.appendChild(p);
			}
			while (fileQueue.length > 0) {
				var item = fileQueue.pop();
				if (item.file.size < 10485760) {
					uploadFile(item.file,item.item);
					showComplete=true;
				} else {
					p.textContent = "One file is to large (max : 10 mo) ";
					p.style["color"] = "red";
					showComplete=false;
				}
			}
			AddFileVModel.loadingFile();
			if(showComplete){
				UploadInfo.removeChild(p);
				p = document.createElement("p");
				p.id = "uploadComplete";
				var pText = document.createTextNode("Upload Complete");
				p.appendChild(pText);
				UploadInfo.appendChild(p);
			}
		}
		else
		{
			alert("Give all the files ! ");
		}
	};
	var addFileListItems = function (files,item) {
		for (var i = 0; i < files.length; i++) {
			var fr = new FileReader();
			fr.file = files[i];
			fr.item = item;
			if(item.name=="File"){
				FileName=fr.file.name;
			}
			fr.onloadend = showFileInList;
			fr.readAsDataURL(files[i]);
		}
	};

	var showFileInList = function (ev) {
		var file = ev.target.file;
		var item = ev.target.item;
		var elemToDelete=null;
		for(var i=0;i<fileQueue.length;i++)
		{
			if(fileQueue[i].item==item)
			{
				//item already put
				elemToDelete=fileQueue[i];
			}
		}
		if(elemToDelete!=null){
			fileQueue.remove(elemToDelete);
		}
		if (file) {
			fileQueue.push({
				file : file,
				item : item
			});
		}
	};


	var uploadFile = function (file,item) {
		if (file && item) {
			var xhr = new XMLHttpRequest(),
			upload = xhr.upload;
			upload.addEventListener("progress", function (ev) {
				//...
			}, false);
			upload.addEventListener("load", function (ev) {
				//...
			}, false);
			upload.addEventListener("error", function (ev) {console.log(ev);}, false);
			xhr.open(
					"POST",
					"PHP/upload.php"
			);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("X-File-Name", file.name);
			xhr.setRequestHeader("X-File-type", item.name);
			xhr.setRequestHeader("X-File",FileName);
			if(User_login!=null){
				xhr.setRequestHeader("X_File_user",User_login+"_2");
			}
			//alert(file.name+" "+item.name+" "+FileName);
			xhr.send(file);
		}
	};

}