<?php

header("Content-Type: application/json; charset=utf-8");

// Get parameters
$action = (isset($_GET["action"])) ? preg_replace("/[^-a-zA-Z0-9_]/", "", $_GET["action"]) : "";
$param = (isset($_GET["param"])) ? preg_replace("/[^-a-zA-Z0-9_]/", "", $_GET["param"]) : "";
$secret = (isset($_GET["secret"])) ? preg_replace("/[^-a-zA-Z0-9_]/", "", $_GET["secret"]) : "";
$percentile = (isset($_GET["percentile"])) ? preg_replace("/[^0-9]/", "", $_GET["percentile"]) : "";

// SQLite database
$sqlitedb = "../word2vec/word2vec.db";

if($action == "model2") {
  $db = new SQLite3($sqlitedb);
  $sql = $db->prepare("SELECT vec, percentile FROM word2vec LEFT OUTER JOIN nearby ON nearby.word = :secret AND nearby.neighbor = :word WHERE word2vec.word = :word");
  $sql->bindValue(":word", $param, SQLITE3_TEXT);
  $sql->bindValue(":secret", $secret, SQLITE3_TEXT);
  $result = $sql->execute()->fetchArray();

  $output["vec"] = array();

  $vec = $result[0];
  for($i=0;$i<strlen($vec);$i+=2) {
    // expand_bfloat
    $bytes = "\00\00" . $vec[$i] . $vec[$i+1];
    $unpacked = unpack("f*", $bytes);
    array_push($output["vec"], $unpacked[1]);
  }

  if(isset($result["percentile"])) {
    $output["percentile"] = intval($result["percentile"]);
  }
  
  if(count($output["vec"]) > 0) {
    echo(json_encode($output));
  }
}

if($action == "similarity") {
  $db = new SQLite3($sqlitedb);
  $sql = $db->prepare("SELECT top, top10, rest FROM similarity_range WHERE word = :word");
  $sql->bindValue(":word", $param, SQLITE3_TEXT);
  $result = $sql->execute()->fetchArray();

  $output["top"] = $result["top"];
  $output["top10"] = $result["top10"];
  $output["rest"] = $result["rest"];
  
  echo(json_encode($output));
}

if($action == "nearby") {
  $db = new SQLite3($sqlitedb);
  $sql = $db->prepare("SELECT neighbor FROM nearby WHERE word = :word ORDER BY percentile DESC LIMIT 10 OFFSET 1");
  $sql->bindValue(":word", $param, SQLITE3_TEXT);
  $result = $sql->execute();

  $output = array();

  while($row = $result->fetchArray()) {
    array_push($output, $row[0]);
  }
   
  echo(json_encode($output));
}

if($action == "nth_nearby") {
  $db = new SQLite3($sqlitedb);
  $sql = $db->prepare("SELECT neighbor FROM nearby WHERE word = :word AND percentile = :percentile LIMIT 1");
  $sql->bindValue(":word", $param, SQLITE3_TEXT);
  $sql->bindValue(":percentile", $percentile, SQLITE3_INTEGER);
  $result = $sql->execute()->fetchArray();

  echo(json_encode($result[0]));
}
