<?php
if (!empty($_SERVER["HTTP_REFERER"])) {
    $vi = $_GET["VI"];
    $email = $_GET["Email"];
    ?>
<!DOCTYPE html>
<html>
<head>
<title>Espace de vote</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/scripte.js"></script>
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
	integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
	crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
	integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
	crossorigin="anonymous"></script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
	integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
	crossorigin="anonymous"></script>
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
	integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
	crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="css/VotantCss.css">

</head>
<body
	style="background: url('centre_dépouillement/images/4.jpg') no-repeat; background-size: cover; height: auto;">
	<div class="position-relative"
		style="margin-top: -10px; background-color: rgb(34, 36, 42);">
		<br>
		<h1>Espace de vote</h1>
		<hr>
	</div>
<?php
    include "user.php";
    include "Votant.php";
    $vt = new Votant();
    $vt = $vt->GetVotant($vi, $email);
    echo $vt->Affiche();
    if (array_key_exists('Confirmer', $_POST)) {
        $cand = $_POST["listcandidat"];
        $vt->AddToBulletinVote($cand);
        $cotenu1 = $vt->getContentCO();
        $cotenu2 = $vt->getContentDE();
        echo "<div class='row alert alert-info msgalert' id='msg' style=\"display: block;\">
				<p>Vous devez envoyer deux mail pour valide votre vote merci!</p>
                <b><a href=\"mailto:chaimaasafi443@gmail.com?subject=$vi&body=$cotenu1\">Mail 1</a>
                <a href=\"mailto:chaimaa.mzk@gmail.com?subject=$vi&body=$cotenu2\">Mail 2</a></b>
                <button style='margin-left: 390px;' class='btn btn-outline-primary btn-sm' onclick='terminer()'>terminer</button>
                    <script> 
                       function terminer(){
                        let d1 = document.getElementById(\"msg\");
                        if(getComputedStyle(d1).display != \"none\"){
                                d1.style.display = \"none\";
                            }
                        location.replace(\"http://localhost/SysVote/Voter.php?VI=$vi&Email=$email\");
                       }   
                    </script>
            </div>";
    }
    ?>
<!---------------------------- Partie de voutage ---------------------------->

	<form method='post'>
		<div class="titre shadow-lg rounded-top">
			<h4>Effectuer un vote</h4>
		</div>
		<div class="shadow-lg bg-white rounded-bottom Contenu">
			<div class="row" style="margin-left: 40px;">
				<select class="form-control inpute" id="listcandidat"
					name="listcandidat" required="required">
					<option value="" disabled selected>Choisie une candidat</option>
                    <?php
                    $Cand = new Candidat();
                    $Candidats = $Cand->GetAllcandidat();
                    foreach ($Candidats as $C) {
                    ?>

                    <option value="<?php echo $C["IDcand"] ?>"><?php echo $C["NomC"]." ".$C["PrenomC"]  ?></option>

                    <?php
    }

    ?>
            </select> <input type="submit"
					class="btn btn-outline-success btn-sm" style="margin-left: 20px;"
					name="Confirmer" value="Confirmer">
			</div>
		</div>
	</form>

<?php
} else {
    echo "<script> alert(\"Vous devez s'authentifier si vous voulez acceder votre profile !!!\")</script >";
    header("refresh:0, url=index.php");
}

?>

</body>
</html>