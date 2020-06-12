<?php
$file=file_get_contents("https://www.umanetexpo.net/expo2015Server/UECDL/grafici/as_1920/report&3Ct$6.asp");
$elenco=json_decode($file,true);
$periodoInizioM;
$periodoFineM;
$periodoInizio=-1;
$periodoFine=-1;
$periodi=dividiPeriodi($elenco["intestazione"]);
$numeroPeriodi=count($periodi);

if(isset($_GET["periodoInizio"])) {
  $periodoInizioM=$_GET["periodoInizio"];
  if(controllaData($periodoInizioM))
    $periodoInizio=trovaData($periodoInizioM,$periodi);
  else $periodoInizio=0;
}else $periodoInizio=0;

if(isset($_GET["periodoFine"])) {
  $periodoFineM=$_GET["periodoFine"];
  if(controllaData($periodoFineM))
    $periodoFineM=trovaData($periodoFineM,$periodi);
  if($periodoFineM<$periodoInizio) {
    $periodoFine=$periodoInizio-1;
  }else $periodoFine=$periodoFineM;
}else $periodoFine=$numeroPeriodi;

function controllaData($s) {
  //Data: gg_mm_aaaa
  ////////0123456789
  //Controllo che la lunghezza sia corretta
  if(strlen($s)!=10)
    return false;
  //Controllo che giorno, mese e anno siano numeri
  if(!is_numeric(substr($s,0,2)))
    return false;
  if(!is_numeric(substr($s,3,2)))
    return false;
  if(!is_numeric(substr($s,6,4)))
    return false;
  //Fine dei controlli
  return true;
}
function dividiPeriodi($s) {
  $periodi=explode('&', $s);
  $n=array_filter($periodi,"controllaData");
  return $n;
}
function trovaData($s,$p) {
  for ($i=0; $i < count($p)-2; $i++)
    if($s==$p[$i+2])
      return $i;
  return 10;
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  <title>Grafico</title>
</head>

<body>
  <div class="container-fluid" id="contenitore">
    <canvas id="grafico"></canvas>
  </div>
  <script src="secondarie.js"></script>
  <script>
    let risultati, intestazione;
    let file;
    //Prendo il file con la memoria
    fetch("https://www.umanetexpo.net/expo2015Server/UECDL/grafici/as_1920/report&3Ct$6.asp")
    .then(d => d.json())
    .then(d => {
      file = d;
      finito()
    })
    .catch(e => console.error(e));

    function finito() {
      risultati = file.risultati;
      intestazione = file.intestazione;
      let inizio=<?php echo $periodoInizio?>;
      let fine=<?php echo $periodoFine?>;
      let punti = prendiPunti(risultati,inizio,fine);
      let nomi = prendiNomi(risultati);
      resetCanvas(); //Elimino il grafico precedente per evitare che si sovrapponga
      bubbleSort(punti,nomi); //Ordino i nomi e i punti
      creaGrafico(nomi,punti); //Creo il grafico
    }
  </script>
</body>

</html>