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
            <section class="border shadow">
              <h2>Les résultats</h2>
              <?php
              //connection à la base de données
              $login = "root"; 
              $mdp = "root"; 
              $sql= new PDO('mysql:host=localhost;dbname=projet', $login, $mdp, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")) ;

              //Réccupération des informations
              $type = $_POST["type"];
              //on vérifie si l'utilisateur à bien entrée quel type d'information il veut ajouter
              if ($type == "mot"){
                $mot_source = $_POST['mot_source'];
                $langue_num = $_POST['langue_source'];
                $mot_fr = $_POST['mot_fr'];
                
                // A partir des numéros, on a le nom de la langue
                if ($langue_num == "1"){$langue_source = "Greedian ancien";}
                elseif ($langue_num == "2"){$langue_source = "Nespatais";}

                if ($langue_num == 1){
                    $mot_melodie = $_POST['mot_melodie'];
                    $mot_pos = $_POST['mot_pos'];
                    $mot_explication = $_POST['explication'];
                    try {
                        $requete = 'INSERT INTO dico_mardi(lexeme,melodie_tonale,pos,sens_court,sens_long) VALUES(:col1, :col2, :col3, :col4, :col5) ';
                        $res = $sql->prepare($requete);
                        $res->bindParam(":col1", $mot_source);
                        $res->bindParam(":col2", $mot_melodie);
                        $res->bindParam(":col3", $mot_pos);
                        $res->bindParam(":col4", $mot_fr);
                        $res->bindParam(":col5", $mot_explication);
                        $res->execute();

                    }
                    catch(PDOExeption $errur){
                        echo $erreur->getMessage();
                        die();
                    }
                }
                elseif ($langue_num == 2){
                    try {
                        $requete = 'INSERT INTO dico_nespatais(nespatais, francais) VALUES(:col1, :col2) ';
                        $res = $sql->prepare($requete);
                        $res->bindParam(":col1", $mot_source);
                        $res->bindParam(":col2", $mot_fr);
                        $res->execute();
                    }
                    catch(PDOExeption $errur){
                        echo $erreur->getMessage();
                        die();
                    }

                }
                else {echo "<p> Le champs 'type' doit obligatoirement être rempli.</p>";}
                echo "Le mot ".$mot_source." (".$langue_source." ) a bien été ajouté !";
              }
              elseif ($type == "exemple"){
                $mot_source = $_POST['mot_source'];
                $langue_num = $_POST['langue_source'];
                $p_source = $_POST['p_source'];
                $p_fr = $_POST['p_fr'];
                
                // A partir des numéros, on a le nom de la langue
                if ($langue_num == "1"){$langue_source = "Greedian ancien";}
                elseif ($langue_num == "2"){$langue_source = "Nespatais";}

                try {
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
                        if ($nb_resultats == 0){ echo "<p>Le mot <b>".$mot_source."</b> n'existe pas dans notre base de donnée.</p>";}
                        else {
                            //sinon, on va l'ajouter dans notre base de donnée
                            while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                                $id = $ligne->id;
                                $requete2 = 'INSERT INTO pharses_mardi(mot_source,p_source,p_cible) VALUES(:col1, :col2, :col3) ';
                                $res2 = $sql->prepare($requete2);
                                $res2->bindParam(":col1", $id);
                                $res2->bindParam(":col2", $p_source);
                                $res2->bindParam(":col3", $p_fr);
                                $res2->execute();
                                echo "La phrase d'exemple a été ajouté à la première occurence du mot !";
                                break;
                            }
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
                            //sinon, on va l'ajouter dans notre base de donnée
                            while($ligne = $res->fetch(PDO::FETCH_OBJ)) {
                                $id = $ligne->id;
                                $requete2 = 'INSERT INTO phrases_nespatais(mot_source,p_source,p_cible) VALUES(:col1, :col2, :col3) ';
                                $res2 = $sql->prepare($requete2);
                                $res2->bindParam(":col1", $id);
                                $res2->bindParam(":col2", $p_source);
                                $res2->bindParam(":col3", $p_fr);
                                $res2->execute();
                                echo "La phrase d'exemple a été ajouté à la première occurence du mot !";
                                break;
                            }
                        }
                    }

                   
                }
                catch(PDOExeption $errur){
                    echo $erreur->getMessage();
                    die();
                }
                
              }
              
              ?>
          </section>
        </main>
        <!-- Le peid de page du site -->
        <footer class="fixed-bottom bg-primary text-dark">@Alexandra LI COMBEAU LONGUET</footer>
</body>
</html>