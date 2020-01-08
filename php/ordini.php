<?php
    require_once('funzione.php');

    $ammin = $_GET['ammin'];
    $title = '';
    if ($ammin == '0') {
        checkSession(false);
        $title = 'I MIEI ORDINI';
    }
    else if ($ammin == '1') {
        checkSession(true);
        $title = 'GESTIONE ORDINI';
    }
    else
        header('Location: index.php');
    
    myHeader($title, true);
    printMessage();

    $id = $_SESSION['id'];

    require_once('db/mysql_credentials.php');

    $query = 'SELECT idOrdine, dataOra, totale, isConsegna,';
    if ($ammin == '1')
        $query .= ' email, telefono,';
    $query .= ' matricola, modello.nome, marca, alimentazione, tipoModello, noPasseggeri,
                 peso, potenza, prezzo, larghezza, lunghezza, altezza, colore
                FROM ordine';
    if ($ammin == '1')
        $query .= ' NATURAL JOIN cliente';
    $query .= ' NATURAL JOIN veicolo
                JOIN modello ON veicolo.idModello = modello.idModello'; //not natural join because there is the relation GESTISCI
    if ($ammin == '0')
        $query .= ' WHERE idCliente=\''. $id. '\'';
    $query .= ' ORDER BY dataOra DESC';
    if ($ammin == '1')
        $query .= ', idOrdine ASC';

    $result = mysqli_query($con, $query);

    if ($result && mysqli_affected_rows($con) > 0) {
        $idOrdine = '';
        $i = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($idOrdine != $row['idOrdine']) {
                $idOrdine = $row['idOrdine'];
                $dataOra = new DateTime($row['dataOra']);
                echo '<br><strong>Ordine: '. $idOrdine. ' in data: '.
                        $dataOra->format('d-m-Y H:i:s');
                if ($ammin == '1')
                    echo ' Email: '. $row['email']. ' Telefono '. $row['telefono'];
                echo ' Totale: '. $row['totale'];
                if ($ammin == '0') {
                    $dataOra->add(new DateInterval('PT5M'));
                    $dataOra = $dataOra->format('Y-m-d H:i:s');
                    $dataOraAnnull = new DateTime;
                    $dataOraAnnull = $dataOraAnnull->format('Y-m-d H:i:s');
                    if ($dataOra > $dataOraAnnull)
                        echo ' <a href=\'annulla_ordine.php?idOrdine='. $idOrdine.
                                '&timeannull='. $dataOraAnnull. '\'>Annulla</a>';
                    else if ($row['isConsegna'] == 0)
                        echo ' IN PREPARAZIONE';
                    else
                        echo ' IN CONSEGNA';
                }
                else {
                    echo ' ';
                    if ($row['isConsegna'] == 0)
                        echo ' <a href=\'consegna_ordine.php?idOrdine='. $row['idOrdine'].
                                '\'>';
                    echo 'IN CONSEGNA';
                    if ($row['isConsegna'] == 0)
                        echo '</a>';
                }
                echo '</strong><br><b>Matricola Nome Marca Alimentazione TipoModello
                        NoPasseggeri Peso Potenza Prezzo Larghezza Lunghezza Altezza 
                        Colore</b><br>';
            }
            echo $row['matricola']. ' '. $row['nome']. ' '. $row['marca']. ' '.
                    $row['alimentazione']. ' '. $row['tipoModello']. ' ',
                    $row['noPasseggeri'], ' '. $row['peso']. ' '. $row['potenza'].
                    ' '. $row['prezzo'], ' '. $row['larghezza']. ' '. $row['lunghezza'].
                    ' '. $row['altezza']. ' '. $row['colore']. '<br>';
            $i++;
        }
    }
    else
        if ($ammin == '0')
            echo 'Non ci sono ordini';
        else
            echo 'Non hai ancora acquistato nessun prodotto';   

    mysqli_close($con);

    include('../html/footer.html');
?>