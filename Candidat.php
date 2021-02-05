<?php

/**
 * 
 * @author FatimaZahra Belgacha
 *
 */
class Candidat
{

    private $IDcand;

    private $NomC;

    private $PrenomC;

    private $db;

    /**
     * Crée nouveau candidat
     */
    public function __construct()
    {
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=dbsystemvote', 'root', '');
        } catch (PDOException $e) {
            echo "Impossible de se connecter! \n" . $e;
        }
    }

    /**
     *
     * @return mixed
     */
    public function getIDcand()
    {
        return $this->IDcand;
    }

    /**
     *
     * @param mixed $IDcand
     */
    public function setIDcand($IDcand)
    {
        $this->IDcand = $IDcand;
    }

    /**
     *
     * @return mixed
     */
    public function getNomC()
    {
        return $this->NomC;
    }

    /**
     *
     * @param mixed $NomC
     */
    public function setNomC($NomC)
    {
        $this->NomC = $NomC;
    }

    /**
     *
     * @return mixed
     */
    public function getPrenomC()
    {
        return $this->PrenomC;
    }

    /**
     *
     * @param mixed $PrenomC
     */
    public function setPrenomC($PrenomC)
    {
        $this->PrenomC = $PrenomC;
    }

    /**
     * Donne tous les candidats
     *
     * @return mixed[][]
     */
    public function GetAllcandidat()
    {
        $result = array();
        $i = 1;
        $sql = $this->db->query("SELECT IDcand, NomC, PrenomC FROM candidat");
        while ($T = $sql->fetch()) {
            $result[$i] = array(
                "IDcand" => $T["IDcand"],
                "NomC" => $T["NomC"],
                "PrenomC" => $T["PrenomC"]
            );
            $i = $i + 1;
        }

        return $result;
    }

    /**
     * Permet de retourne le candidat par son numero
     *
     * @param int $num
     * @return mixed
     */
    public function GetCandidat($num)
    {
        $result = $this->GetAllcandidat();
        return $result[$num];
    }

    /**
     * Return un tableau html des resultat de vote
     *
     * @return string
     */
    public function GetResultat()
    {
        $sql = $this->db->query("SELECT NomC, PrenomC, nbr_vote, pr_vote FROM candidat ORDER BY nbr_vote DESC");
        $Text = '';

        while ($res = $sql->fetch()) {

            $Text .= '<div class="form-inline">
            <label style="width: 50%; color:black;">
            <strong class="border-bottom">' . $res["NomC"] . '<span>(' . $res["nbr_vote"] . ' Votes)</span></strong></label>
            <br>
        <div class="progress" style="width: 75%; height: 15px;">
            <div class="progress-bar" role="progressbar" style="width:' . $res["pr_vote"] . '%; background-color: #e9353a; color: black;" aria-valuenow="' . $res["pr_vote"] . '%" aria-valuemin="0" aria-valuemax="100">' . $res["pr_vote"] . '%</div>
          </div>
        </div>';
        }

        return $Text;
    }
}

