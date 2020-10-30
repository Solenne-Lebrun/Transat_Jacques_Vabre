<?php

namespace transat;


use LimitIterator;

/**
 * Class VueBateau
 *
 */
class VueBateau extends AbstractVue
{

    /**
     * Production d'une chaîne de caractères contenant un formulaire HTML destiné à saisir un nouveau
     * bateau ou à modifier un bateau existant.
     *
     * @param array        $assoc      le tableau contenant des informations pour le formulaire
     * @param string       $nom_action le nom de l'action qui sera envoyé lors de la validation du formulaire
     * @param \MyPDO       $pdo        le pdo de la table
     * @param EntiteBateau $bateau
     * @return string le formulaire HTML
     */
    public function getForm4Entity(array $assoc, string $nom_action, \MyPDO $pdo, EntiteBateau $bateau = null): string
    {
        $nomCols = array("bateau_id", "bateau_nom", "bateau_type");
        $arrayForm = array();
        $formHTML = $this->getDebutForm($assoc, array('bateau_id'));

        if (isset($bateau))
            $arrayForm[] = getLabel($bateau->getBateauId(), "has-text-weight-normal");
        else
            $arrayForm[] = getLabel("", "has-text-weight-normal");

        foreach ($assoc as $col => $val) {
            if ($col == 'bateau_type') {
                $formSelect = "<div class=\"select is-rounded is-info\">\n<select name='$col'>\n";
                foreach (EntiteBateau::$TYPESBATEAU as $TYPEBATEAU) {
                    if (isset($val['default']) && $val['default'] == $TYPEBATEAU)
                        $formSelect .= "<option value='$TYPEBATEAU' selected>$TYPEBATEAU</option>\n";
                    else
                        $formSelect .= "<option value='$TYPEBATEAU'>$TYPEBATEAU</option>\n";
                }
                $formSelect .= "</select>\n" . "</div>\n";
                $arrayForm[] = $formSelect;
            } else {
                $arrayForm[] = $this->getInput($val, $col);
            }
        }

        $col = getColum(getLabel($nomCols[0]) . "\n" . $arrayForm[0]);
        $col .= getColum(getLabel($nomCols[1]) . "\n" . $arrayForm[1]);
        $formHTML .= getColumns($col, "is-desktop");
        $col = getColum(getLabel($nomCols[2]) . "\n" . $arrayForm[2]);
        $formHTML .= getContent(getColumns($col, "is-desktop"));

        if ($nom_action == "modifierEntité" || $nom_action == "sauverEntité")
            $action = "Modifier";
        else
            $action = "Insérer";
        $formHTML .= $this->getFinForm($nom_action, "Entite_bateau");
        return getBox("$action Bateau", $formHTML);
    }

    /**
     * Production d'une chaîne de caractères contenant un tableau HTML représentant un ensemble de bateaux
     * et permettant de les modifier ou de les supprimer grace à un lien hypertexte.
     *
     * @param \MyPDO $pdo le pdo utilisé pour parcourir les enregistrements de la table Bateau
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
        $tableauHTML .= "<table class=\"table is-striped is-hoverable \" border='1'>\n";
        $tableauHTML .= "<tr><th>bateau_id</th><th>bateau_nom</th><th>bateau_type</th><th colspan='2'/></tr>\n";

        foreach ($pageCourante as $bateau) {
            if ($bateau instanceof EntiteBateau) {
                $tableauHTML .= "<tr>";
                $tableauHTML .= "<td>" . $bateau->getBateauId() . "</td> ";
                $tableauHTML .= "<td>" . $bateau->getBateauNom() . "</td> ";
                $tableauHTML .= "<td>" . $bateau->getBateauType() . "</td> ";
                $tableauHTML .= "<td>" . "<a href='?action=modifierEntité&bateau_id=" . $bateau->getBateauId() . "'>Modifier</a></td> ";
                $tableauHTML .= "<td>" . "<a href='?action=supprimerEntité&bateau_id=" . $bateau->getBateauId() . "'>Supprimer</a></td> ";
                $tableauHTML .= "</tr>\n";
            }
        }

        $tableauHTML .= "</table></div>\n";
        return $tableauHTML;
    }
}
