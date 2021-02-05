<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include 'phpseclib/Crypt/RSA.php';
include 'phpseclib/Crypt/Rijndael.php';
include "Candidat.php";

/**
 *
 * @author Fatima-Zahra Belgacha
 *        
 */
class Votant
{

    private $NumVi;

    private $Nom;

    private $Prenom;

    private $DateNaissance;

    private $Email;

    private $KpVi;

    private $KprVi;

    private $passphrase;

    /**
     * Clé étrangaire entre la table users et votant
     *
     * @var int
     */
    private $userid;

    /**
     * Clé étrangaire entre la table Candidat et votant
     *
     * @var int
     */
    private $IDcand;

    /**
     * variable utilise pour ouvrire la connexion avec la base de donne une seul fois
     *
     * @var PDO
     */
    private $db;

    /**
     * variable utilise pour crée une instance de la classe crypte rsa une seul fois
     *
     * @var Crypt_RSA
     */
    private $rsa;
    
    
    private $I_B;

    /**
     * Class permet de crée un votant
     */
    function __construct()
    {
        $this->rsa = new Crypt_RSA();

        try {
            $this->db = new PDO('mysql:host=localhost;dbname=dbsystemvote', 'root', '');
        } catch (PDOException $e) {
            echo "Impossible de se connecter! \n" . $e;
        }
    }

    /**
     * Fonction permet de crée un nouveau votant dans le cas d'inscription
     *
     * @return int : retourne NumVi d'un votant
     * @param String $n
     *            : nom
     * @param String $p
     *            : prénom
     * @param DateTime $d
     *            : date naissance
     * @param String $ps
     *            : passphrase
     * @param String $em
     *            : email
     * @param int $id
     *            : id de votant dans la table users
     */
    public function NvVotant($n, $p, $d, $ps, $em, $id)
    {
        $this->setNom($n);
        $this->setPrenom($p);
        $this->setDateNaissance($d);
        $this->setpassphrase($ps);
        $this->setEmail($em);
        $this->setUserid($id);
        $keys = $this->rsa->createKey(2048);
        if (is_null($this->KpVi)) {
            $this->setKpVi($keys['publickey']);
            $this->setKprVi($keys['privatekey']);
        }
        $this->incrementNbtotalVotant();
        $this->GPGkeys($this->getNom() . " " . $this->getPrenom(), $this->getEmail(), $this->getPassphrase());
        return $this->AjouterVotant();
    }

    /**
     * Fonction permet de récupérer un votant à partir de la base de donne dans le cas d'authentification
     *
     * @return Object : return un object de type Votant
     * @param int $vi
     *            : NumVi d'un Votant
     * @param String $n
     *            : nom
     * @param String $p
     *            : prénom
     * @param DateTime $d
     *            : date naissance
     * @param String $ps
     *            : passphrase
     * @param String $em
     *            : email
     * @param String $kp
     *            : clé public
     * @param String $kpr
     *            : clé privé
     */
    public function OldVotant($vi, $n, $p, $d, $ps, $em, $kp, $kpr)
    {
        $this->setNumVi($vi);
        $this->setNom($n);
        $this->setPrenom($p);
        $this->setDateNaissance($d);
        $this->setpassphrase($ps);
        $this->setEmail($em);
        $this->setKpVi($kp);
        $this->setKprVi($kpr);
        return $this;
    }

    /**
     * Fonction permet d'ajouter un nouveau votant dans la base de donne
     *
     * @return int : numVi d'un votant
     */
    private function AjouterVotant()
    {
        $vi = $this->VI();
        $this->db->exec("INSERT INTO votant(NumVi, Nom, Prenom, DateNaissance, passphrase, KpVi, KprVi, userid) VALUES ('" . $vi . "','" . $this->getNom() . "','" . $this->getPrenom() . "','" . $this->getDateNaissance() . "','" . $this->getPassphrase() . "','" . $this->getKpVi() . "','" . $this->getKprVi() . "','" . $this->userid . "')");
        return $vi;
    }

    /**
     * Fonction return True si un votant est deja existe et return False si votant n'exist pas
     *
     * @return boolean
     */
    public function NotExiste()
    {
        return is_null($this->getNumVi());
    }

