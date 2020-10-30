<?php


class MyIteratorParticipe implements Iterator, Countable
{
    /**
     * @var MyPDO
     */
    protected $pdo;
    /**
     * @var int Représente l'id courant de la table Edition.
     */
    protected $edition_id;
    /**
     * @var int Représente l'id courant de la table Skipper.
     */
    protected $skipper_id;
    /**
     * @var PDOStatement Résultat de la requête select skipper_id from ...
     */
    protected $ligneSkipperId;
    /**
     * @var PDOStatement Résultat de la requête select edition_id from ...
     */
    protected $ligneEditionId;
    /**
     * @var bool true si l'itérator est valide.
     */
    protected $valid;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->valid = true;
    }

    /**
     * @inheritDoc;
     */
    public function current()
    {
        return $this->pdo->getPdo()->query("SELECT * from participe where edition_num 
= $this->edition_id and skipper_id = $this->skipper_id")->fetchObject('transat\AssociationParticipe');
    }

    /**
     * @inheritDoc;
     */
    public function next()
    {
        $fetchSkipperId = $this->ligneSkipperId->fetch();
        if ($fetchSkipperId == false) {
            $fetchEditionId = $this->ligneEditionId->fetch();
            if ($fetchEditionId == false) {
                $this->valid = false;
            } else {
                $this->edition_id = $fetchEditionId[0];
                $this->ligneSkipperId = $this->pdo
                    ->getPdo()
                    ->query("SELECT skipper_id from participe" .
                        " where edition_num = $this->edition_id order by skipper_id");
                $this->skipper_id = $this->ligneSkipperId->fetch()[0];
            }
        } else {
            $this->skipper_id = $fetchSkipperId[0];
        }
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return array($this->edition_id, $this->skipper_id);
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->valid;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->ligneEditionId = $this->pdo->getPdo()->query("SELECT edition_num from participe group by edition_num order by edition_num");
        $this->edition_id = ($this->ligneEditionId->fetch())[0];
        $this->ligneSkipperId = $this->pdo->getPdo()->query("SELECT skipper_id from participe where edition_num = $this->edition_id order by skipper_id");
        $this->skipper_id = ($this->ligneSkipperId->fetch())[0];
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->pdo->count();
    }

    /**
     * @return MyPDO
     */
    public function getPdo(): MyPDO
    {
        return $this->pdo;
    }
}