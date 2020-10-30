<?php


class MyIteratorArrive implements Iterator, Countable
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
     * @var int Représente l'id courant de la table Bateau.
     */
    protected $bateau_id;
    /**
     * @var PDOStatement Résultat de la requête select bateau_id from ...
     */
    protected $ligneBateauId;
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
        return $this->pdo->getPdo()->query("SELECT * from arrive where edition_num 
= $this->edition_id and bateau_id = $this->bateau_id")->fetchObject('transat\AssociationArrive');
    }

    /**
     * @inheritDoc;
     */
    public function next()
    {
        $fetchBateauId = $this->ligneBateauId->fetch();
        if ($fetchBateauId == false) {
            $fetchEditionId = $this->ligneEditionId->fetch();
            if ($fetchEditionId == false) {
                $this->valid = false;
            } else {
                $this->edition_id = $fetchEditionId[0];
                $this->ligneBateauId = $this->pdo
                    ->getPdo()
                    ->query("SELECT bateau_id from arrive" .
                        " where edition_num = $this->edition_id order by bateau_id");
                $this->bateau_id = $this->ligneBateauId->fetch()[0];
            }
        } else {
            $this->bateau_id = $fetchBateauId[0];
        }
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return array($this->edition_id, $this->bateau_id);
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
        $this->ligneEditionId = $this->pdo->getPdo()->query("SELECT edition_num from arrive group by edition_num order by edition_num");
        $this->edition_id = ($this->ligneEditionId->fetch())[0];
        $this->ligneBateauId = $this->pdo->getPdo()->query("SELECT bateau_id from arrive where edition_num = $this->edition_id order by bateau_id");
        $this->bateau_id = ($this->ligneBateauId->fetch())[0];
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