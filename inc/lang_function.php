<?php



if(!isset($_COOKIE['lang']))
{
	setcookie('lang', 'Ar', time() + (86400 * 30), "/");
}
if(isset($_GET['lang']) && !empty($_GET['lang'])){

	if($_GET['lang'] == 'Ar')
	{
		setcookie('lang', 'Ar', time() + (86400 * 30), "/");

	}
	else if($_GET['lang'] == 'en')
	{
		setcookie('lang', 'En', time() + (86400 * 30), "/");
	}

}

$url=mysqli_real_escape_string($conn,$_GET['url']);


header('location:'.$url);
?>