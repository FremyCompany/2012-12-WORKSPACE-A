<?php

	// ...<1000 : erreurs spécifiques à une page
	const ERR_NOTFOUND = 404;
	const ERR_404 = "Une resource n'a pas été trouvée";
	
	const ERR_UNKNOWN = 500;
	const ERR_500 = "Erreur générique";
	
	const ERR_PHP = 666;
	const ERR_666 = "Erreur native PHP";

	// 10xx : erreurs communes
	const ERR_ARGS = 1000;
	const ERR_1000 = "Arguments invalides";
	
	const ERR_INVALID = 1001;
	const ERR_1001 = "Action invalide";
	
	const ERR_UNABLE = 1002;
	const ERR_1002 = "Impossible d'effectuer l'action";
	
	const ERR_DISABLED = 1003;
	const ERR_1003 = "Cette action a été désactivée";
	
	const ERR_SQL = 1004;
	const ERR_1004 = "La base de donnée n'a pas fourni le résultat attendu";
	
	// 11xx : erreurs de connexion
	const ERR_SHARESPACEDISABLED = 1100; // Toute connexion est bloqu�e, SAUF si on dispose du cookie admin
	const ERR_1100 = "ShareSpace a été désactivé par un administrateur";
	
	const ERR_RPXCONNEXION = 1101;
	const ERR_1101 = "Une erreur de connexion au service RPX est survenue. Essayez de vous connecter normalement";
	
	const ERR_LICENCE = 1102;
	const ERR_1102 = "Veillez accepter la licence avant de continuer";
	
	const ERR_BLOCKED = 1103;
	const ERR_1103 = "Votre compte a été désactivé par un administrateur";
	
	const ERR_BADINFOS = 1104;
	const ERR_1104 = "L'identifiant ou le mot de passe est incorrect";
	
	const ERR_MESSAGETOREAD = 1105;
	const ERR_1105 = "Veuillez lire ce message important avant de continuer";
	
	const ERR_HASHKEYNOTFOUND = 1106;
	const ERR_1106 = "La clef de hash est inexistante ou a expiré";
	
	const ERR_TOOMANYREQUEST = 1107;
	const ERR_1107 = "Trop de connexions infructueuses ont eu lieu sur cet identifiant OU depuis votre ordinateur. Veuillez réessayer dans 30 minutes.";
	
	const ERR_DISCONNECTEDBYADMIN = 1108;
	const ERR_1108 = "Vous avez été déconnecté ! Ceci survient généralement lorsqu'un professeur ou un administrateur effectue des opérations de paramétrage sur l'un de vos espaces";
	
	const ERR_SHARESPACEDOWN = 1111; // Toute connexion est bloquée, méme si on dispose du cookie admin
	const ERR_1111 = "ShareSpace a été désactivé par un administrateur";
	
	// 20xx : erreurs de sécurité
	// envoie un mail à webmaster@saint-boni.be (en production)
	const ERR_RIGHTS = 2000;
	const ERR_2000 = "L'utilisateur en cours n'avait pas assez de droits pour effectuer cette action";
	
	const ERR_ILLOGICAL = 2001;
	const ERR_2001 = "Cette action n'est pas logique";
	
	const ERR_BRUTEFORCEATTACK = 2002;
	const ERR_2002 = "Un nombre beaucoup trop important de tentatives infructueuses de connexions a �t� d�tect�. ShareSpace a �t� d�sactiv� par mesure de s�curit�";
?>