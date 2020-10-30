<?php


namespace transat;


use LimitIterator;

class VueEdition extends AbstractVue
{
    /**
     * Production d'une chaîne de caractères contenant un formulaire HTML destiné à saisir une nouvelle
     * edition ou à modifier une edition existante.
     *
     * @param array         $assoc      le tableau contenant des informations pour le formulaire
     * @param string        $nom_action le nom de l'action qui sera envoyé lors de la validation du formulaire
     * @param \MyPDO        $pdo        le pdo de la table
     * @param EntiteEdition $edition
     * @return string le formulaire HTML
     */
    public function getForm4Entity(array $assoc, string $nom_action, \MyPDO $pdo, EntiteEdition $edition = null): string
    {
        $nomCols = array("edition_num", "edition_date_depart", "edition_port_depart", "edition_port_arrivee");
        $arrayForm = array();

        $formHTML = $this->getDebutForm($assoc, array('edition_num'));

        if (isset($edition)) {
            $arrayForm[] = getLabel($edition->getEditionNum(), "has-text-weight-normal");
        } else {
            $arrayForm[] = getLabel("", "has-text-weight-normal");
        }

        foreach ($assoc as $col => $val) {
            $arrayForm[] = $this->getInput($val, $col);
        }

        $col = getColum(getLabel($nomCols[0]) . "\n" . $arrayForm[0]);
        $col .= getColum(getLabel($nomCols[1]) . "\n" . $arrayForm[1]);
        $formHTML .= getColumns($col, "is-desktop");
        $col = getColum(getLabel($nomCols[2]) . "\n" . $arrayForm[2]);
        $col .= getColum(getLabel($nomCols[3]) . "\n" . $arrayForm[3]);
        $formHTML .= getContent(getColumns($col, "is-desktop"));

        if ($nom_action == "modifierEntité" || $nom_action == "sauverEntité")
            $action = "Modifier";
        else
            $action = "Insérer";
        $formHTML .= $this->getFinForm($nom_action, "Entite_edition");
        return getBox("$action Edition", $formHTML);
    }

    /**
     * Production d'une chaîne de caractères contenant un tableau HTML représentant un ensemble d'éditions
     * et permettant de les modifier ou de les supprimer grace à un lien hypertexte.
     *
     * @param \MyPDO $pdo le pdo utilisé pour parcourir les enregistrements de la table Edition
     * @return string le tableau HTML
     * @throws \ReflectionException
     */
    public function getAllEntities(\MyPDO $pdo): string
    {
        return $this->getPagination(new \MyIterator($pdo));
    }

    /**
     * @inheritDoc
     */
    protected function parcoursIterator(LimitIterator $pageCourante): string
    {
        $tableauHTML = "<div class=\"table-container\">";
        $tableauHTML .= "<table class=\"table is-striped is-hoverable\" border='1'>\n";
        $tableauHTML .= "<tr><th>edition_num</th><th>edition_date_depart</th><th>edition_port_depart</th>
                <th>edition_port_arrivee</th><th colspan='2'/></tr>\n";

        foreach ($pageCourante as $edition) {
            if ($edition instanceof EntiteEdition) {
                $tableauHTML .= "<tr>";
                $tableauHTML .= "<td>" . $edition->getEditionNum() . "</td> ";
                $tableauHTML .= "<td>" . $edition->getEditionDateDepart() . "</td> ";
                $tableauHTML .= "<td>" . $edition->getEditionPortDepart() . "</td> ";
                $tableauHTML .= "<td>" . $edition->getEditionPortArrivee() . "</td> ";
                $tableauHTML .= "<td>" . "<a href='?action=modifierEntité&edition_num=" . $edition->getEditionNum() . "'>Modifier</a></td> ";
                $tableauHTML .= "<td>" . "<a href='?action=supprimerEntité&edition_num=" . $edition->getEditionNum() . "'>Supprimer</a></td> ";
                $tableauHTML .= "</tr>\n";
            }
        }

        $tableauHTML .= "</table></div>\n";
        return $tableauHTML;
    }
}
