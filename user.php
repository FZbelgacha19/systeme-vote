<?php

/**
 * 
 */
class User
{

    private $ID;

    private $Nom;

    private $Prenom;

    private $DateNaissance;

    private $Email;

    private $MotDePasse;

    private $db;

    /**
     * Crée nouveau utilisateur
     */
    function __construct()
    {
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=dbsystemvote', 'root', '');
           
        } catch (PDOException $e) {
            echo "Impossible de se connecter! \n" . $e;
        }
    }
    
    /**
     * Permet d'ajouter un nouveau utilisateur
     *
     * @param String $n
     *            : nom
     * @param String $p
     *            : prénom
     * @param DateTime $dN
     *            : date de naissance
     * @param String $e
     *            : email
     * @param String $mp
     *            : mot de passe
     * @return NULL|number
     */
    public function Inscrit($n, $p, $dN, $e, $mp)
    {
        $this->setEmail($e);
        $this->ID = $this->getUserID();
        $this->setNom($n);
        $this->setPrenom($p);
        $this->setDateNaissance($dN);
        $this->setMotDePasse($mp);

        if (is_null($this->ID)) {
            $this->ID = $this->ID();
            $arr = array(
                $this->ID,
                $this->getNom(),
                $this->getPrenom(),
                $this->getDateNaissance(),
                $this->getEmail(),
                $this->getMotDePasse()
            );
            $sql = $this->db->prepare("INSERT INTO users(UserID, Nom, Prenom, DateNaissance, Email, MotDePasse) VALUES(?,?,?,?,?,?)");
            $sql->execute($arr);
            return $this->ID;
        }
        echo "<script> alert(\"Ce utilisateur $this->ID est déja existe\")</script >";
        sleep(3);
        return null;
    }

    /**
     * Permet récupérer un utilisateur par son email et son mot de passe s'il exist
     *
     * @param String $em
     *            : email
     * @param String $mp
     *            : mot de passe
     * @return int|NULL
     */
    public function Authentifie($em, $mp)
    {
        $sql = $this->db->query("SELECT UserID, Email, MotDePasse FROM users WHERE Email LIKE '" . $em . "' AND MotDePasse LIKE '" . $mp . "'");
        if (is_null($sql) == false) {
            $res = $sql->fetch();

            if (is_null($res['Email']) or is_null($res['MotDePasse']))
                echo "<script> alert(\"Votre email ou mot de passe est errone\")</script>";
            else
                return $res['UserID'];
        }
        sleep(3);
        return null;
    }

    /**
     * Permet de récupérer l'id d'un utilisateur en utilisant son email
     *
     * @return int|NULL
     */
    public function getUserID()
    {
        $sql = $this->db->query("SELECT UserID FROM users WHERE Email LIKE '" . $this->getEmail() . "'");
        if (is_null($sql) == false) {
            $res = $sql->fetch();
            return $res['UserID'];
        }
        return null;
    }

    /**
     * Permet de crée un nouveau id pour un utilisateur prenons le maximum Userid dans la table et on ajoute 1 à cette valeur
     *
     * @return number|NULL
     */
    public function ID()
    {
        $id = $this->db->query("SELECT COUNT(*) AS numbre, MAX(UserID) AS maximum FROM users");
        if (is_null($id) == false) {
            $v = $id->fetch();

            if ($v['numbre'] == 0) {
                $this->Numid = 100;
            } else {
                $this->Numid = $v['maximum'] + 1;
            }
            return $this->Numid;
        }
        return null;
        
    }

    /**
     *
     * @return mixed
     */
    public function getNom()
    {
        return $this->Nom;
    }

    /**
     *
     * @param mixed $Nom
     */
    public function setNom($Nom)
    {
        $this->Nom = $Nom;
    }

    /**
     *
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->Prenom;
    }

    /**
     *
     * @param mixed $Prenom
     */
    public function setPrenom($Prenom)
    {
        $this->Prenom = $Prenom;
    }

    /**
     *
     * @return mixed
     */
    public function getDateNaissance()
    {
        return $this->DateNaissance;
    }

    /**
     *
     * @param mixed $DateNaissance
     */
    public function setDateNaissance($DateNaissance)
    {
        $this->DateNaissance = $DateNaissance;
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
     * @param mixed $Email
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;
    }

    /**
     *
     * @return mixed
     */
    public function getMotDePasse()
    {
        return $this->MotDePasse;
    }

    /**
     *
     * @param mixed $MotDePasse
     */
    public function setMotDePasse($MotDePasse)
    {
        $this->MotDePasse = $MotDePasse;
    }
}

?>