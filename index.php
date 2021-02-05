<!DOCTYPE html>
<html>
<head>
<title>System Vote électronique</title>


<link rel="stylesheet" type="text/css" href="css/style.css">

<script src="js/scripte.js"></script>
<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
	integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
	crossorigin="anonymous">
<script
	src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
	integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
	crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="css/indexcss.css">
</head>

<body
	style="background: url('centre_dépouillement/images/4.jpg'); background-size: cover; height: auto;">
<?php
include "user.php";
include "Votant.php";
if (array_key_exists('Sauthentifier', $_POST)) {
    $email = $_POST["email1"];
    $motpass = $_POST["motpasse1"];

    $user = new User();
    $id = $user->Authentifie($email, $motpass);

    if (! is_null($id)) {
        $vt = new Votant();
        $vt = $vt->GetVotant($id, $email);
        header("refresh:0, url=Voter.php?VI=$id&Email=$email");
    } else {
        header("refresh:2, url=index.php");
    }
}
if (array_key_exists('inscrit', $_POST)) {

    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $dateN = $_POST["datenaissance"];
    $email = $_POST["email2"];
    $motpass = $_POST["motpasse2"];
    $user = new User();
    $id = $user->Inscrit($nom, $prenom, $dateN, $email, $motpass);
    if (! is_null($id)) {
        $vt = new Votant();
        $vt->NvVotant($nom, $prenom, $dateN, $motpass, $email, $id);

        header("refresh:0, url=index.php");
    } else {

        header("refresh:2, url=index.php");
    }
}

?>


            <div class="position-relative"
		style="margin-top: -15px; background-color: rgb(34, 36, 42);">
		<br>
		<h1>Systéme de vote éléctronique</h1>
		<hr>
	</div>

	<!---------------------------- Partie d'authetification ---------------------------->
	<div class="row" id="authentification" style="display: block">
		<div class="titre shadow-lg rounded-top">
			<h4>S'autentifier</h4>
		</div>
		<form method="post"
			class="shadow-lg p-3 mb-5 bg-white rounded-bottom Contenu">
			<div class="row">
				<div class="input-field col s12">
					<input id="email" type="email" class="validate" name="email1"
						required> <label for="email">Email</label> <span
						class="helper-text" data-error="ce champ est obligatoir"
						data-success="les informations correct">Entre votre email</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<input id="password" type="password" class="validate"
						name="motpasse1" required> <label for="password">Mot de passe</label>
					<span class="helper-text" data-error="ce champ est obligatoir"
						data-success="les informations correct">Crée un nouveau mot de
						passe</span>
				</div>
			</div>

			<div style="width: 400px; margin-left: 110px;" class="row">
				<input type="submit" class="btn btn-outline-primary waves-light"
					style="margin-right: 5px;" name="Sauthentifier" id="Sauthentifier"
					value="S'authentifie"> <input type="button"
					class="btn btn-outline-primary waves-light"
					style="margin-right: 5px;" name="Inscrite" id="Inscrite"
					value="Inscrit"> <input type="button"
					class="btn btn-outline-danger waves-light" name="Annuler"
					id="Annuler" value="Annuler"
					onclick='event.preventDefault(); location.reload();'>
			</div>
		</form>
	</div>
	<!---------------------------- Partie d'inscription ---------------------------->
	<div class="row" id="Inscription" style="display: none;">
		<div class="titre shadow-lg rounded-top">
			<h4>S'inscrire</h4>
		</div>
		<form method="post"
			class="shadow-lg p-3 mb-5 bg-white rounded-bottom Contenu">
			<div class="row">
				<div class="input-field col s6">
					<input id="first_name" type="text" class="validate" name="nom"
						required> <label for="first_name">Nom</label> <span
						class="helper-text" data-error="ce champ est obligatoir"
						data-success="les informations correct">Entre votre Nom correcte</span>
				</div>
				<div class="input-field col s6">
					<input id="last_name" type="text" class="validate" name="prenom"
						required> <label for="last_name">Prénom</label> <span
						class="helper-text" data-error="ce champ est obligatoir"
						data-success="les informations correct">Entre votre prénom
						correcte</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<input id="disabled" type="date" class="validate"
						name="datenaissance" max="2000-12-31" min="1955-12-31" required> <label
						for="date">Date de naissance</label> <span class="helper-text"
						data-error="ce champ est obligatoir"
						data-success="les informations correct">Date naissance obligatoire</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<input id="email" type="email" class="validate" name="email2"
						required> <label for="email">Email</label> <span
						class="helper-text" data-error="ce champ est obligatoir"
						data-success="les informations correct">Entre votre email</span>
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12">
					<input id="password" type="password" class="validate"
						name="motpasse2" required> <label for="password">Mot de passe</label>
					<span class="helper-text" data-error="ce champ est obligatoir"
						data-success="les informations correct">Crée un nouveau mot de
						passe</span>
				</div>
			</div>
			<div style="width: 400px; margin-left: 110px;" class="row">
				<input type="submit" class="btn btn-outline-primary waves-light"
					style="margin-right: 5px;" name="inscrit" id="inscrit"
					value="S'inscrit"> <input type="button"
					class="btn btn-outline-primary waves-light"
					style="margin-right: 5px;" name="authentifie" id="authentifie"
					value="S'authentifie"> <input type="button"
					class="btn btn-outline-danger waves-light" name="Annuler"
					value="Annuler"
					onclick='event.preventDefault(); location.reload();'>
			</div>

		</form>
	</div>

	<!---------------------------- Scripte javascripte pour qlq click ---------------------------->
	<script type="text/javascript">
        let Auth = document.getElementById("authentifie");
        let Insc = document.getElementById("Inscrite");
        let d1 = document.getElementById("authentification");
        let d2 = document.getElementById("Inscription");


        Insc.addEventListener("click", () => {
            if(getComputedStyle(d1).display != "none"){
                d1.style.display = "none";
                d2.style.display = "block";
            } else {
                d1.style.display = "block";
            }
        })
        Auth.addEventListener("click", () => {
            if(getComputedStyle(d2).display != "none"){
                d1.style.display = "block";
                d2.style.display = "none";
            } else {
                d2.style.display = "block";
            }
        })
		window.history.forward(); 
        function noBack() { 
            window.history.forward(); 
        } 
    </script>


</body>
</html>
