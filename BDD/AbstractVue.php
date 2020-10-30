<?php


namespace transat;

use LimitIterator;

abstract class AbstractVue
{

    protected $type = array(
        "integer" => "number",
        "text" => "text",
        "date" => "date",
    );

    /**
     * Le nombre d'enregistrements affiché dans une page.
     *
     * @var int
     */
    protected static $taillePage = 20;

    /**
     * Parcours l'itérator de la table pour afficher ses enregistrements.
     *
     * @param LimitIterator $pageCourante l'itérator de la table
     * @return string une chaîne de caractères contenant les enregistrements
     */
    protected abstract function parcoursIterator(LimitIterator $pageCourante): string;

    /**
     * Affiche tous les enregistrements de la table sous forme de page.
     *
     * @param \Iterator $it l'itérator de la table
     * @return string une page contenant les enregistrements
     */
    protected function getPagination(\Iterator $it): string
    {
        $pdo = $it->getPdo();
        $table = $pdo->getTypeTable() . "_" . $pdo->getNomTable();

        if (!isset($_SESSION['debut'])) {
            $_SESSION['debut'] = 1;
            $_SESSION['taillePage'] = self::$taillePage;
            $_SESSION['page'] = 1;
        }

        if (isset($_GET['page'])) {
            $_SESSION['page'] = $_GET['page'];
        }

        $_SESSION['debut'] = $_SESSION['page'] * $_SESSION['taillePage'] - $_SESSION['taillePage'] + 1;

        $maxPage = (int)((count($it) + $_SESSION['taillePage'] - 1) / $_SESSION['taillePage']);
        $decalagePrev = ($_SESSION['debut'] == 1) ? $_SESSION['page'] : $_SESSION['page'] - 1;
        $decalageNext = ($_SESSION['debut'] + $_SESSION['taillePage'] > count($it)) ? $_SESSION['page'] : $_SESSION['page'] + 1;

        $urlPrev = $_SERVER['PHP_SELF'] . "?table_name=" . $table . "&action=selectionnerTable&page=" . $decalagePrev;
        $urlNext = $_SERVER['PHP_SELF'] . "?table_name=" . $table . "&action=selectionnerTable&page=" . $decalageNext;

        $pageCourante = new LimitIterator($it, $_SESSION['debut'] - 1, $_SESSION['taillePage']);

        $contenu = "<h5 class='subtitle is-5'>**** " . $_SESSION['debut'] . " -- " . ($_SESSION['debut'] + $_SESSION['taillePage'] - 1) . " ****</h5>";

        $contenu .= $this->getNumeroPagination($urlPrev, $maxPage, $table, $urlNext);

        $contenu .= $this->parcoursIterator($pageCourante);

        $contenu .= $this->getNumeroPagination($urlPrev, $maxPage, $table, $urlNext);
        return $contenu;
    }

    /**
     * Crée les liens représentant les pages ainsi que leur numéro.
     *
     * @param string $urlPrev
     * @param int    $maxPage
     * @param string $table
     * @param string $urlNext
     * @return string
     */
    private function getNumeroPagination(string $urlPrev, int $maxPage, string $table, string $urlNext): string
    {
        $contenu = "<nav class=\"pagination is-centered\" role=\"navigation\" aria-label=\"pagination\">\n";
        $disabled = "";
        if ($_SESSION['page'] == 1)
            $disabled = "disabled";
        $contenu .= "<a class=\"pagination-previous\" href='$urlPrev' $disabled> &lt; </a>\n";
        $contenu .= "<ul class=\"pagination-list\" style='list-style-type: none;margin-left: auto;'>\n";
        for ($i = 1; $i <= $maxPage; $i++) {
            $url = $_SERVER['PHP_SELF'] . "?table_name=" . $table . "&action=selectionnerTable&page=" . $i;
            if ($i == $_SESSION['page']) {
                $contenu .= "<li style='margin-top: auto'><a class=\"pagination-link is-current\" aria-label=\"page $i\" aria-current=\"page\" href='$url'>$i</a></li>\n";
            } else
                $contenu .= "<li style='margin-top: auto'><a class=\"pagination-link\" aria-label=\"Goto page $i\" href='$url'>$i</a></li>\n";
        }
        $contenu .= "</ul>\n";
        $disabled = "";
        if ($_SESSION['page'] == $maxPage)
            $disabled = "disabled";
        $contenu .= "<a class=\"pagination-next\" href='$urlNext' $disabled> > </a>\n";
        $contenu .= "</nav>";
        return $contenu;
    }

