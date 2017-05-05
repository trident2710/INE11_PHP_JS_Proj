<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <style>
        .container {
            padding: 50px;
        }
    </style>
</head>
<body>
    <?php
        if(!empty($_GET['status'])){
            switch ($_GET['status']){
                case 'win':
                    echo "Felicitations, vous aves gagné. Votre jeu est fini.";
                    break;
                case 'lose':
                    echo "Vous êtes au chambre sans sortie. C'est le defaite, désolé. Votre jeu est fini.";
                    break;
                case 'login':
                    echo "Inserez le login de votre personnage si il existe ou inserez le nom de neuveau personage";
                    break;
                case 'dead':
                    echo "Vous êtes tué. Désolé, votre jeu est fini";
                    break;
                default:

            }
        }
    ?>
    <div class="container">
        <form action="../game.php" method="GET">
            <input type="hidden" name="command" value="login" />
            <p>Inserez votre login</p>
            <input type="text" id = name name="name">
            <input type="submit"/>
        </form>
    </div>
</body>
</html>