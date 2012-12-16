<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ROOT/FUNCTIONS/initializeV4.php");
?>
<html>
<?php
global $db;

$str="SELECT id FROM arch_sond WHERE cached='0' ORDER BY datum ASC LIMIT 0,1";
$req=$db->prepare($str);
$req->execute(array());
$donnee=$req->fetch();
if($donnee['id']<50)
{
$str="UPDATE arch_sond set cached='1' WHERE id=?";
$req=$db->prepare($str);
$req->execute(array($donnee['id']));
?>
<script>
	var id=<?php echo $donnee['id']; ?>;
	window.location.href="/?page=poll&sond_id="+id;
</script>
<?php
}
?>
</html>