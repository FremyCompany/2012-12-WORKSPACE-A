function FileAPI  (f,addFileVModel,uploadInfo) {

	var file = f[0],
	Sign = f[1],
	Key	= f[2],
	KeySign = f[3],
	AddFileVModel=addFileVModel,
	UploadInfo=uploadInfo,
	showComplete=true;
	fileQueue = new Array();


	this.init = function () {
		file.onchange = this.addFiles;
		Sign.onchange = this.addFiles;
		Key.onchange = this.addFiles;
		KeySign.onchange = this.addFiles;

	};

	this.addFiles = function () {
		addFileListItems(this.files);
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
				uploadFile(item.file);
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
	var addFileListItems = function (files) {
		for (var i = 0; i < files.length; i++) {
			var fr = new FileReader();
			fr.file = files[i];
			fr.onloadend = showFileInList;
			fr.readAsDataURL(files[i]);
		}
	};

	var showFileInList = function (ev) {
		var file = ev.target.file;
		if (file) {
			fileQueue.push({
				file : file,
			});
		}
	};


	var uploadFile = function (file) {
		if (file) {
			var xhr = new XMLHttpRequest(),
			upload = xhr.upload;
			upload.addEventListener("progress", function (ev) {
			}, false);
			upload.addEventListener("load", function (ev) {
			}, false);
			upload.addEventListener("error", function (ev) {console.log(ev);}, false);
			xhr.open(
					"POST",
					"PHP/upload.php"
			);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.setRequestHeader("X-File-Name", file.name);
			xhr.send(file);
		}
	};

}