<?php
 
// INCLUDES
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");
 
require_once('./crypto.php');
 
// SERVICE
$jsonService = "Secloud"; class Secloud {
 
//
// CONNECTION
//
 
 //
 // Connect to the webservice
 //
 public static function connect($login, $proof) {
  
  // if an user is already connected, disconnect first
  if(isset($_SESSION['login'])) {
   if($login==$_SESSION['login']) {
    return Secloud::getMyUserInfo();
   } else {
    Secloud::disconnect();
   }
  }
  
  // convert argument into valid data
  $login = pathEncode($login);
  
  // check that the user exists
  if(!is_dir(USERS_FOLDER.$login)) { cThrow(ERR_NOT_FOUND); }
  
  // check that the proof is valid
  if(checkProof($login, session_id(), $proof)) {
   
   // connect the user
   $_SESSION['login'] = $login;
   
   // return user data
   return Secloud::getMyUserInfo();
   
  } else {
  
   // bad infos
   cThrow(ERR_BADINFOS);
   
  }
  
 }
 
 //
 // Disconnect from the webservice
 //
 public static function disconnect() {
  
  // clean
  unset($_SESSION['login']); unset($_SESSION['user']);
  
  // delete session and recreate one (just to be sure)
  session_destroy(); session_start();
  
  // ok
  return null;
 }
 
 //
 // Register you on the website
 //
 public static function register($data, $pass) {
  
  // create a login
  $login = pathEncode($data['firstName'].' '.$data['idNumber']);
  
  // check that the login don't exist already
  if(is_dir(USERS_FOLDER.$login)) { cThrow(ERR_ARGS,'Login déjà utilisé');return false; }
  
  // create the user folders
  mkdir(USERS_FOLDER.$login);
        mkdir(USERS_FOLDER.$login.'/user');
        mkdir(USERS_FOLDER.$login.'/files');
        mkdir(USERS_FOLDER.$login.'/'.$login);
  
  // create a password
  file_put_contents('temp-pass.txt',$pass);
  
  // TODO: check data validity
  //...
  
  // now, save data
  foreach($data as $key=>$value) {
   file_put_contents(USERS_FOLDER.$login.'/user/'.pathEncode($key).'.txt',$value);
  }
  
  // return ok
  return true;
  
 }
 
 //
 // Revoke your access on the website
 //
 public static function unregister($pass) {
  
  // check that you're connected
  if(!isConnected()) { cThrow(ERR_RIGHTS); }
  
  // create the user folder
  if($pass)
  deleteFile(USERS_FOLDER.$_SESSION['login']);
  
  // return ok
  return true;
  
 }
 

//
// USERS
//
 
 //
 // Returns a list of all the registered users of the service
 //
 public static function getAllUsers() {
 
  // check that you're connected
  if(!isConnected()) { cThrow(ERR_RIGHTS); }
 
  // initialize data
  $result = array(); $dirpath=USERS_FOLDER;
  $dir = opendir($dirpath);
  
  // walk the USERS folder
  while (($file = readdir($dir)) !== false) {
  
   // don't consider UNIX virtual paths
   if($file=="."||$file=="..") { continue; }
   
   // check that we only give access to folders
   if (is_dir($dirpath.$file)) {
    
    // check that the user has been successfully registered
    if(file_exists($dirpath.$file.'/public.key')) {
    
     // append the file to the results
     $result[] = array(pathDecode($file));
     
    }
    
   }
   
   
  }
  
  return $result;
 
 }
 
 
 //
 // Returns more information about an user
 //
 public static function getUserInfo($login) {
   
  // check that you're connected
  if(!isConnected()) { cThrow(ERR_RIGHTS); }
  
  // convert argument into valid data
  $login = pathEncode($login);
  
  // check that the user exists
  if(!is_dir(USERS_FOLDER.$login)) { cThrow(ERR_NOT_FOUND); }
  
  // craft the user info
  return array(
   "firstName" => file_get_contents(USERS_FOLDER.$login.'/user/firstName.txt'),
   "lastName" => file_get_contents(USERS_FOLDER.$login.'/user/lastName.txt'),
   "mail" => file_get_contents(USERS_FOLDER.$login.'/user/mail.txt')
  		
  );
  
 }
 
 //
 // Returns more information about the connected user
 //
 public static function getMyUserInfo() {
   
  // check that you're connected
  if(!isConnected()) { return null; }
  
  if(!isset($_SESSION['user'])) {
   
   // convert argument into valid data
   $login = $_SESSION['login'];
   
   // check that the user exists
   if(!is_dir(USERS_FOLDER.$login)) { cThrow(ERR_NOT_FOUND); }
   
   // craft the user info
   $_SESSION['user'] = array(
    "login"  => pathDecode($_SESSION['login']),
    "firstName" => file_get_contents(USERS_FOLDER.$login.'/user/firstName.txt'),
    "lastName" => file_get_contents(USERS_FOLDER.$login.'/user/lastName.txt'),
    "mail"  => file_get_contents(USERS_FOLDER.$login.'/user/mail.txt'),
    "phone"  => file_get_contents(USERS_FOLDER.$login.'/user/phone.txt'),
    "address" => file_get_contents(USERS_FOLDER.$login.'/user/address.txt'),
    "idNumber" => file_get_contents(USERS_FOLDER.$login.'/user/idNumber.txt'),
   );
   
  }
  
  return $_SESSION['user'];
  
 }
 
 //
 // Modify more information about the connected user
 //
 public static function setMyUserInfo($data) {
   
  // check that you're connected
  if(!isConnected()) { cThrow(ERR_RIGHTS); }
  $user = Secloud::
  
  // convert argument into valid data
  $login = pathEncode($login);
  
  // check that the user exists
  if(!is_dir(USERS_FOLDER.$login)) { cThrow(ERR_NOT_FOUND); }
  
  // craft the user info
  foreach($data as $key=>$value) {
   file_put_contents(USERS_FOLDER.$login.'/user/'.pathEncode($key).'.txt',$value);
  }
  
  // return ok
  return true;
  
 }
 
//
// FILES
//
 public static function getMyFiles(){
 	return Secloud::getFilesFor($_SESSION["login"]);
 }
 //
 // Returns a list of all the files you have access from a user
 //
 public static function getFilesFor($login) {
 
  // check that you're connected
  if(!isConnected()) { cThrow(ERR_RIGHTS); }
      
  // convert argument into valid data
  $login = pathEncode($login);
  
  // check that the user exists
  if(!is_dir(USERS_FOLDER.$login)) { cThrow(ERR_NOT_FOUND); }
  $result = array();
  // check that data exists for you by this user
  $dirpath=USERS_FOLDER.$login.'/'.$_SESSION['login'];
  if(!is_dir($dirpath)) { return $result; } 
  
  // initialize data
  $result = array(); 
  $dir = opendir($dirpath);
  
  // walk the key folder
  while (($file = readdir($dir)) !== false) {
  
   // don't consider UNIX virtual paths
   if($file=="."||$file=="..") { continue; }
   
   // check that we only give access to folders
   if (is_dir($dirpath."/".$file)) {
    
    // check that the linked data files still exist (and are signed)
    /*if(
                    file_exists($dirpath.$file.'/.data')
                    && file_exists($dirpath.$file.'/.hmac')
                    && file_exists($dirpath.'/../files/'.$file.'/.data')
                    && file_exists($dirpath.'/../files/'.$file.'/.hmac')
                ) {*/
    
     // append the file to the results
     $result[] = array("name"=>pathDecode($file));
     
    //}
    
   }
   
   
  }
  
  return $result;
 
 }
 
 //
 // Returns a list of all the files you have access from a user
 //
 public static function getPublicKeyOf($login) {
 
  // check that you're connected
  if(!isConnected()) { cThrow(ERR_RIGHTS); }
      
  // convert argument into valid data
  $login = pathEncode($login);
  
  // check that the user exists
  if(!is_dir(USERS_FOLDER.$login)) { cThrow(ERR_NOT_FOUND); }
  
  // check that the file exists
  if(!file_exists(USERS_FOLDER.$login.'/public.key')) { cThrow(ERR_NOT_FOUND); }
  
  // return its content
  return base64_encode(file_get_contents(USERS_FOLDER.$login.'/public.key'));
  
 }
 
}
 
