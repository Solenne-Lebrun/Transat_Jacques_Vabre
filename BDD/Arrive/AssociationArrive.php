<?php


namespace transat;


use Cassandra\Date;

class AssociationArrive extends AbstractEntite
{
    const TABLENAME = 'arrive';
    static $COLNAMES = array('edition_num', 'bateau_id', 'date_arrivee');
    static $COLTYPES = array('integer', 'integer', 'date'); // par facilité, les types des formulaires => à améliorer
    static $PK = array('edition_num', 'bateau_id');  // tableau pour une éventuelle clé composite
    static $AUTOID = FALSE; // booléen indiquant si le renseignement de la clé est automatisé
    static $FK = array('edition_num', 'bateau_id');  // tableau pour les éventuelles clés étrangères
    static $TABLESFK = array('edition', 'bateau');

    protected $edition_num;
    protected $bateau_id;
    protected $date_arrivee;

    public function getALLValues()
    {
        return array($this->edition_num, $this->bateau_id, $this->date_arrivee);
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
     * @return AssociationArrive
     */
    public function setEditionNum(int $edition_num): AssociationArrive
    {
        $this->edition_num = $edition_num;
        return $this;
    }

    /**
     * @return int
     */
    public function getBateauId(): int
    {
        return $this->bateau_id;
    }

    /**
     * @param int bateau_id
     * @return AssociationArrive
     */
    public function setBateauId(int $bateau_id): AssociationArrive
    {
        $this->bateau_id = $bateau_id;
        return $this;
    }

    /**
     * @return Date
     */
    public function getDateArrivee()
    {
        return $this->date_arrivee;
    }

    /**
     * @param Date $date_arrivee
     * @return AssociationArrive
     */
    public function setDateArrivee($date_arrivee): AssociationArrive
    {
        $this->date_arrivee = $date_arrivee;
        return $this;
    }

    public function __toString()
    {
        return "object:AssociationArrive (" . $this->edition_num . ", " . $this->bateau_id .
            ", " . $this->date_arrivee . ")";
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
        $isValid = true;

        if ($action == "insérerEntité")
            $messageErreur = "<h1 class='title is-1 has-text-danger'>Erreur Insertion</h1>\n";
        else
            $messageErreur = "<h1 class='title is-1 has-text-danger'>Erreur Modification</h1>\n";
        if (isset($params['date_arrivee'])) {
            $requete_date_depart = 'SELECT edition_date_depart FROM edition WHERE edition_num = ' . $params['edition_num'];
            $sth = $myPDO->getPdo()->prepare($requete_date_depart);
            $sth->execute();
            $result = $sth->fetch($myPDO->getPdo()::FETCH_BOTH);
            $dateDepart = date_create($result[0]);
            $dateArrive = date_create($params['date_arrivee']);

            if ($dateArrive <= $dateDepart) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>date d'arrivée incorrecte !!</h2>";
                $isValid = false;
            }
        }

        if ($action == "insérerEntité") {
            $arrive = $myPDO->get(array('edition_num' => $params['edition_num'], 'bateau_id' => $params['bateau_id']));
            if ($arrive != false) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>Le bateau est déjà utilisé dans cette édition</h2>";
                $isValid = false;
            }
        }

        if (!$isValid)
            $message .= getMessage($messageErreur, "is-danger");;

        return $isValid;
    }


}