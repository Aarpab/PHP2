<!DOCTYPE html>

<html>
    <head>
        <title>Forum</title>
        <link rel="stylesheet" type="text/css" href="ziel.css">
    </head>

    <body>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty(htmlspecialchars(stripslashes(trim($_POST["username"])))) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort"]))))) {
                    $servername = "localhost";
                    $nutzername = "root";
                    $pw = "";
                    $dbname = "forum";

                    $conn = new mysqli($servername, $nutzername, $pw, $dbname);

                    if ($conn -> connect_error) {
                        header("Location: index.php");
                    }
                    else {
                        $sql = $conn -> prepare("SELECT * FROM User WHERE Name=?");

                        $sql -> bind_param("s", $_POST["username"]);
                        $sql -> execute();

                        $sql -> bind_result($res_id, $res_name, $res_pass);

                        $richtig = false;

                        while ($sql -> fetch()) {
                            if (password_verify($_POST["passwort"], $res_pass)) {
                                $richtig = true;
                                break;
                            }
                        }

                        $sql -> close();

                        if ($richtig === false) {
                            header("Location: index.php");
                        }
                    }
                }
                else {
                    header("Location: index.php");
                }
            }
        ?>

        <div id="div1">
            <div id="div2"></div>
            <form></form>
        </div>
    </body>
</html>