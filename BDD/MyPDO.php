<?php


class MyPDO
{
    /**
     * @var PDO
     */
    private $pdo;
    /**
     * @var PDOStatement
     */
    private $pdos_selectAll;
    /**
     * @var PDOStatement
     */
    private $pdos_select;
    /**
     * @var PDOStatement
     */
    private $pdos_update;
    /**
     * @var PDOStatement
     */
    private $pdos_insert;
    /**
     * @var PDOStatement
     */
    private $pdos_delete;
    /**
     * @var PDOStatement
     */
    private $pdos_count;

    /**
     * @var string
     */
    private $nomTable;

    private $typeTable;


    /**
     * MyPDO constructor.
     *
     * @param $sgbd
     * @param $host
     * @param $db
     * @param $user
     * @param $password
     * @param $nomTable
     */
    public function __construct($sgbd, $host, $db, $user, $password)
    {
        switch ($sgbd) {
            case "mysql":
                $this->pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $password);
                break;
            case "pgsql":
                $this->pdo = new PDO("pgsql:host=" . $host . " dbname=" . $db . " user=" . $user
                    . " password=" . $password);
                break;
            default:
                exit;

        }

        // pour récupérer aussi les exceptions provenant de PDOStatement
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function setNomTable(string $nom)
    {
        $this->nomTable = $nom;
    }

    public function setTypeTable(string $type)
    {
        $this->typeTable = $type;
    }

    /**
     * préparation de la requête SELECT * FROM $nomTable
     * instantiation de $this->pdos_selectAll
     */
    public function initPDOS_selectAll()
    {
        $this->pdos_selectAll = $this->pdo->prepare('SELECT * FROM ' . $this->nomTable);
    }

    /**
     * Suppose une convention de nommage de la classe entité et de son namespace !!
     *
     * @return array
     */
    public function getAll(): array
    {
        $this->initPDOS_selectAll();
        $this->getPdosSelectAll()->execute();
        return $this->getPdosSelectAll()->fetchAll(PDO::FETCH_CLASS,
            "transat\\" . $this->typeTable . ucfirst($this->getNomTable()));
    }

    /**
     * préparation de la requête SELECT * FROM $this->nomTable WHERE $nomColId = :id
     * instantiation de $this->pdos_select
     *
     * @param array  $nomColsId
     * @param string $nomColSelect
     */
    public function initPDOS_select(array $nomColsId, string $nomColSelect = '*'): void
    {

        $requete = "SELECT " . $nomColSelect . " FROM " . $this->nomTable
            . " WHERE ";
        foreach ($nomColsId as $nomColId) {
            $requete .= "$nomColId=:" . $nomColId . " and ";
        }
        $requete = substr($requete, 0, strlen($requete) - 5);
        $this->pdos_select = $this->pdo->prepare($requete);
    }

    /**
     * Suppose une convention de nommage de la classe entité et de son namespace !!
     *
     * @param array  $colsValues
     * @param string $nomColSelect
     * @return mixed
     */
    public function get(array $colsValues, string $nomColSelect = '*')
    {
        $this->initPDOS_select(array_keys($colsValues), $nomColSelect);
        foreach ($colsValues as $key => $val)
            $this->getPdosSelect()->bindValue(":" . $key, $val);
        $this->getPdosSelect()->execute();
        if ($nomColSelect == '*')
            return $this->getPdosSelect()
                ->fetchObject("transat\\" . $this->typeTable . ucfirst($this->getNomTable()));
        else
            return $this->getPdosSelect()->fetch();
    }

    /**
     * @param string $nomColId
     * @param array  $colNames
     */
    public function initPDOS_update(array $nomColsId, array $colNames): void
    {
        $query = "UPDATE " . $this->nomTable . " SET ";
        foreach ($colNames as $colName) {
            $query .= $colName . "=:" . $colName . ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= " WHERE ";
        foreach ($nomColsId as $nomColId)
            $query .= $nomColId . "=:" . $nomColId . " and ";
        $query = substr($query, 0, strlen($query) - 5);
        $this->pdos_update = $this->pdo->prepare($query);
    }

    /**
     * @param string $id
     * @param array  $assoc
     */
    public function update(array $id, array $assoc): void
    {
        $this->initPDOS_update($id, array_keys($assoc));
        foreach ($assoc as $key => $value) {
            $this->getPdosUpdate()->bindValue(":" . $key, $value);
        }
        $this->getPdosUpdate()->execute();
    }

    /**
     * @param array
     */
    public function initPDOS_insert(array $colNames): void
    {
        $query = "INSERT INTO " . $this->nomTable . "(";
        foreach ($colNames as $colName) {
            $query .= $colName . ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ") VALUES(";
        foreach ($colNames as $colName) {
            $query .= ":" . $colName . ", ";
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ')';
        $this->pdos_insert = $this->pdo->prepare($query);
    }

    /**
     * @param array $assoc
     */
    public function insert(array $assoc): void
    {
        $this->initPDOS_insert(array_keys($assoc));
        foreach ($assoc as $key => $value) {
            $this->getPdosInsert()->bindValue(":" . $key, $value);
        }
        $this->getPdosInsert()->execute();
    }

    /**
     * @param string
     */
    public function initPDOS_delete(array $nomColsId): void
    {
        $query = "DELETE FROM " . $this->nomTable
            . " WHERE ";
        foreach ($nomColsId as $nomColId) {
            $query .= "$nomColId=:" . $nomColId . " and ";
        }
        $query = substr($query, 0, strlen($query) - 5);
        $this->pdos_delete = $this->pdo->prepare($query);
    }

    /**
     * @param array $assoc
     */
    public function delete(array $assoc)
    {
        $this->initPDOS_delete(array_keys($assoc));
        foreach ($assoc as $key => $value) {
            $this->getPdosDelete()->bindValue(":" . $key, $value);
        }
        $this->getPdosDelete()->execute();
    }

    /**
     * préparation de la requête SELECT COUNT(*) FROM
     * instantiation de self::$_pdos_count
     */
    public function initPDOS_count()
    {
        $this->pdos_count = $this->pdo->prepare('SELECT COUNT(*) FROM ' . $this->nomTable);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $this->initPDOS_count();
        $this->getPdosCount()->execute();
        $resu = $this->getPdosCount()->fetch();
        return $resu[0];
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * @return PDOStatement
     */
    public function getPdosSelect(): PDOStatement
    {
        return $this->pdos_select;
    }


    /**
     * @return PDOStatement
     */
    public function getPdosSelectAll(): PDOStatement
    {
        return $this->pdos_selectAll;
    }

    /**
     * @return PDOStatement
     */
    public function getPdosUpdate(): PDOStatement
    {
        return $this->pdos_update;
    }

    /**
     * @return PDOStatement
     */
    public function getPdosInsert(): PDOStatement
    {
        return $this->pdos_insert;
    }

    /**
     * @return PDOStatement
     */
    public function getPdosDelete(): PDOStatement
    {
        return $this->pdos_delete;
    }

    /**
     * @return PDOStatement
     */
    public function getPdosCount(): PDOStatement
    {
        return $this->pdos_count;
    }

    /**
     * @return string
     */
    public function getNomTable(): string
    {
        return $this->nomTable;
    }

    /**
     * @return mixed
     */
    public function getTypeTable()
    {
        return $this->typeTable;
    }

}