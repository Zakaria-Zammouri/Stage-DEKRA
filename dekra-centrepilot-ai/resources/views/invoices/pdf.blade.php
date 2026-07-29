<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<title>Facture {{ $invoice->numero }}</title>

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:13px;
    color:#333;
    margin:40px;
}

.header{
    width:100%;
    border-bottom:3px solid #00853F;
    padding-bottom:15px;
    margin-bottom:30px;
}

.logo{
    width:120px;
    text-align:center;
    background:#00853F;
    color:white;
    font-size:24px;
    font-weight:bold;
    padding:10px;
}

.company{
    float:right;
    text-align:right;
}

.company h2{
    margin:0;
    color:#16233A;
}

.company p{
    margin:2px;
    color:#666;
}

.clear{
    clear:both;
}

.title{

    text-align:center;
    margin-top:25px;
    margin-bottom:25px;

}

.title h1{

    color:#16233A;
    margin:0;

}

.info{

    width:100%;
    margin-bottom:25px;

}

.info td{

    padding:5px;

}

table.facture{

    width:100%;
    border-collapse:collapse;

}

.facture th{

    background:#16233A;
    color:white;
    padding:12px;
    text-align:left;

}

.facture td{

    border:1px solid #DDD;
    padding:12px;

}

.total{

    background:#F2F2F2;
    font-weight:bold;
    font-size:15px;

}

.status{

    margin-top:25px;
    font-size:16px;

}

.footer{

    margin-top:70px;
    text-align:center;
    color:#777;
    font-size:11px;

}

.signature{

    margin-top:80px;

}

.signature td{

    width:50%;
    text-align:center;

}

</style>

</head>

<body>

<div class="header">

    <div class="logo">

        DEKRA

    </div>

    <div class="company">

        <h2>DEKRA CentrePilot AI</h2>

        <p>Centre de contrôle technique</p>

        <p>Adresse : Votre centre DEKRA</p>

        <p>Téléphone : +212 XX XX XX XX XX</p>

        <p>Email : contact@dekra.ma</p>

    </div>

    <div class="clear"></div>

</div>

<div class="title">

    <h1>FACTURE</h1>

</div>

<table class="info">

<tr>

<td>

<strong>Numéro :</strong>

{{ $invoice->numero }}

</td>

<td align="right">

<strong>Date :</strong>

{{ $invoice->date_facture->format('d/m/Y') }}

</td>

</tr>

<tr>

<td colspan="2">

<strong>Client :</strong>

{{ $invoice->client }}

</td>

</tr>

<tr>

<td colspan="2">

<strong>Objet :</strong>

{{ $invoice->objet }}

</td>

</tr>

</table>

<table class="facture">

<thead>

<tr>

<th>Désignation</th>

<th align="right">Montant</th>

</tr>

</thead>

<tbody>

<tr>

<td>Prestation DEKRA</td>

<td align="right">

{{ number_format($invoice->montant_ht,2,',',' ') }} DH

</td>

</tr>

<tr>

<td>TVA</td>

<td align="right">

{{ number_format($invoice->tva,2,',',' ') }} DH

</td>

</tr>

<tr class="total">

<td>TOTAL TTC</td>

<td align="right">

{{ number_format($invoice->montant_ttc,2,',',' ') }} DH

</td>

</tr>

</tbody>

</table>

<div class="status">

<strong>Statut :</strong>

@if($invoice->statut=="Payée")

<span style="color:green">

✔ FACTURE PAYÉE

</span>

@else

<span style="color:red">

✘ FACTURE NON PAYÉE

</span>

@endif

</div>

<table class="signature">

<tr>

<td>

_________________________

<br>

Signature du client

</td>

<td>

_________________________

<br>

Signature DEKRA

</td>

</tr>

</table>

<div class="footer">

Document généré automatiquement par <strong>DEKRA CentrePilot AI</strong>.

<br>

Merci de votre confiance.

</div>

</body>

</html>