// IF NOT INCLUDE
if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) {
 
 // ACTIVATE WEBSERVICE
 include($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/json.php');
 if (false) { ?><script><?php }
 ?>
/* Module used to load informations about the connected user */
(function() {
    
 function updateData(ok, data, errMessage) {
  try {
   if(ok) {
    if((!window.sCredentials) || (!data) || (data.login!=window.sCredentials.login)) {
     window.sCredentials = data; lastId=null; lastPassword=null;
     sessionStorage.setItem("sCredentials",JSON.stringify(data));
     raiseCustomEvent('connectedUserChanged',{});
    }
   } else {
    switch(data) {
     case 1102: // Licence
      Dialogs.showMessage("Ceci est votre première connexion sur le site web. Vous devez accepter la licence avant de continuer. Vous allez être redirigé vers le site ShareSpace après fermeture de cette boite de dialogue.","Première connexion",function(){
       location.href = "http://sharespace.saint-boni.be";
      }); 
      return;
     case 1105: // Messages
      var message = errMessage.split("|") // [id, "nom", "date", "sujet", "message"]
      message[0] = message[0].split(";")[1];
      Dialogs.open('Message important', [
       {type:'x-text',value:'Veuillez lire ce message important avant de continuer:'},
       //{type:'x-html',value:'<small>De: '+unescape(message[1])+'<br />Le: '+unescape(message[2])+'<br />SUJET: '+unescape(message[3])+'</small>'},
       //{type:'x-text',value:'Le: '+unescape(message[2])},
       //{type:'x-text',value:'SUJET: '+unescape(message[3])},
       {type:'x-html',value:'<div style="padding: 5px; background-color: rgb(200,200,200); overflow: auto; max-height: 250px;"><small style="color: white;">'+unescape(message[1])+', le '+unescape(message[2])+'<br /><b>Concerne: '+unescape(message[3])+'</b></small><div style="margin-top: 5px; max-width: 460px; text-align: justify;">'+unescape(message[4])+'</div></div>'}
      ],[
       {type:'button',value:'Annuler',onclick:function(){Dialogs.close();}},
       {type:'button',value:'Continuer',onclick:function(){Secloud.connect(lastId,SHA_ENCODE(lastPassword),parseInt(message[0]),updateData); Dialogs.close();}}
      ]); 
      return;
     case 1104:
      // special check for session revival
      if(cookies.getItem("PHPSESSID")!=lastSessID) { 
       login(lastId,lastPassword);
       return;
      }
     default:
      Dialogs.showMessage(errMessage+" (Erreur "+data+")", "Connexion impossible"); 
      return;
    }
   }
  } catch (ex) {}
 }
 
 function fetchData() {
        Secloud.getMyUserInfo(updateData);
    }
 
 var lastId, lastPassword, lastSessID;
 function login(id, password) {
  lastId=id; lastPassword=password; lastSessID=cookies.getItem("PHPSESSID");
  Secloud.connect(lastId,SHA_ENCODE(lastPassword),updateData);
 }
 
 function logout() {
  lastId=null; lastPassword=null;
  Secloud.disconnect();
  updateData(true,window.sCredentialsManager.defaultValue);
 }
 
 window.sCredentialsManager = {
  update: fetchData,  // ()
  login:  login,      // (id,password)
  logout: logout,     // ()
  defaultValue: null
 };
 window.sCredentials = window.sCredentialsManager.defaultValue;
 
 // check that the user has access to this page
 function checkShowLogin(){
     if((window.location.pathname!="/" || window.location.search!="") && (!sCredentials || !sCredentials.login)) {
         window.location.href="/";
  }
    };
 
    watchCustomEvent('connectedUserChanged',checkShowLogin);
 
    // if cache is empty
    if(!sessionStorage.getItem("sCredentials")) {
 
        // fetch from server using XMLHTTPRequest
  fetchData();
  
    } else {
 
        // else, read data from cache (which takes less time)
        try {
   window.sCredentials = JSON.parse(sessionStorage.getItem("sCredentials")); 
   raiseCustomEvent('connectedUserChanged',{});
   if(!cookies.getItem("PHPSESSID")) { fetchData(); }
  } catch (ex) {
   fetchData();
  }
  
    }
    
 // Regulary update connection status (keep session on server)
    setInterval(fetchData, 30000);
    
})();<?php
 
}
 

?>