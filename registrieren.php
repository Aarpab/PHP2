<!DOCTYPE html>

<html>
    <head>
        <title>Registrieren</title>
    </head>

    <body>
        <form action="registrieren.php" method="POST">
            Name: <input type="text" name="name" autocomplete="off"><br>
            Passwort: <input type="password" name="passwort"><br>
            Passwort bestätigen: <input type="password" name="passwort2"><br>
            <input type="submit">
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty(htmlspecialchars(stripslashes(trim($_POST["name"])))) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort"])))) && !empty(htmlspecialchars(stripslashes(trim($_POST["passwort2"]))))) {
                    if ($_POST["passwort"] === $_POST["passwort2"]) {
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

                            $sql -> bind_param("s", $_POST["name"]);
                            $sql -> execute();

                            $sql -> bind_result($res_id, $res_name, $res_passwort);

                            $vorhanden = false;

                            if ($sql -> fetch()) {
                                echo "<script>window.alert('Der Name ist schon vorhanden')</script>";
                                $vorhanden = true;
                            }

                            $sql -> close();

                            if ($vorhanden === false) {
                                $hashed = password_hash($_POST["passwort"], PASSWORD_DEFAULT);

                                $sql = $conn -> prepare("INSERT INTO User(Name, Passwort) VALUES(?, ?)");

                                $sql -> bind_param("ss", $_POST["name"], $hashed);
                                $sql -> execute();

                                $sql -> close();

                                $sql = $conn -> prepare("SELECT * FROM User WHERE Name=?");

                                $sql -> bind_param("s", $_POST["name"]);
                                $sql -> execute();

                                if ($sql -> fetch()) {
                                    echo "<script>window.alert('Du hast dich erfolgreich angemeldet')</script>";
                                    echo "<a href='index.php'>Zurück zur Anmeldung</a>";
                                }
                                else {
                                    echo "<script>window.alert('Du hast dich erfolgreich registriert')</script>";
                                }

                                $sql -> close();
                            }
                        }
                    }
                }
            }
        ?>
    </body>
</html>