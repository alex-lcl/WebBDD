<!DOCTYPE html>
<html lang="fr">
    <!-- L'en-tête de la page -->
    <head>
        <!-- pour bootstrap -->
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"/>
		<link rel="stylesheet" href="custom.css"/>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
        <meta charset="UTF-8" />
        <meta name="author" content="Alexandra LI COMBEAU LONGUET" />
        <meta name="Traduction" content="rechercher une traduction ou une phrase d'exemple" />
        <title>Rechercher une traduction</title>
    </head>
    <!-- Le contenu du site -->
    <body>
        <!-- Le bandeau du site -->
        <header class="bg-primary">
            <!-- Le nom du site -->
            <h1>Idéolangue en action !</h1>
            <!-- La bar de navigation du site -->
            <nav class="navbar navbar-expand-lg navbar-light bg-primary">
                <div class="container-fluid">
                  <a class="navbar-brand" href="#">Navigation</a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                      <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="recherche.html#formulaire1">Rechercher une traduction</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="recherche.html#formulaire2">Rechercher une phrase d'exemple</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="ajout.html#formulaire3">Ajouter un mot ou une phrase</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>
        </header>
        <!-- le contenu du site -->
        <main class="mx-auto container">
            <!-- Le forumilaire de recherche d'une traduction -->
            <section class="border shadow">
                <h2 id="formulaire1">Je recherche une traduction</h2>
                <form class=".form-horizontal" method="GET" action="traduction.php">
                <!-- éléments du formulaire -->
                <fieldset>
                    <legend>Quel mot voulez-vous traduire en français ?</legend>
                    
                    <p class="row"><label class="lab col-3" for="mot_source">Mot en langue source</label>
                    <input class="col-3" type="text" id="mot_source"  name="mot_source"/></p>
                    
                    <p class="row"><label class="lab col-3" for="langue_source" >Langue du mot source</label>
                    <select name="langue_source" class="col-3">
                    <option value="1">Greedien ancien</option>
                    <option value="2" >Nespatais</option>
                    </select></p>
                    <input class="btn btn-primary mb-2" type="submit" value="Envoyer" />
                </fieldset>
                </form>
            </section>
            <!-- Affichage des résulats en php -->
            <section class="border shadow">
              <h2 id="formulaire2">Les résultats</h2>
              <?php
              //on récupère les variables du formulaire 1
              $mot_source = $_GET['mot_source'];
              $langue_num = $_GET['langue_source'];
              // A partir des numéros, on a le nom de la langue
              if ($langue_num == "1"){$langue_source = "Greedian ancien";}
              elseif ($langue_num == "2"){$langue_source = "Nespatais";}
              
              // On affiche les informations entrées dans le formulaire 1
              echo "<p> Vous cherchez la traduction en français de <b>".$mot_source."</b> (".$langue_source.").";
              
              //On cherche dans la base de donnée une traduction:
              try {
                //connection à la base de données
                $login = "root"; 
                $mdp = "root"; 
                $sql= new PDO('mysql:host=localhost;dbname=projet', $login, $mdp, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")) ;
                //si la langue source est le greedien ancien
                if ($langue_num == "1"){
                    //on fait la requêtte sql
                    $requete = 'SELECT sens_court, sens_long FROM dico_mardi WHERE lexeme = :mot_source';
                    $res = $sql->prepare($requete);
                    $res->bindParam(":mot_source", $mot_source);
                    $res->execute();
                    //calcul du nombee de résultats
                    $nb_resultats = $res->rowCount();
                    //affichage des résultats suivant ce nombre
                    if ($nb_resultats == 0){ echo "<p>Aucune traduction trouvée.</p>";}
                    elseif ($nb_resultats == 1){
                        echo "<p>Traduction possible:</p>";
                        echo "<ul>";
                        while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            echo '<li>Traduction: '.$ligne->sens_court.'<br/>Explication brève: '.$ligne->sens_long.'</li>';
                        }
                    echo "</ul>";
                    }
                    else {
                        echo "<p>Traductions possibles:</p>";
                        echo "<ul>";
                        while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            echo '<li>Traduction: '.$ligne->sens_court.'<br/>Explication brève: '.$ligne->sens_long.'</li>';
                        }
                        echo "</ul>";
                    }
                }
                //si la langue source est en nespatais
                elseif ($langue_num == "2"){
                    //on fait la requête sql
                    $requete = 'SELECT francais FROM dico_nespatais WHERE nespatais = :mot_source';
                    $res = $sql->prepare($requete);
                    $res->bindParam(":mot_source", $mot_source);
                    $res->execute();
                    //calcul du nomrbe de résultats
                    $nb_resultats = $res->rowCount();
                    // affichage des résultats en conséquence
                    if ($nb_resultats == 0){ echo "<p>Aucune traduction trouvée.</p>";}
                    elseif ($nb_resultats == 1){
                        echo "<p>Traduction possible:</p>";
                        echo "<ul>";
                        while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            echo '<li>'.$ligne->francais.'</li>';
                        }
                        echo "</ul>";
                    }
                    else {
                        echo "<p>Traductions possibles:</p>";
                        echo "<ul>";
                        while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            echo '<li>'.$ligne->francais.'</li>';
                        }
                        echo "</ul>";
                    }
                }
                }
                catch(PDOExeption $errur){
                    echo $erreur->getMessage();
                    die();
                }
              ?>
          </section>
        </main>
        <!-- Le peid de page du site -->
        <footer class="fixed-bottom bg-primary text-dark">@Alexandra LI COMBEAU LONGUET</footer>
</body>
</html>