    /**
     * Renvoie un lien hypertexte permettant d'annuler l'insertion ou la modification en cours.
     *
     * @param string $table
     * @return string
     */
    protected function lienAnnuler(string $table): string
    {
        return "<a class=\"input is-rounded is-danger has-text-danger has-text-weight-bold\" href='index.php?table_name=" . $table . "&action=selectionnerTable' class='has-text-danger'>Annuler</a>\n";
    }

    protected function getDebutForm(array $assoc, array $array): string
    {
        $ch = "<form action='" . $_SERVER['PHP_SELF'] . "' method='GET'>\n";
        foreach ($array as $key) {
            if (!isset($assoc[$key]) && isset($_GET[$key]))
                $ch .= "<input type='hidden' name='$key' value='" . $_GET[$key] . "'/>\n";
        }
        return $ch;
    }

    protected function getSubmitForm(string $nom_action): string
    {
        return "<input class=\"input is-rounded is-success has-text-success has-text-weight-bold\" style='cursor: pointer' type='submit' name='action' value='" . $nom_action . "'/>\n";
    }

    protected function getFinForm(string $nom_action, string $table): string
    {
        return getColumns(getColum($this->getSubmitForm($nom_action)) . getColum($this->lienAnnuler($table)),
            "is-mobile is-pulled-right");
    }

    function getInput($val, $col)
    {
        if (is_array($val))
            return "<input class=\"input is-info is-rounded has-text-centered\" name='$col' type='" . $this->type[$val['type']]
                . "' value='" . $val['default'] . "' />\n";
        return "<input class=\"input is-info is-rounded has-text-centered\" type='" . $this->type[$val] . "' name='$col'/>\n";
    }

    /**
     * Renvoie une liste correspondant aux enregistrements d'une clé étrangère.
     *
     * @param \MyPDO $pdo le pdo de la table
     * @param array  $val tableau contenant le nom de la table qui possède la clé étrangère et si il y a une valeur par
     *                    défaut
     * @param string $col le nom de la colonne de la table
     * @return string la liste d'enregistrements
     * @throws \ReflectionException
     */
    protected function getSelectForeignKey(\MyPDO $pdo, array $val, string $col): string
    {
        $pdo->setTypeTable('Entite');
        $ch = "<div class=\"select is-rounded is-info\">\n";
        $ch .= "<select name='$col'>\n";
        $pdo->setNomTable($val['select']);
        $it = new \MyIterator($pdo);
        foreach ($it as $id => $entite) {
            if (isset($val['default']) && $val['default'] == $id)
                $ch .= "<option value=$id selected>";
            else
                $ch .= "<option value=$id>";
            $ch .= $this->afficheValueForeignKey($entite);
            $ch .= "</option>";
        }
        $ch .= "</select>";
        $ch .= "</div>\n";
        return $ch;
    }

    /**
     * Affiche les informations concrètes d'une clé étrangère.
     *
     * @param        $entite l'entité contenant les informations concètes
     * @return string les informations
     */
    private function afficheValueForeignKey($entite): string
    {
        $ch = "";
        if ($entite instanceof \transat\EntiteSkipper) {
            $ch .= $entite->getSkipperNom() . " " . $entite->getSkipperPrenom() . " : " . $entite->getSkipperNationalite();
        } else if ($entite instanceof \transat\EntiteBateau) {
            $ch .= $entite->getBateauNom() . " : " . $entite->getBateauType();
        } else if ($entite instanceof \transat\EntiteEdition) {
            $ch .= $entite->getEditionNum();
        }
        return $ch;
    }
}