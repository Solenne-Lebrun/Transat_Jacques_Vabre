<?php

include 'AbstractEntite.php';
include 'AbstractVue.php';
include 'connex.php';
include 'MyPDO.php';
include 'Skipper/EntiteSkipper.php';
include 'Skipper/VueSkipper.php';
include 'Bateau/EntiteBateau.php';
include 'Bateau/VueBateau.php';
include 'Edition/EntiteEdition.php';
include 'Edition/VueEdition.php';
include 'Participe/AssociationParticipe.php';
include 'Participe/VueParticipe.php';
include 'Participe/MyIteratorParticipe.php';
include 'Arrive/AssociationArrive.php';
include 'Arrive/VueArrive.php';
include 'Arrive/MyIteratorArrive.php';
include 'MyIterator.php';
include 'fonctionHTML.php';
include 'fonctionPHP.php';
ini_set('display_errors', 'on');//juste pour afficher les erreurs dans le navigateur


session_start();

// Tableau associant le nom des tables avec leur type
$tables = array("bateau" => "Entite", "skipper" => "Entite", "edition" => "Entite", "participe" => "Association", "arrive" => "Association");
$myPDO = new MyPDO('pgsql', $_ENV['host'], $_ENV['db'], $_ENV['user'], $_ENV['password']);

$contenu = "";
$message = "";

if (!isset($_GET['action']))
    $_GET['action'] = "initialiser";

// Pour réinitialiser la pagination à 1 lors de l'affichage de la table
if (isset($_SESSION['debut']) && $_GET['action'] == "initialiser")
    unset($_SESSION['debut']);

switch ($_GET['action']) {
    case 'initialiser':
        $_SESSION['état'] = 'Accueil';
        break;
    case 'selectionnerTable':
        $array_explode = explode('_', $_GET['table_name']);
        $_SESSION['type_table'] = $array_explode[0];
        $_SESSION['table_name'] = $array_explode[1];
        initTableMyPDO($myPDO);
        $_SESSION['état'] = 'afficheTable';
        break;
    case 'supprimerEntité':
        $colsValuesGET = array_diff_key($_GET, array('action' => TRUE));
        cascadeSuppression($tables, $myPDO, $colsValuesGET);
        initTableMyPDO($myPDO);
        try {
            $entite = $myPDO->get($colsValuesGET);
            $myPDO->delete($colsValuesGET);
            $message .= getMessage("<p>Entité " . $entite . " supprimée</p>\n", "is-success");
        } catch (PDOException $e) {
            $message .= getMessage($e->getMessage(), "is-danger");
        }
        $_SESSION['etat'] = 'afficheTable';
        break;
    case 'créerEntité':
    case 'modifierEntité':
        $_SESSION['état'] = 'formulaireTable';
        break;
    case 'insérerEntité':
    case 'sauverEntité':
        initTableMyPDO($myPDO);
        formatageGET();
        $param = array_diff_key($_GET, array('action' => true));
        $classe = new ReflectionClass("transat\\" . $_SESSION['type_table'] . ucfirst($_SESSION['table_name']));
        if ($classe->getMethod('isValid')->invokeArgs(null, array($param, $myPDO, &$message, $_GET['action']))) {
            try {
                if ($_GET['action'] == 'sauverEntité') {
                    $myPDO->update($classe->getStaticPropertyValue(("PK")), $param);
                    $message .= getMessage("<p>Entité " . $myPDO->get($param) . " modifiée</p>\n", "is-success");
                } else {
                    $myPDO->insert($param);
                    $message .= getMessage("<p>Entité " . $myPDO->get($param) . " crée</p>\n", "is-success");
                }
            } catch (PDOException $e) {
                $message .= getMessage($e->getMessage(), "is-danger");
            }
            $_SESSION['état'] = 'afficheTable';
        } else {
            $_SESSION['état'] = 'formulaireTable';
        }
        break;
    default:
        $message .= "<p>Action " . $_GET['action'] . " non implémentée.</p>\n";
        $_SESSION['etat'] = 'Accueil';
}


