<?php

namespace transat;

use LimitIterator;

class VueSkipper extends AbstractVue
{
    /**
     * Production d'une chaîne de caractères contenant un formulaire HTML destiné à saisir un nouveau
     * skipper ou à modifier un skipper existant.
     *
     * @param array         $assoc      le tableau contenant des informations pour le formulaire
     * @param string        $nom_action le nom de l'action qui sera envoyé lors de la validation du formulaire
     * @param \MyPDO        $pdo        le pdo de la table
     * @param EntiteSkipper $skipper
     * @return string le formulaire HTML
     */
    public function getForm4Entity(array $assoc, string $nom_action, \MyPDO $pdo, EntiteSkipper $skipper = null): string
    {
        $nomCols = array("skipper_id", "skipper_nom", "skipper_prenom", "skipper_nationalite");
        $arrayForm = array();

        $formHTML = $this->getDebutForm($assoc, array('skipper_id'));

        if (isset($skipper)) {
            $arrayForm[] = getLabel($skipper->getSkipperId(), "has-text-weight-normal");
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
        $formHTML .= $this->getFinForm($nom_action, "Entite_skipper");
        return getBox("$action Skipper", $formHTML);
    }

    /**
     * Production d'une chaîne de caractères contenant un tableau HTML représentant un ensemble de skippers
     * et permettant de les modifier ou de les supprimer grace à un lien hypertexte.
     *
     * @param \MyPDO $pdo le pdo utilisé pour parcourir les enregistrements de la table Skipper
     * @return string le tableau HTML
     * @throws \ReflectionException
     */
    public function getAllEntities(\MyPDO $pdo): string
    {
        return $this->getPagination(new \MyIterator($pdo));
    }

    /**
     * {@inheritDoc}
     */
    protected function parcoursIterator(LimitIterator $pageCourante): string
    {
        $tableauHTML = "<div class=\"table-container\">";
        $tableauHTML .= "<table class=\"table is-striped is-hoverable\" border='1'>\n";
        $tableauHTML .= "<tr><th>skipper_id</th><th>skipper_nom</th><th>skipper_prenom</th><th>skipper_nationalite</th><th colspan='2'/></tr>\n";

        foreach ($pageCourante as $skipper) {
            if ($skipper instanceof EntiteSkipper) {
                $tableauHTML .= "<tr>";
                $tableauHTML .= "<td>" . $skipper->getSkipperId() . "</td> ";
                $tableauHTML .= "<td>" . $skipper->getSkipperNom() . "</td> ";
                $tableauHTML .= "<td>" . $skipper->getSkipperPrenom() . "</td> ";
                $tableauHTML .= "<td>" . $skipper->getSkipperNationalite() . "</td> ";
                $tableauHTML .= "<td>" . "<a href='?action=modifierEntité&skipper_id=" . $skipper->getSkipperId() . "'>Modifier</a></td> ";
                $tableauHTML .= "<td>" . "<a href='?action=supprimerEntité&skipper_id=" . $skipper->getSkipperId() . "'>Supprimer</a></td> ";
                $tableauHTML .= "</tr>\n";
            }
        }

        $tableauHTML .= "</table></div>\n";
        return $tableauHTML;
    }
}
