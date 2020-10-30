<?php

class MyIterator implements Iterator, Countable
{
    /**
     * @var string Le nom de la table.
     */
    protected $nom_table;
    /**
     * Le pdo de la table
     * @var MyPDO
     */
    protected $pdo_table;
    /**
     * Représente l'id de la table.
     * @var int
     */
    protected $id_table;
    /**
     * Représente le nom de la colonne associé à l'id de la table.
     * @var string
     */
    protected $nom_id;
    /**
     * @var PDOStatement Résultat de la requête select nom_table_id from nom_table.
     */
    protected $ligneTable;
    /**
     * @var bool true si l'itérator est valide.
     */
    protected $valid;

    /**
     * Constructeur MyIterator.
     * @param MyPDO $pdo_table le pdo de la table
     * @throws ReflectionException
     */
    public function __construct(MyPDO $pdo_table)
    {
        $this->pdo_table = $pdo_table;
        $this->nom_table = $this->pdo_table->getNomTable();
        $classe = new ReflectionClass('transat\Entite' . ucfirst($this->nom_table));
        $this->nom_id = $classe->getStaticPropertyValue('PK')[0];
        $this->valid = true;
    }

    /**
     * Renvoie le nombre d'objets parcourable par l'itérator.
     * @return int le nombre d'objets
     */
    public function count()
    {
        return $this->pdo_table->count();
    }

    /**
     * Renvoie une instance de la classe représentant un enregistrement la table.
     * @return mixed l'instance
     */
    public function current()
    {
        $this->pdo_table->initPDOS_select(array($this->nom_id));
        return $this->pdo_table->get(array($this->nom_id => $this->id_table));
    }

    /**
     * Renvoie l'id courant.
     * @return int l'id courant
     */
    public function key()
    {
        return $this->id_table;
    }

    /**
     * Change l'id courant par l'id de la ligne suivante dans select nom_table_id from nom_table.
     */
    public function next()
    {
        $fetchLigneTable = $this->ligneTable->fetch();
        if ($fetchLigneTable == false)
            $this->valid = false;
        else
            $this->id_table = $fetchLigneTable[0];
    }

    /**
     * Reset l'id courant.
     */
    public function rewind()
    {
        $this->ligneTable = $this->pdo_table->getPdo()->query("select $this->nom_id from $this->nom_table order by $this->nom_id");
        $this->next();
    }

    /**
     * Vérifie si l'id courant est valide.
     * @return bool true si l'id courant est valide
     */
    public function valid()
    {
        return $this->valid;
    }

    /**
     * @return MyPDO
     */
    public function getPdo(): MyPDO
    {
        return $this->pdo_table;
    }
}

?>