    /**
     * Fonction permet d'ajouter le candidat choisie par un votant
     *
     * @param int $IDcand
     * @return string
     */
    public function AddToBulletinVote($IDcand)
    {   
        $this->IDcand = $IDcand;
        $this->db->exec("UPDATE votant SET IDcand = '" . $IDcand . "' WHERE NumVi = '" . $this->getNumVi() . "'");
        $this->I_B = $this->chiffremsg($this->getNumVi())." ".$this->chiffremsg($this->IDcand);
        $this->db->exec("UPDATE votant SET MSG = '" . $this->I_B . "' WHERE NumVi = '" . $this->getNumVi() . "'");
        $this->InsertInfoEmail();

        return $this->I_B;
    }

    private function InsertInfoEmail()
    {
        $cpt = 1;
        
        $sql = $this->db->prepare("SELECT COUNT(*) AS cpt FROM centre_de_table WHERE id_votant = :id AND i_b_votant = :msg");
        $sql->execute(array(
            ':id' => $this->getNumVi(),
            ':msg' => $this->I_B
        ));
        if (is_null($sql) == false) {
            $res = $sql->fetch();
            if ($res["cpt"]) {
                $cpt = $res["cpt"] + 1;
            }
        }
        $insert = $this->db->prepare("INSERT INTO centre_de_table(id_votant, i_b_votant, cpt) VALUES (:id, :msg, :cpt)");
        $insert->execute(array(
            ':id' => $this->getNumVi(),
            ':msg' => $this->I_B,
            ':cpt' => $cpt
        ));
    }

    /**
     * fonction pour le chiffrement d'un message
     *
     * @return string
     */
    private function chiffremsg($MSG)
    {
        $DE = file_get_contents('DEpublic.key');
        $this->rsa->loadKey($DE);
        $Enmsg = $this->rsa->encrypt($MSG);
        return base64_encode($Enmsg);
    }

    /**
     * Incrémenter le nombre totale des votants
     */
    private function incrementNbtotalVotant()
    {
        $res = $this->db->query("SELECT nbtotalvote FROM nbtotalvote WHERE 1");
        $nb = $res->fetch();
        $n = $nb["nbtotalvote"] + 1;
        $this->db->exec("UPDATE nbtotalvote SET nbtotalvote=$n WHERE 1");
    }

    /**
     * Génerer des clés gpg (clés prive, clé public)
     *
     * @param string $name
     * @param string $email
     * @param string $pass
     */
    private function GPGkeys($name, $email, $pass)
    {
        shell_exec("echo %echo Generating a basic OpenPGP key >key.txt");
        shell_exec("echo Key-Type: RSA >>key.txt");
        shell_exec("echo Key-Length: 2048 >>key.txt");
        shell_exec("echo Subkey-Type: RSA >>key.txt");
        shell_exec("echo Subkey-Length: 2048 >>key.txt");
        shell_exec("echo Name-Real: $name >>key.txt");
        shell_exec("echo Name-Comment: no comment >>key.txt");
        shell_exec("echo Name-Email: $email >>key.txt");
        shell_exec("echo Expire-Date: 0 >>key.txt");
        shell_exec("echo Passphrase: $pass >>key.txt");
        shell_exec("echo # Do a commit here, so that we can later print \"done\" :-) >>key.txt");
        shell_exec("echo %commit >>key.txt");
        shell_exec("echo %echo done >>key.txt");
        shell_exec('gpg --batch --gen-key key.txt');
    }

    /**
     * Recupere le numero VI à partir de la table Votant si ce votant est déja exist
     *
     * @return int|NULL
     */
    public function getNumVi()
    {
        $vi = $this->db->prepare("SELECT NumVi, Nom, Prenom, DateNaissance, passphrase, KpVi, KprVi FROM votant WHERE Nom=:nom AND Prenom=:prenom");
        $vi->execute(array(
            ':nom' => $this->Nom,
            ':prenom' => $this->Prenom
        ));
        if (is_null($vi) == false) {
            $v = $vi->fetch();
            return $v['NumVi'];
        }
        return null;
    }

    /**
     * fonction permet de crée un nouveau vi , il prende la valeur maximum dans la table votant puis il ajoute 1 à cette valeur
     *
     * @return number|NULL
     */
    public function VI()
    {
        $vi = $this->db->query("SELECT COUNT(*) AS numbre, MAX(NumVi) AS maximum FROM votant");
        if (is_null($vi) == false) {
            $v = $vi->fetch();

            if ($v['numbre'] == 0) {
                $nm = 100;
            } else {
                $nm = $v['maximum'] + 1;
            }
            return $nm;
        }
        return null;
    }