switch ($_SESSION['état']) {
    case 'Accueil':
        $contenu .= getNavBar();
        $contenu .= getListeTables($tables);
        $contenu .= getContenuAccueil();
        break;
    case 'afficheTable' :
        initTableMyPDO($myPDO);
        $nbEnregistrements = $myPDO->count();
        $contenu .= getDebutColumn();
        $contenu .= $message;
        $contenu .= "<h5 h5 class='subtitle has-text-info'>La table " . ucfirst($_SESSION['table_name']) . " contient " . $nbEnregistrements . " enregistrements.</h5>";
        $contenu .= "<h5 class='subtitle is-5'><a href='?action=créerEntité'>Créer enregistrement " . $_SESSION['table_name'] . "</a></h5>\n";
        $classeVue = new ReflectionClass("transat\Vue" . ucfirst($_SESSION['table_name']));
        $vue = $classeVue->newInstance();
        $contenu .= $vue->getAllEntities($myPDO);
        $contenu .= getFinColumn();
        break;
    case 'formulaireTable':
        // Détermine l'action qui sera envoyé lors de la validation du formulaire
        $actions = array('modifierEntité' => 'sauverEntité', 'créerEntité' => 'insérerEntité',
            'sauverEntité' => 'sauverEntité', 'insérerEntité' => 'insérerEntité');
        initTableMyPDO($myPDO);
        $colsValuesGET = array_diff_key($_GET, array('action' => TRUE));
        $classe = new ReflectionClass("transat\\" . $_SESSION['type_table'] . ucfirst($_SESSION['table_name']));
        $colNames = $classe->getStaticPropertyValue("COLNAMES");
        $colTypes = $classe->getStaticPropertyValue("COLTYPES");
        $PK = $classe->getStaticPropertyValue("PK");
        $paramForm = array_combine($colNames, $colTypes);
        $entite = null;

        if ($_GET['action'] == 'modifierEntité' || $_GET['action'] == 'sauverEntité') {
            $entite = $myPDO->get(array_intersect_key($colsValuesGET, array_combine($PK, $PK)));
            $colValues = array_combine($colNames, $entite->getALLValues());
        } elseif (!empty($colsValuesGET)) // Cas où les données sont récupérable par le $_GET
            $colValues = $_GET;

        // Tous les cas sauf pour l'insertion sans erreur car il ne doit pas y avoir de valeur par défaut
        if (!empty($colValues))
            foreach ($paramForm as $colName => $colType)
                if (isset($colValues[$colName]))
                    $paramForm[$colName] = array('type' => $colType, 'default' => $colValues[$colName]);

        // Cas où la table possède des clés étrangères, il faut donc une liste de valeurs possibles dans le formulaire
        if (!empty($classe->getStaticPropertyValue("FK"))) {
            $colsFK = array_combine($classe->getStaticPropertyValue("FK"), $classe->getStaticPropertyValue("TABLESFK"));
            foreach ($colsFK as $nomColFK => $nomTableFK) {
                if (!empty($colValues)) {
                    $paramForm[$nomColFK] = array('select' => $nomTableFK, 'default' => $colValues[$nomColFK]);
                } else
                    $paramForm[$nomColFK] = array('select' => $nomTableFK);
            }
        }

        //On enlève les clés primaires si elle s'auto-incrémente ou si on modifie l'entité
        if ($classe->getStaticPropertyValue("AUTOID") || $_GET['action'] == 'modifierEntité' || $_GET['action'] == 'sauverEntité')
            $paramForm = array_diff_key($paramForm, array_combine($PK, $PK));
        $classeVue = new ReflectionClass("transat\Vue" . ucfirst($_SESSION['table_name']));
        $vue = $classeVue->newInstance();
        $contenu .= getDebutColumn();
        $contenu .= $message;
        $contenu .= $vue->getForm4Entity($paramForm, $actions[$_GET['action']], $myPDO, $entite);
        $contenu .= getFinColumn();
        break;
    default:
        $message .= "<p>état " . $_SESSION['etat'] . " inconnu</p>\n";
        $_SESSION['etat'] = 'Accueil';
}

echo getDebutHTML();
echo $contenu;
echo getFinHTML();