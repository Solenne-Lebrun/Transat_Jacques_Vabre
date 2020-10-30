<?php
function getDebutHTML(): string
{
    return "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Base de donn&eacute;es Transat Jacques-Vabre</title>
    <link rel=\"stylesheet\" href=\"../bulma-start-0.0.3/css/main.css\">
</head>
<body>

<section class=\"hero is-bold is-primary is-small\">
    <div class=\"hero-body\">
        <div class=\"container\">
            <div class=\"columns \">
                <div class=\"column is-one-quarter-desktop is-two-thirds-mobile is-one-third-tablet\" style='margin: auto'>
                    <figure class=\"image\" style='margin: auto'>
                        <img src=\"../bulma-start-0.0.3/Images/logo-tjv-2019.svg\" alt=\"Logo_Transat_Jacques_Vabre_2019\">
                    </figure>
                </div>
                <div class=\"column is-8-desktop is-offset-2-desktop\">
                    <h1 class=\"title is-spaced is-size-1 is-size-3-mobile\">
                        Base de donn&eacute;es Transat Jacques-Vabre
                    </h1>
                    <h2 class=\"subtitle is-size-3 is-size-4-mobile\">
                        <strong>&Eacute;ditions 2017 & 2019</strong>
                    </h2>
                </div>
            </div>
        </div>
    </div>
</section>

<section class=\"section\">
    <div class=\"container\">
        <div class=\"columns\" style='min-height: 100%'>
        ";
}

function getFinHTML(): string
{
    return "
</div>
</div>
</section>
<footer class=\"footer has-text-centered\">
    <div class=\"container\">
        <div class=\"columns\">
            <div class=\"column is-8-desktop is-offset-2-desktop\">
                <p class='has-text-black has-text-weight-bold'>Grégoire AVENEL &#8226; Mohammed CHARAOUI &#8226; Niels KERNÉ &#8226; Solenne LEBRUN</p>
                <p class='has-text-grey-darker'><br>Université du Havre<br>L3 Info - InfoWeb 2019/2020</p>
            </div>
        </div>
    </div>
</footer>
<script type=\"text/javascript\" src=\"../bulma-start-0.0.3/lib/main.js\"></script>
</body>
</html>
";
}

function getNavBar(){
    return "
<nav class=\"navbar is-hidden-desktop is-hidden-tablet\" role=\"navigation\" aria-label=\"main navigation\">
    <a role=\"button\" class=\"navbar-burger\" aria-label=\"menu\" aria-expanded=\"false\" data-target=\"navMenu\">
        <span aria-hidden=\"true\"></span>
        <span aria-hidden=\"true\"></span>
        <span aria-hidden=\"true\"></span>
    </a>

    <div id=\"navMenu\" class=\"navbar-menu\">
        <div class=\"navbar-start\">

            <div class=\"navbar-item has-dropdown is-hoverable\">
                <a class=\"navbar-link\">
                    Modifier une table
                </a>

                <div class=\"navbar-dropdown\">
                    <a class=\"navbar-item\" href=\"index.php?table_name=Entite_bateau&action=selectionnerTable\">
                        Bateau
                    </a>
                    <hr class=\"navbar-divider\">
                    <a class=\"navbar-item\" href=\"index.php?table_name=Entite_skipper&action=selectionnerTable\">
                        Skipper
                    </a>
                    <hr class=\"navbar-divider\">
                    <a class=\"navbar-item\" href=\"index.php?table_name=Entite_edition&action=selectionnerTable\">
                        Edition
                    </a>
                </div>
            </div>

            <div class=\"navbar-item has-dropdown is-hoverable\">
                <a class=\"navbar-link\">
                    Modifier une association
                </a>

                <div class=\"navbar-dropdown\">
                    <a class=\"navbar-item\" href=\"index.php?table_name=Association_participe&action=selectionnerTable\">
                        Participe
                    </a>
                    <hr class=\"navbar-divider\">
                    <a class=\"navbar-item\" href=\"index.php?table_name=Association_arrive&action=selectionnerTable\">
                        Arrive
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
";
}


