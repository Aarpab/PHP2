<!DOCTYPE html>

<html>
    <head>
        <title>Forum</title>
        <link rel="stylesheet" type="text/css" href="ziel.css">
        <script src="ziel.js"></script>
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

            if (!empty($_POST["text"])) {
                $servername = "localhost";
                $nutzername = "root";
                $pw = "";
                $dbname = "forum";

                $conn = new mysqli($servername, $nutzername, $pw, $dbname);
                
                if ($conn -> connect_error) {
                    echo "<script>window.alert('Bei der Verbindung zum Server ist ein Fehler aufgetreten')</script>";
                }
                else {
                    $sql = $conn -> prepare("SELECT ID FROM User WHERE Name=?");

                    $sql -> bind_param("s", $_POST["username"]);
                    $sql -> execute();

                    $sql -> bind_result($res_id);

                    if ($sql -> fetch()) {
                        $id = $res_id;
                    }

                    $sql -> close();

                    $sql = $conn -> prepare("SELECT * FROM Dateien WHERE UserID=?");

                    $sql -> bind_param("i", $id);
                    $sql -> execute();

                    $sql -> bind_result($res_id, $res_text, $res_bild);

                    $i = 0;

                    while ($sql -> fetch()) {
                        $i++;
                    }

                    $sql -> close();

                    $pfad = "user/" . $_POST["username"] . "/texte" . "/text" . $i + 1 . ".txt";

                    $file = fopen($pfad, "w");

                    fwrite($file, $_POST["text"]);

                    fclose($file);

                    $ziel = "user/" . $_POST["username"] . "/bilder" . "/";
                    $zieldatei = $ziel . basename($_FILES["datei"]["name"]);
                    $error = 0;

                    if ($zieldatei != $ziel) {
                        $imagesize = getimagesize($_FILES["datei"]["tmp_name"]);
                        if ($imagesize === false) {
                            $error = "Du kannst nur Bilder versckicken";
                        }
                        else {
                            $imagesize["mime"];
                        }

                        $endung = pathinfo($zieldatei, PATHINFO_EXTENSION);
                        if ($endung != "jpg" && $endung != "jpeg" && $endung != "png") {
                            $error = "Das Format deiner Datei wird nicht unterstützt";
                        }

                        if ($_FILES["datei"]["size"] > 2*1000*1000) {
                            $error = "Das Bild ist zu groß";
                        }

                        if ($error === 0) {
                            if (!move_uploaded_file($_FILES["datei"]["tmp_name"], $zieldatei)) {
                                echo "<script>window.alert('Bei dem Speichern des Bildes ist ein Fehler aufgetreten')</script>";
                            }
                            else {
                                $sql = $conn -> prepare("INSERT INTO Dateien VALUES(?, ?, ?)");

                                $sql -> bind_param("iss", $id, $pfad, $zieldatei);
                                $sql -> execute();
                            }
                        }
                        else {
                            echo "<script>window.alert('" . $error . "')</script>";
                        }
                    }
                    else {
                        $sql = $conn -> prepare("INSERT INTO Dateien (UserID, Name) VALUES(?, ?)");

                        $sql -> bind_param("is", $id, $pfad);
                        $sql -> execute();
                    }

                    $sql -> close();
                    $conn -> close();
                }
            }
        ?>

        <a class="link" href="#form">Nach unten</a><br>
        <a class="link" href="löschen.php">Account löschen</a>

        <div id="div1">
            <div id="div2">
                <?php
                    $servername = "localhost";
                    $nutzername = "root";
                    $pw = "";
                    $dbname = "forum";

                    $conn = new mysqli($servername, $nutzername, $pw, $dbname);

                    if ($conn -> connect_error) {
                        echo "<script>window.alert('Bei der Verbindung zum Server ist ein Fehler aufgetreten')</script>";
                    }
                    else {
                        $sql = $conn -> prepare("SELECT * FROM Dateien");

                        $sql -> execute();

                        $sql -> bind_result($res_id, $res_text, $res_bild);

                        $ids = array();
                        $nachrichten = array();
                        $bilder = array();

                        while ($sql -> fetch()) {
                            array_push($ids, $res_id);
                            array_push($nachrichten, $res_text);
                            array_push($bilder, $res_bild);
                        }

                        $sql -> close();

                        for ($i = 0; $i < count($ids); $i++) {
                            $sql = $conn -> prepare("SELECT Name FROM User WHERE ID=?");

                            $sql -> bind_param("i", $ids[$i]);
                            $sql -> execute();

                            $sql -> bind_result($res_name);

                            if ($sql -> fetch()) {
                                $name = $res_name;
                            }

                            $sql -> close();

                            echo "<br><h3>" . $name . "</h3>";

                            if ($bilder[$i] != "") {
                                echo "<img src='" . $bilder[$i] . "'></img>" . "<br>";
                            }

                            $file = fopen($nachrichten[$i], "r");

                            while (!feof($file)) {
                                echo fgets($file) . "<br>";
                            }

                            fclose($file);
                        }
                    }
                ?>
            </div>

            <form id="form" action="ziel.php#form" method="POST" enctype="multipart/form-data">
                <input id="text" type="text" name="text" autocomplete="off">
                <input type="submit"><br>
                Bild: <input id="datei" type="file" name="datei">

                <input type="hidden" name="username" value="<?php echo htmlspecialchars(stripslashes(trim($_POST["username"])))?>">
                <input type="hidden" name="passwort" value="<?php echo htmlspecialchars(stripslashes(trim($_POST["passwort"])))?>">
            </form>
        </div>
    </body>
</html>