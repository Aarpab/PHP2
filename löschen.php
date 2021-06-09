<!DOCTYPE html>

<html>
    <head>
        <title>Account löschen</title>
    </head>

    <body>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty($_POST["name_l"]) && !empty($_POST["passwort_l"])) {
                    $servername = "localhost";
                    $nutzername = "root";
                    $pw = "";
                    $dbname = "forum";

                    $conn = new mysqli($servername, $nutzername, $pw, $dbname);

                    if ($conn -> connect_error) {
                        echo "<script>window.alert('Bei der Verbindung zum Server ist ein Fehler aufgetreten')</script>";
                    }
                    else {
                        $sql = $conn -> prepare("SELECT * FROM User WHERE Name=?");

                        $sql -> bind_param("s", $_POST["name_l"]);
                        $sql -> execute();

                        $sql -> bind_result($res_id, $res_name, $res_pass);

                        $richtig = false;

                        while ($sql -> fetch()) {
                            if (password_verify($_POST["passwort_l"], $res_pass)) {
                                $id = $res_id;
                                $name = $res_name;
                                $richtig = true;
                                break;
                            }
                        }

                        $sql -> close();

                        if ($richtig === true) {
                            $sql = $conn -> prepare("SELECT Name, Bild FROM Dateien WHERE UserID=?");

                            $sql -> bind_param("i", $id);
                            $sql -> execute();

                            $sql -> bind_result($res_text, $res_bild);

                            while ($sql -> fetch()) {
                                unlink($res_text);

                                if (!empty($res_bild)) {
                                    unlink($res_bild);
                                }
                            }

                            $sql -> close();

                            rmdir("user/" . $name . "/texte");
                            rmdir("user/" . $name . "/bilder");
                            rmdir("user/" . $name);

                            $sql = $conn -> prepare("DELETE FROM User WHERE ID=?");

                            $sql -> bind_param("i", $id);
                            $sql -> execute();

                            $sql -> close();

                            $sql = $conn -> prepare("DELETE FROM Dateien WHERE UserID=?");

                            $sql -> bind_param("i", $id);
                            $sql -> execute();

                            $conn -> close();

                            header("Location: index.php");
                        }
                    }
                }
            }
        ?>

        <form action="löschen.php" method="POST">
            Name: <input type="text" name="name_l" autocomplete="off"><br>
            Passwort: <input type="password" name="passwort_l"><br>
            <input type="submit" value="löschen">
        </form>
    </body>
</html>