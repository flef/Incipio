<?php
	$pseudo=$_POST['pseudo'];
	$password=$_POST['password'];
	$serveur_LDAP = $_POST['serveur_LDAP'];
	$serveur_port = $_POST['serveur_port'];

	echo "serveur_LDAP :".$serveur_LDAP." : ".$serveur_port."<br/>";
	echo "pseudo :".$pseudo."<br/>";
	//echo "password :".$password."<br/>";

	$connexion = @ldap_connect($serveur_LDAP, $serveur_port);
	
	if(!$connexion)
		echo "connexion serveur <b>échouée</b> ".$serveur_LDAP."<br/>";
	else
	{
		echo "connexion serveur effectuée ".$serveur_LDAP."<br/>";
		/*if(!@ldap_set_option($connexion, LDAP_OPT_PROTOCOL_VERSION, 3)) //version 3 du ldap
			echo "configuration de la connexion serveur effectuée<br/>";
		else
			echo "configuration de la connexion serveur <b>échouée</b><br/>";*/

	}
		
	echo "<br />";
		
	$recherche = 'uid=' . $pseudo . ', ou=people, dc=emse,dc=fr';
	$bind_serveur_LDAP = @ldap_bind($connexion,$recherche,$password);
	
	if (!$bind_serveur_LDAP)
		echo "identification <b>échouée</b> ".$pseudo." :".$bind_serveur_LDAP."<br/>";
	else
		echo "identification effectuée ".$pseudo."<br/>";

	echo "<br />";
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>test ldap</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <p>
    <label for="serveur_LDAP">serveur_LDAP</label>
    <input name="serveur_LDAP" type="text" id="serveur_LDAP" value="chimere.emse.fr" /><br />
    <label for="serveur_port">serveur_port</label>
    <input name="serveur_port" type="text" id="serveur_port" value="389" /><br />
    <label for="pseudo">pseudo</label>
    <input type="text" name="pseudo" id="pseudo" /><br />
    <label for="password">password</label>
    <input type="text" name="password" id="password" />
  </p>
  <p>
    <input type="submit" name="button" id="button" value="Envoyer">
  </p>
</form>
</body>
</html>
