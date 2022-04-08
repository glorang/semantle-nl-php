<?php

// Get parameters
$param = (isset($_GET["param"])) ? preg_replace("/[^-a-zA-Z0-9_]/", "", $_GET["param"]) : "";

$secret = base64_decode($param);

// SQLite database
$sqlitedb = "../word2vec/word2vec.db";

$db = new SQLite3($sqlitedb);
$sql = $db->prepare("SELECT neighbor, percentile, similarity FROM nearby WHERE word = :word ORDER BY percentile DESC LIMIT 1000 OFFSET 1");
$sql->bindValue(":word", $secret, SQLITE3_TEXT);
$result = $sql->execute();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/assets/js/secretWords.js?v=3"></script>
    <script src="/assets/js/nearest1k.js"></script>
    <title>Semantle: nearest words</title>
  </head>
  <body>
    <div id="warning" style="display:none; ">
      <div style="border:thin solid black; background-color: #ffdddd">
        Opgepast: Deze pagina verraadt de opgave van vandaag. Klik op deze waarschuwing om de inhoud toch te zien.
      </div>
    </div>
    <div id="nearest" style="display:none;">
      Volgende woorden zijn het meest gerelateerd aan <b id="word"><?php echo($secret); ?></b>:
      <br /><br />
      <table >
        <tr>
          <th>Woord</th>
          <th>Score</th>
          <th>&permil;</th>
        </tr>
        <?php while($row = $result->fetchArray()) { ?>
        <tr>
          <td> <?php echo($row["neighbor"]); ?> </td>
          <td style="width: 100px; text-align:center;"> <?php echo(sprintf("%0.2f", $row["similarity"] * 100)); ?> </td>
          <td style="width: 100px; text-align:center;"> <?php echo(intval($row["percentile"])); ?> </td>
        </tr>
        <?php } ?>
      </table>
    </div>

    <br/>
    <a href="/">Terug naar Semantle</a>
  </body>
</html>