    public function getNom()
    {
        return $this->Nom;
    }

    public function getPrenom()
    {
        return $this->Prenom;
    }

    public function getDateNaissance()
    {
        return $this->DateNaissance;
    }

    /**
     * Permet de récupérer un votant exist dans la table votant , cette fonction utilise dans le cas d'authentification
     *
     * @param int $ID
     *            : id d'un votant dans la table users
     * @param String $email
     * @return Object|NULL
     */
    public function GetVotant($ID, $email)
    {
        $sql = $this->db->query("SELECT NumVi, Nom, Prenom, DateNaissance, passphrase, KpVi, KprVi,userid FROM votant WHERE userid = " . $ID);
        if (is_null($sql) == false) {
            $res = $sql->fetch();

            $this->OldVotant($res['NumVi'], $res['Nom'], $res['Prenom'], $res['DateNaissance'], $res['passphrase'], $email, $res['KpVi'], $res['KprVi']);

            return $this;
        }
        return null;
    }
    /**
     * Body email envoi au centre comptage
     */
    public function getContentCO(){
        $sql = $this->db->query("SELECT kpCO FROM co");
        $res = $sql->fetch();
        $this->rsa->loadKey($res["kpCO"]);
        $content = $this->rsa->encrypt($this->getNumVi()." ".$this->I_B);
        return base64_encode($content);
    }
    /**
     * Body email envoyer au centre depouillement
     */
    public function getContentDE(){
        $MSG = "http://localhost/SysVote/centre_d%c3%a9pouillement/login.php";
        
        return $MSG;
    }
    /**
     *
     * @param int $NumVi
     */
    public function setNumVi($NumVi)
    {
        $this->NumVi = $NumVi;
    }

    /**
     *
     * @param String $Nom
     */
    public function setNom($Nom)
    {
        $this->Nom = $Nom;
    }

    /**
     *
     * @param String $Prenom
     */
    public function setPrenom($Prenom)
    {
        $this->Prenom = $Prenom;
    }

    /**
     *
     * @param DateTime $DateNaissance
     */
    public function setDateNaissance($DateNaissance)
    {
        $this->DateNaissance = $DateNaissance;
    }

    /**
     *
     * @param String $Email
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     *
     * @param String $KpVi
     */
    public function setKpVi($KpVi)
    {
        $this->KpVi = $KpVi;
    }

    /**
     *
     * @param String $KprVi
     */
    public function setKprVi($KprVi)
    {
        $this->KprVi = $KprVi;
    }

    /**
     *
     * @param String $passphrase
     */
    public function setPassphrase($passphrase)
    {
        $this->passphrase = $passphrase;
    }

    /**
     *
     * @param int $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     *
     * @param PDO $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     *
     * @return mixed
     */
    public function getKpVi()
    {
        return $this->KpVi;
    }

    /**
     *
     * @return mixed
     */
    public function getKprVi()
    {
        return $this->KprVi;
    }

    /**
     *
     * @return mixed
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }

    /**
     *
     * @return mixed
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     *
     * @return PDO
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Permet d'afficher le bulletin de votant ou bien les informations d'un votant
     *
     * @return string
     */
    public function Affiche()
    {
        $C = new Candidat();
        $text = '<div class="titre shadow-lg rounded-top "><h4>Bienvenue dans votre espace </h4></div>';
        $text .= '<div class="shadow-lg bg-white rounded-bottom Contenu">';
        $text .= "<br><B> Nom : </B>" . $this->Nom . "<br><B> Prenom : </B>" . $this->Prenom;
        $text .= "<br><B> DateNaissance : </B>" . $this->DateNaissance;
        $text .= "<br><B> Email : </B>" . $this->Email;
        $text .= '<br><br><button class="btn btn-outline-danger btn-sm" onclick="myFunction()">Se deconnecter</button>';
        $text .= '<!-- Button trigger modal -->
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
              Résultat de vote
            </button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Résultat de vote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ' . $C->GetResultat() . '
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>' . "</div>" . "<script>
	function myFunction() {
                  
				  location.replace(\"http://localhost/SysVote/index.php\");
				}
                
				
$('#myModal').on('shown.bs.modal', function () {
				    $('#myInput').trigger('focus')
				})
				</script>";
        return $text;
    }
}