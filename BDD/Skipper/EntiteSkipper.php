<?php

namespace transat;

class EntiteSkipper extends AbstractEntite
{
    const TABLENAME = 'skipper';
    static $COLNAMES = array('skipper_id', 'skipper_nom', 'skipper_prenom', 'skipper_nationalite');
    static $COLTYPES = array('integer', 'text', 'text', 'text');
    static $PK = array('skipper_id');
    static $AUTOID = true;
    static $FK = array();
    static $TABLESFK = array();

    protected $skipper_id;
    protected $skipper_nom;
    protected $skipper_prenom;
    protected $skipper_nationalite;


    public function getALLValues()
    {
        return array($this->skipper_id, $this->skipper_nom, $this->skipper_prenom, $this->skipper_nationalite);
    }

    /**
     * @return int
     */
    public function getSkipperId(): int
    {
        return $this->skipper_id;
    }

    /**
     * @param int $skipper_id
     * @return EntiteSkipper
     */
    public function setSkipperId(int $skipper_id): EntiteSkipper
    {
        $this->skipper_id = $skipper_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkipperNom(): string
    {
        return $this->skipper_nom;
    }

    /**
     * @param string $skipper_nom
     * @return EntiteSkipper
     */
    public function setSkipperNom($skipper_nom): EntiteSkipper
    {
        $this->skipper_nom = $skipper_nom;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkipperPrenom(): string
    {
        return $this->skipper_prenom;
    }

    /**
     * @param string $skipper_prenom
     * @return EntiteSkipper
     */
    public function setSkipperPrenom(string $skipper_prenom): EntiteSkipper
    {
        $this->skipper_prenom = $skipper_prenom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkipperNationalite()
    {
        return $this->skipper_nationalite;
    }

    /**
     * @param mixed $skipper_nationalite
     */
    public function setSkipperNationalite($skipper_nationalite): void
    {
        $this->skipper_nationalite = $skipper_nationalite;
    }

    public function __toString()
    {
        return "object:EntiteSkipper (" . $this->skipper_id . ", " . $this->skipper_nom .
            ", " . $this->skipper_prenom . ", " . $this->skipper_nationalite . ")";
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
            $message .= getMessage($messageErreur, "is-danger");

        return $isValid;
    }
}