<?php

namespace transat;


use LimitIterator;

class VueParticipe extends AbstractVue
{
    /**
     * Production d'une chaîne de caractères contenant un formulaire HTML destiné à saisir un nouvel
     * enregistrement de la table Participe ou à en modifier un existant.
     *
     * @param array                $assoc      le tableau contenant des informations pour le formulaire
     * @param string               $nom_action le nom de l'action qui sera envoyé lors de la validation du formulaire
     * @param \MyPDO               $pdo        le pdo de la table
     * @param AssociationParticipe $participe
     * @return string le formulaire HTML
     * @throws \ReflectionException
     */
    public function getForm4Entity(array $assoc, string $nom_action, \MyPDO $pdo, AssociationParticipe $participe = null): string
    {
        $pdo->setTypeTable('Entite');
        $nomCols = array("Numéro Édition", "Skipper", "Co_Skipper", "Nom Bateau");
        $arrayForm = array();

        $formHTML = $this->getDebutForm($assoc, array('edition_num', 'skipper_id'));

        if (isset($participe)) {
            $arrayForm[] = getLabel($participe->getEditionNum(), "has-text-weight-normal");
            $pdo->setNomTable('skipper');
            $skipper = $pdo->get(array('skipper_id' => $participe->getSkipperId()));
            $arrayForm[] = getLabel($skipper->getSkipperNom() . " " . $skipper->getSkipperPrenom(), "has-text-weight-normal");
        }

        foreach ($assoc as $col => $val) {
            if (is_array($val)) {
                if (isset($val['select'])) {
                    $arrayForm[] = $this->getSelectForeignKey($pdo, $val, $col);
                }
            }
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
        $formHTML .= $this->getFinForm($nom_action, "Association_participe");
        return getBox("$action Participe", $formHTML);
    }

    /**
     * Production d'une chaîne de caractères contenant un tableau HTML représentant un ensemble d'enregistrements de la
     * table Participe et permettant de les modifier ou de les supprimer grace à un lien hypertexte.
     *
     * @param \MyPDO $pdo le pdo utilisé pour parcourir les enregistrements de la table Participe
     * @return string le tableau HTML
     */
    public function getAllEntities(\MyPDO $pdo): string
    {
        return $this->getPagination(new \MyIteratorParticipe($pdo));
    }

    /**
     * @inheritDoc
     */
    protected function parcoursIterator(LimitIterator $pageCourante): string
    {
        $pdo = $pageCourante->getPdo();
        $pdo->setTypeTable('Entite');
        $tableauHTML = "<div class=\"table-container\">";
        $tableauHTML .= "<table class=\"table is-stiped is-hoverable\" border='1'>\n";
        $tableauHTML .= "<tr><th>Numéro Édition</th><th>Skipper</th>" .
            "<th>Co_Skipper</th><th>Nom Bateau</th><th colspan='2'/></tr>\n";

        foreach ($pageCourante as $participe) {
            if ($participe instanceof AssociationParticipe) {
                $tableauHTML .= "<tr>";
                $tableauHTML .= "<td>" . $participe->getEditionNum() . "</td>";
                $pdo->setNomTable('skipper');
                $pdo->initPDOS_select(array('skipper_id'));
                $skipper = $pdo->get(array('skipper_id' => $participe->getSkipperId()));
                $tableauHTML .= "<td>" . $skipper->getSkipperNom() . " ";
                $tableauHTML .= "" . $skipper->getSkipperPrenom() . "</td>";
                $skipper = $pdo->get(array('skipper_id' => $participe->getCoSkipperId()));
                $tableauHTML .= "<td>" . $skipper->getSkipperNom() . " ";
                $tableauHTML .= "" . $skipper->getSkipperPrenom() . "</td>";
                $pdo->setNomTable('bateau');
                $pdo->initPDOS_select(array('bateau_id'));
                $bateau = $pdo->get(array('bateau_id' => $participe->getBateauId()));
                $tableauHTML .= "<td>" . $bateau->getBateauNom() . " ";
                $tableauHTML .= "<td>" . "<a href='?action=modifierEntité&edition_num=" . $participe->getEditionNum()
                    . "&skipper_id=" . $participe->getSkipperId() . "'>Modifier</a> " . "</td>";
                $tableauHTML .= "<td>" . "<a href='?action=supprimerEntité&edition_num=" . $participe->getEditionNum()
                    . "&skipper_id=" . $participe->getSkipperId() . "'>Supprimer</a> " . "</td>";
                $tableauHTML .= "</tr>\n";
            }
        }

        $tableauHTML .= "</table></div>\n";
        return $tableauHTML;
    }
}
