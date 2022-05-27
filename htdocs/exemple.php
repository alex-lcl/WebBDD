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
            <!-- Le forumilaire de recherche des phrases d'exemples -->
            <section class="border shadow">
              <h2 id="formulaire2">Je recherche des exemples d'emploi d'un mot...</h2>
              <form class=".form-horizontal" method="GET" action="exemple.php">
              <!-- éléments du formulaire -->
              <fieldset>
                  <legend>Vous recherchez des exemples d'utisilation de quel mot ?</legend>
                  
                  <p class="row"><label class="lab col-3" for="mot_source">Mot en langue source</label>
                  <input class="col-3" type="text" id="mot_source"  name="mot_source" required="required"/></p>
                  
                  <p class="row"><label class="lab col-3" for="langue_source" >Langue du mot source</label>
                  <select name="langue_source" class="col-3" required="required">
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
              //on récupère les variables du formulaire 2
              $mot_source = $_GET['mot_source'];
              $langue_num = $_GET['langue_source'];
              // A partir des numéros, on a le nom de la langue
              if ($langue_num == "1"){$langue_source = "Greedian ancien";}
              elseif ($langue_num == "2"){$langue_source = "Nespatais";}
              
              // On affiche les informations entrées dans le formulaire 2
              echo "<p> Vous cherchez des exemples d'utilisation de <b>".$mot_source."</b> en <b>".$langue_source."</b>.";
              
              //On cherche dans la base de donnée une traduction:
              try {
                //connection à la base de données
                $login = "root"; 
                $mdp = "root"; 
                $sql= new PDO('mysql:host=localhost;dbname=projet', $login, $mdp, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")) ;
                //si la langue source est le greedien ancien
                if ($langue_num == "1"){
                    //on cherche l'id du mot
                    $requete = 'SELECT id FROM dico_mardi WHERE lexeme = :mot_source';
                    $res = $sql->prepare($requete);
                    $res->bindParam(":mot_source", $mot_source);
                    $res->execute();
                    //on cherche si le mot entrée est bien dans notre base de donnée
                    $nb_resultats = $res->rowCount();
                    //affichage des résultats ce résultat:
                    //s'il n'existe pas, on affiche un message
                    if ($nb_resultats == 0){ echo "<p>Le mot n'existe pas dans notre base de donnée.</p>";}
                    else {
                        //sinon, on va cherche des phrases d'exemples
                        echo "<ul>";
                        $aucun_resultat = 0;
                        while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            // avec l'identifiant du mot réccupéré 
                            $id = $ligne->id;
                            $requete2 = 'SELECT p_source, p_cible FROM pharses_mardi WHERE mot_source = :id';
                            $res2 = $sql->prepare($requete2);
                            $res2->bindParam(":id", $id);
                            $res2->execute();
                            //on affiche les résultats
                            $nb_resultats2 = $res2->rowCount();
                            
                            if ($nb_resultats2 == 0){$aucun_resultat = $aucun_resultat + 1;}
                            else{
                                while ($ligne2 = $res2->fetch(PDO::FETCH_OBJ)){
                                    echo '<li>Greedien ancien: <b>'.$ligne2->p_source.'</b><br/>Traduction en français: <i>'.$ligne2->p_cible.'</i></li>';
                                }
                            }
                        }
                        if ($aucun_resultat == $nb_resultats){echo "<li>Aucune phrase d'a été trouvé dans notre base de donnée.</li>";}
                        echo "</ul>";
                    }
                }
                //si la langue source est en nespatais
                elseif ($langue_num == "2"){
                    //on cherche l'id du mot
                    $requete = 'SELECT id FROM dico_nespatais WHERE nespatais = :mot_source';
                    $res = $sql->prepare($requete);
                    $res->bindParam(":mot_source", $mot_source);
                    $res->execute();
                    //on cherche si le mot entrée est bien dans notre base de donnée
                    $nb_resultats = $res->rowCount();
                    //affichage des résultats ce résultat:
                    //s'il n'existe pas, on affiche un message
                    if ($nb_resultats == 0){ echo "<p>Le mot n'existe pas dans notre base de donnée.</p>";}
                    else {
                        //sinon, on va cherche des phrases d'exemples
                        echo "<ul>";
                        $aucun_resultat = 0;
                        while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                            // avec l'identifiant du mot réccupéré 
                            $id = $ligne->id;
                            $requete2 = 'SELECT p_source, p_cible FROM phrases_nespatais WHERE mot_source = :id';
                            $res2 = $sql->prepare($requete2);
                            $res2->bindParam(":id", $id);
                            $res2->execute();
                            //on affiche les résultats
                            $nb_resultats2 = $res2->rowCount();
                            if ($nb_resultats2 == 0){$aucun_resultat = $aucun_resultat + 1;}
                            else{
                                while ($ligne2 = $res2->fetch(PDO::FETCH_OBJ)){
                                    echo '<li>Greedien ancien: <b>'.$ligne2->p_source.'</b><br/>Traduction en français: <i>'.$ligne2->p_cible.'</i></li>';
                                }
                            }
                        }
                        if ($aucun_resultat == $nb_resultats){echo "<li>Aucune phrase d'a été trouvé dans notre base de donnée.</li>";}
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