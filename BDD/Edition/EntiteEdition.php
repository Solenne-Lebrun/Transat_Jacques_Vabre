<?php

namespace transat;

use Cassandra\Date;


class EntiteEdition extends AbstractEntite
{

    const TABLENAME = 'edition';
    static $COLNAMES = array('edition_num', 'edition_date_depart', 'edition_port_depart', 'edition_port_arrivee');
    static $COLTYPES = array('integer', 'date', 'text', 'text'); // par facilité, les types des formulaires => à améliorer
    static $PK = array('edition_num');  // tableau pour une éventuelle clé composite
    static $AUTOID = TRUE; // booléen indiquant si le renseignement de la clé est automatisé
    static $FK = array();  // tableau pour les éventuelles clés étrangères
    static $TABLESFK = array();


    protected $edition_num;
    protected $edition_date_depart;
    protected $edition_port_depart;
    protected $edition_port_arrivee;


    public function getALLValues()
    {
        return array($this->edition_num, $this->edition_date_depart, $this->edition_port_depart, $this->edition_port_arrivee);
    }


    /**
     * @return int
     */
    public function getEditionNum(): int
    {
        return $this->edition_num;
    }

    /**
     * @param int $edition_num
     * @return EntiteEdition
     */
    public function setEditionNum(int $edition_num): EntiteEdition
    {
        $this->edition_num = $edition_num;
        return $this;
    }

    /**
     * @return Date
     */
    public function getEditionDateDepart()
    {
        return $this->edition_date_depart;
    }

    /**
     * @param Date $edition_date_depart
     * @return EntiteEdition
     */
    public function setEditionDateDepart($edition_date_depart): EntiteEdition
    {
        $this->edition_date_depart = $edition_date_depart;
        return $this;
    }


    /**
     * @return string
     */
    public function getEditionPortDepart(): string
    {
        return $this->edition_port_depart;
    }

    /**
     * @param string $edition_port_depart
     * @return EntiteEdition
     */
    public function setEditionPortDepart($edition_port_depart): EntiteEdition
    {
        $this->edition_port_depart = $edition_port_depart;
        return $this;
    }

    /**
     * @return string
     */
    public function getEditionPortArrivee(): string
    {
        return $this->edition_port_arrivee;
    }

    /**
     * @param string $edition_port_arrivee
     * @return EntiteEdition
     */
    public function setEditionPortArrivee(string $edition_port_arrivee): EntiteEdition
    {
        $this->edition_port_arrivee = $edition_port_arrivee;
        return $this;
    }

    public function __toString()
    {
        return "object:EntiteEdition (" . $this->edition_num . ", " . $this->edition_date_depart .
            ", " . $this->edition_port_depart . ", " . $this->edition_port_arrivee . ")";
    }

    /**
     * @param array $params
     * @param \MyPDO $myPDO
     * @param string $message
     * @param String $action
     * la méthode isValid teste si les données entrée dans les champs de saisie lors de l'insertion
     * ou la modification des tables de la base de données sont correctes ou non.
     * et affiche un message correspondant à l'erreur ou erreurs qu'elle détecte.
     * @return bool
     */
    public static function isValid(array $params, \MyPDO $myPDO, string &$message, String $action): bool
    {
        if ($action == "insérerEntité")
            $messageErreur = "<h1 class='title is-1 has-text-danger'>Erreur Insertion</h1>\n";
        else
            $messageErreur = "<h1 class='title is-1 has-text-danger'>Erreur Modification</h1>\n";

        $isValid = true;
        foreach ($params as $key => $col) {
            if (empty($col)) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>le champ " . $key . " est vide !!!</h2>";
                $isValid = false;
            }
        }

        if (!$isValid)
            $message .= getMessage($messageErreur, "is-danger");;

        return $isValid;
    }
}