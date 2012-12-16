function FileSave  (f,addFileVModel,uploadInfo,user_info) {

	var file = f[0],
	Sign = f[1],
	Key	= f[2],
	KeySign = f[3],
	AddFileVModel=addFileVModel,
	UploadInfo=uploadInfo,
	showComplete=true,
	//user with whom we share
	user_info=user_info,
	fileQueue = new Array();


	this.init = function () {
		//type of the file
		file.name="File";
		Sign.name="FileSign";
		Key.name="Key";
		KeySign.name="KeySign";
		file.onchange = this.addFile;
		Sign.onchange = this.addSign;
		Key.onchange = this.addKey;
		KeySign.onchange = this.addKeySign;

	};

	this.addFile = function () {
		addFileListItems(this.files,file);
	};
	this.addSign = function () {
		addFileListItems(this.files,Sign);
	};
	this.addKey = function () {

		addFileListItems(this.files,Key);
	};
	this.addKeySign = function () {
		addFileListItems(this.files,KeySign);
	};


	this.uploadQueue = function (ev) {
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
	};
	var addFileListItems = function (files,item) {
		for (var i = 0; i < files.length; i++) {
			var fr = new FileReader();
			fr.file = files[i];
			fr.item = item;
			fr.onloadend = showFileInList;
			fr.readAsDataURL(files[i]);
		}
	};

	var showFileInList = function (ev) {
		var file = ev.target.file;
		var item = ev.target.item;
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
			xhr.send(file);
		}
	};

}