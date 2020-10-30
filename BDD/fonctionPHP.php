<?php

/**
 * @param array $tables
 * @param MyPDO $myPDO
 * @param array $colsValuesGET
 * @return void
 * @throws ReflectionException
 */
function cascadeSuppression(array $tables, MyPDO $myPDO, array $colsValuesGET)
{
    if ($_SESSION['type_table'] == 'Entite') {
        $classeActuel = new ReflectionClass("transat\\" . $_SESSION['type_table'] . ucfirst($_SESSION['table_name']));
        // ATTENTION si une entité possède plus d'une clé primaire le code ne marchera plus
        $colPK = $classeActuel->getStaticPropertyValue("PK")[0];
        foreach ($tables as $nomTable => $typeTable) {
            if ($typeTable == "Association") {
                $classeAvecFK = new ReflectionClass("transat\Association" . ucfirst($nomTable));
                $colNamesFK = $classeAvecFK->getStaticPropertyValue("FK");
                $tablesFK = $classeAvecFK->getStaticPropertyValue("TABLESFK");
                $colsFK = array_combine($colNamesFK, $tablesFK);
                foreach ($colsFK as $nomColFK => $nomTableFK) {
                    if ($_SESSION['table_name'] == $nomTableFK) {
                        $myPDO->setNomTable($nomTable);
                        $myPDO->setTypeTable($typeTable);
                        $myPDO->delete(array($nomColFK => $colsValuesGET[$colPK]));
                    }
                }
            }
        }
    }
}

/**
 * Initialise le type et le nom de la table dans myPDO.
 *
 * @param MyPDO $myPDO
 */
function initTableMyPDO(MyPDO $myPDO): void
{
    $myPDO->setNomTable($_SESSION['table_name']);
    $myPDO->setTypeTable($_SESSION['type_table']);
}

/**
 * Si un formulaire renvoie une valeur sous forme de tableau (cas pour la date et l'heure qui sont associées) on enlève
 * le tableau les contenant et on les concatène avec un espace entre eux.
 */
function formatageGET(): void
{
    foreach ($_GET as $key => $col) {
        if (is_array($col)) {
            $s = "";
            foreach ($col as $c)
                $s .= $c . " ";
            $s = substr($s, 0, strlen($s) - 1);
            if (substr($s, 0, 1) == " ")
                unset($_GET[$key]);
            else
                $_GET[$key] = $s;
        }
    }
}