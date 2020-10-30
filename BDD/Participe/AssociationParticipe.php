<?php


namespace transat;


class AssociationParticipe extends AbstractEntite
{
    const TABLENAME = 'participe';
    static $COLNAMES = array('edition_num', 'skipper_id', 'co_skipper_id', 'bateau_id');
    static $COLTYPES = array('integer', 'integer', 'integer', 'integer'); // par facilité, les types des formulaires => à améliorer
    static $PK = array('edition_num', 'skipper_id');  // tableau pour une éventuelle clé composite
    static $AUTOID = false; // booléen indiquant si le renseignement de la clé est automatisé
    static $FK = array('edition_num', 'skipper_id', 'co_skipper_id', 'bateau_id');  // tableau pour les éventuelles clés étrangères
    static $TABLESFK = array('edition', 'skipper', 'skipper', 'bateau');

    protected $edition_num;
    protected $skipper_id;
    protected $co_skipper_id;
    protected $bateau_id;

    public function getALLValues()
    {
        return array($this->edition_num, $this->skipper_id, $this->co_skipper_id, $this->bateau_id);
    }

    /**
     * @return mixed
     */
    public function getEditionNum()
    {
        return $this->edition_num;
    }

    /**
     * @param mixed $edition_num
     * @return AssociationParticipe
     */
    public function setEditionNum($edition_num): AssociationParticipe
    {
        $this->edition_num = $edition_num;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkipperId()
    {
        return $this->skipper_id;
    }

    /**
     * @param mixed $skipper_id
     * @return AssociationParticipe
     */
    public function setSkipperId($skipper_id): AssociationParticipe
    {
        $this->skipper_id = $skipper_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoSkipperId()
    {
        return $this->co_skipper_id;
    }

    /**
     * @param mixed $co_skipper_id
     */
    public function setCoSkipperId($co_skipper_id): AssociationParticipe
    {
        $this->co_skipper_id = $co_skipper_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBateauId()
    {
        return $this->bateau_id;
    }

    /**
     * @param mixed $bateau_id
     * @return AssociationParticipe
     */
    public function setBateauId($bateau_id): AssociationParticipe
    {
        $this->bateau_id = $bateau_id;
        return $this;
    }

    public function __toString()
    {
        return "object:AssociationParticipe (" . $this->edition_num . ", " . $this->skipper_id .
            ", " . $this->co_skipper_id . ", " . $this->bateau_id . ")";
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

        if ($params['skipper_id'] == $params['co_skipper_id']) {
            $messageErreur .= "<h2 class='title is-3 has-text-danger'>Le skipper et co_skipper doivent être différents</h2>";
            $isValid = false;
        }

        if ($action == "insérerEntité") {
            $skippers = $myPDO->get(array('edition_num' => $params['edition_num'], 'co_skipper_id' => $params['skipper_id']));
            if ($skippers != false) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>skipper est co_skipper dans un autre bateau</h2>";
                $isValid = false;
            }
            $skippers = $myPDO->get(array('edition_num' => $params['edition_num'], 'skipper_id' => $params['skipper_id']));
            if ($skippers != false) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>skipper est déjà utilisé dans cette édition</h2>";
                $isValid = false;
            }
        }

        $coSkippers = $myPDO->get(array('edition_num' => $params['edition_num'], 'co_skipper_id' => $params['co_skipper_id']));
        if ($coSkippers != false && $coSkippers->getSkipperId() != $_GET['skipper_id']) {
            $messageErreur .= "<h2 class='title is-3 has-text-danger'>co_skipper est déjà utilisé dans cette édition</h2>";
            $isValid = false;
        }
        $skippers = $myPDO->get(array('edition_num' => $params['edition_num'], 'skipper_id' => $params['co_skipper_id']));
        if ($skippers != false && $skippers->getSkipperId() != $_GET['skipper_id']) {
            $messageErreur .= "<h2 class='title is-3 has-text-danger'>co_skipper est skipper dans un autre bateau</h2>";
            $isValid = false;
        }
        $bateau = $myPDO->get(array('edition_num' => $params['edition_num'], 'bateau_id' => $params['bateau_id']));
        if ($bateau != false && $bateau->getSkipperId() != $_GET['skipper_id']) {
            $messageErreur .= "<h2 class='title is-3 has-text-danger'>Le bateau est déjà utilisé dans cette édition</h2>";
            $isValid = false;
        }

        if (!$isValid)
            $message .= getMessage($messageErreur, "is-danger");;

        return $isValid;
    }
}