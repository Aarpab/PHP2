<!DOCTYPE html>

<html>
    <head>
        <title>Anmelden</title>
        <link rel="stylesheet" type="text/css" href="index.css">
    </head>

    <body>
        <form action="ziel.php" method="POST">
            Name: <input type="text" name="username" autocomplete="off"><br>
            Passwort: <input type="password" name="passwort"><br>

            <div id="div1">
                <input type="submit" value="Einloggen">
                <a href="registrieren.php">registrieren</a>
            </div>
        </form>
    </body>
</html>