function getDebutColumn(): string
{
    return "
<div class=\"column has-text-centered\">
<div class=\"content\">
<h5 class='title is-size-3' style='margin-bottom: 50px'><a href=\"index.php\">Accueil</a></h5>
";
}

function getFinColumn(): string
{
    return "
</div>
</div>
";
}

function getListeTables(array $tables): string
{
    $listeEntites = "";
    $listeAssociations = "";
    foreach ($tables as $nomTable => $typeTable) {
        if ($typeTable == "Entite")
            $listeEntites .= "<li><a href=\"index.php?table_name=" . $typeTable . "_" . $nomTable . "&action=selectionnerTable\">" . ucfirst($nomTable) . "</a></li>\n";
        else
            $listeAssociations .= "<li><a href=\"index.php?table_name=" . $typeTable . "_" . $nomTable . "&action=selectionnerTable\">" . ucfirst($nomTable) . "</a></li>\n";
    }
    $contenu = "<div class=\"column is-one-quarter is-hidden-mobile\">
                <aside class=\"menu\">
                  <p class=\"menu-label\"> MODIFIER UNE TABLE </p>
                  <ul class=\"menu-list\">\n";
    $contenu .= $listeEntites;
    $contenu .= "</ul>
                  <p class=\"menu-label\">MODIFIER UNE ASSOCIATION</p>
                  <ul class=\"menu-list\">\n";
    $contenu .= $listeAssociations;
    $contenu .= "</ul>
                </aside>
              </div>";
    return $contenu;
}

function getContenuAccueil(): string
{
    $contenu = "<div class=\"column\">\n<div class=\"content\">\n";
    $contenu .= "<h1 class=\"title is-size-1 is-size-3-mobile\">Bienvenue !</h1>";
    $contenu .= "<p>La <strong>Transat Jacques Vabre</strong> est une course transatlantique en duo partant du Havre. Elle emprunte la c&eacute;l&egrave;bre route
                du caf&eacute;, route historique entre La France et le Br&eacute;sil. Cette course se d&eacute;roule tous les deux
                ans depuis 1993, chaque instance de cet &eacute;v&egrave;nement est appel&eacute;e une &eacute;dition. Elle admet des bateaux de type Ultims, IMOCA, Multi 50 et
                Class40. Chaque navire est dirig&eacute; par un &eacute;quipage de deux marins, un skipper et un co-skipper.</p>";
    $contenu .= "<p>Ce site web a pour vocation le stockage des informations relatives &agrave; cette course, les donn&eacute;es d&eacute;j&agrave; disponibles
                recueillent les informations des &eacute;ditions 2017 et 2018.</p>";
    $contenu .= "<p>Vous pouvez simplement consulter la base ou bien ajouter, modifier, supprimer des donn&eacute;es de la base &agrave; votre convenance.
                Voici ci-dessous le sch&eacute;ma relatif au fonctionnement de la base :</p>";
    $contenu .= "<figure class=\"image is-marginless\"><img src=\"../bulma-start-0.0.3/Images/SchemaE_A.png\"></figure>";
    $contenu .= "</div>\n</div>\n";

    return $contenu;
}

function getMessage($message, $etat): string
{
    return "
<article class=\"message $etat is-medium\">
  <div class=\"message-body \">
  $message
  </div>
  </article>";
}

function getLabel($label, $style = "")
{
    return "<label class=\"label $style\">$label</label>";
}

function getColumns($contenu, $style = "")
{
    return "<div class=\"columns $style\">
                $contenu
            </div>\n";
}

function getColum($contenu, $style = "")
{
    return "<div class=\"column $style\">\n
                $contenu
            </div>\n";
}

function getContent($contenu)
{
    return "<div class=\"content\" style=\"border-top:2px solid #F37021; padding-top: 1em;\">
                $contenu
            </div>";
}

function getBox($titre, $contenu)
{
    return "<div class=\"box is-marginless has-background-light\"> <h5 class=\"title is-5\">$titre</h5></div>
                <div class=\"box \">
                    $contenu
                </div>
            </div>";
}


