<?php


namespace transat;


use LimitIterator;

class VueArrive extends AbstractVue
{

    /**
     * Production d'une chaîne de caractères contenant un formulaire HTML destiné à saisir un nouvel
     * enregistrement de la table Arrive ou à en modifier un existant.
     *
     * @param array             $assoc      le tableau contenant des informations pour le formulaire
     * @param string            $nom_action le nom de l'action qui sera envoyé lors de la validation du formulaire
     * @param \MyPDO            $pdo        le pdo de la table
     * @param AssociationArrive $arrive
     * @return string le formulaire HTML
     * @throws \ReflectionException
     */
    public function getForm4Entity(array $assoc, string $nom_action, \MyPDO $pdo, AssociationArrive $arrive = null): string
    {
        $pdo->setTypeTable('Entite');
        $nomCols = array("Numéro Édition", "Nom Bateau", "Date d'arrivée");
        $arrayForm = array();

        $formHTML = $this->getDebutForm($assoc, array('edition_num', 'bateau_id'));

        if (isset($arrive)) {
            $arrayForm[] = getLabel($arrive->getEditionNum(), "has-text-weight-normal");
            $pdo->setNomTable('bateau');
            $bateau = $pdo->get(array('bateau_id' => $arrive->getBateauId()));
            $arrayForm[] = getLabel($bateau->getBateauNom(), "has-text-weight-normal");
        }

        foreach ($assoc as $col => $val) {
            if (is_array($val)) {
                if (isset($val['select'])) {
                    $arrayForm[] = $this->getSelectForeignKey($pdo, $val, $col);
                } else {
                    if ($val['type'] == 'date') {
                        $arrayForm[] = $this->getDateWithTime($col, $val);
                    }
                }
            } elseif ($val == 'date') {
                $arrayForm[] = $this->getDateWithTime($col);
            } else {
                $arrayForm[] = $this->getInput($val, $col);
            }
        }

        $col = getColum(getLabel($nomCols[0]) . "\n" . $arrayForm[0]);
        $col .= getColum(getLabel($nomCols[1]) . "\n" . $arrayForm[1]);
        $formHTML .= getColumns($col, "is-desktop");
        $col = getColum(getLabel($nomCols[2]) . "\n" . $arrayForm[2], "");
        $formHTML .= getContent(getColumns($col, "is-desktop"));

        if ($nom_action == "modifierEntité" || $nom_action == "sauverEntité")
            $action = "Modifier";
        else
            $action = "Insérer";
        $formHTML .= $this->getFinForm($nom_action, "Association_arrive");
        return getBox("$action Arrive", $formHTML);
    }

    /**
     * Production d'une chaîne de caractères contenant un tableau HTML représentant un ensemble d'enregistrements de la
     * table Arrive et permettant de les modifier ou de les supprimer grace à un lien hypertexte.
     *
     * @param \MyPDO $pdo le pdo utilisé pour parcourir les enregistrements de la table Arrive
     * @return string le tableau HTML
     */
    public function getAllEntities(\MyPDO $pdo): string
    {
        return $this->getPagination(new \MyIteratorArrive($pdo));
    }

    /**
     * @inheritDoc
     */
    protected function parcoursIterator(LimitIterator $pageCourante): string
    {
        $pdo = $pageCourante->getPdo();
        $pdo->setTypeTable('Entite');
        $tableauHTML = "<div class=\"table-container\">";
        $tableauHTML .= "<table class=\"table is-striped is-hoverable\" border='1'>\n";
        $tableauHTML .= "<tr><th>Numéro Édition</th><th>Nom Bateau</th>" .
            "<th>Date d'arrivée</th><th colspan='2'/></tr>\n";

        foreach ($pageCourante as $arrive) {
            if ($arrive instanceof AssociationArrive) {
                $tableauHTML .= "<tr>";
                $tableauHTML .= "<td>" . $arrive->getEditionNum() . "</td>";
                $pdo->setNomTable('bateau');
                $pdo->initPDOS_select(array('bateau_id'));
                $bateau = $pdo->get(array('bateau_id' => $arrive->getBateauId()));
                $tableauHTML .= "<td>" . $bateau->getBateauNom() . " ";
                $tableauHTML .= "<td>" . $arrive->getDateArrivee() . " ";
                $tableauHTML .= "<td>" . "<a href='?action=modifierEntité&edition_num=" . $arrive->getEditionNum()
                    . "&bateau_id=" . $arrive->getBateauId() . "'>Modifier</a> " . "</td>";
                $tableauHTML .= "<td>" . "<a href='?action=supprimerEntité&edition_num=" . $arrive->getEditionNum()
                    . "&bateau_id=" . $arrive->getBateauId() . "'>Supprimer</a> " . "</td>";
                $tableauHTML .= "</tr>\n";
            }
        }

        $tableauHTML .= "</table></div>\n";
        return $tableauHTML;
    }

    /**
     * @param array  $val
     * @param        $col
     * @param string $ch
     * @return string
     */
    private function getDateWithTime(string $col, array $val = null): string
    {
        if (isset($val['default']) && $val['default'] != "null")
            $explodeVal = explode(' ', $val['default']);
        else
            $explodeVal = array("", "");
        $ch = "<div class=\"field is-horizontal\">\n";
        $ch .= "<input class=\"input is-info has-text-centered\" name='$col" . "[]' type='date'"
            . " value='" . $explodeVal[0] . "' />\n<span style='margin-right: 6px;margin-left: 6px;'></span>";
        $ch .= "<input class=\"input is-info has-text-centered\" name='$col" . "[]' type='time'"
            . " value='" . $explodeVal[1] . "' />\n";
        $ch .= "</div>";
        return $ch;
    }
}
