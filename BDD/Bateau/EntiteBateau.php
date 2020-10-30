<?php


namespace transat;

class EntiteBateau extends AbstractEntite
{
    const TABLENAME = 'bateau';
    static $COLNAMES = array('bateau_id', 'bateau_nom', 'bateau_type');//toutes les colonnes
    static $COLTYPES = array('integer', 'text', 'text'); // par facilité, les types des formulaires => à améliorer
    static $PK = array('bateau_id');  // tableau pour une éventuelle clé composite
    static $AUTOID = TRUE; // booléen indiquant si le renseignement de la clé est automatisé
    static $FK = array();  // tableau pour les éventuelles clés étrangères
    static $TABLESFK = array();
    static $TYPESBATEAU = array('Multi50', 'IMOCA', 'Class40', 'ORMA', 'Classe 2', 'MOD70', 'Ultime');

    protected $bateau_id;
    protected $bateau_nom;
    protected $bateau_type;

    public function getALLValues()
    {
        return array($this->bateau_id, $this->bateau_nom, $this->bateau_type);
    }


    /**
     * @return int
     */
    public function getBateauId(): int
    {
        return $this->bateau_id;
    }

    /**
     * @param int $bateau_id
     * @return EntiteBateau
     */
    public function setBateauId(int $bateau_id): EntiteBateau
    {
        $this->bateau_id = $bateau_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getBateauNom(): string
    {
        return $this->bateau_nom;
    }

    /**
     * @param string $bateau_nom
     * @return EntiteBateau
     */
    public function setBateauNom($bateau_nom): EntiteBateau
    {
        $this->bateau_nom = $bateau_nom;
        return $this;
    }

    /**
     * @return string
     */
    public function getBateauType(): string
    {
        return $this->bateau_type;
    }

    /**
     * @param string $bateau_type
     * @return EntiteBateau
     */
    public function setBateauType(string $bateau_type): EntiteBateau
    {
        $this->bateau_type = $bateau_type;
        return $this;
    }

    public function __toString()
    {
        return "object:EntiteBateau (" . $this->bateau_id . ", " . $this->bateau_nom .
            ", " . $this->bateau_type . ")";
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

        if ($action == "insérerEntité") {
            $bateau = $myPDO->get(array('bateau_nom' => $params['bateau_nom']));
            if ($bateau != false) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>Ce nom de bateau est déjà utilisé !</h2>";
                $isValid = false;
            }
        } else {
            $bateau = $myPDO->get(array('bateau_nom' => $params['bateau_nom']));
            if ($bateau != false && $bateau->getBateauId() != $_GET['bateau_id']) {
                $messageErreur .= "<h2 class='title is-3 has-text-danger'>Ce nom de bateau est déjà utilisé !</h2>";
                $isValid = false;
            }
        }

        if (!$isValid)
            $message .= getMessage($messageErreur, "is-danger");;

        return $isValid;
    }

}
