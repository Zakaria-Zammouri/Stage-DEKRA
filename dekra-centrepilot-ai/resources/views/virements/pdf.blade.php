<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

body{
font-family: DejaVu Sans;
font-size:14px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

td{
border:1px solid #000;
padding:8px;
}

h2{
text-align:center;
margin-bottom:30px;
}

</style>

</head>

<body>

<h2>Ordre de Virement</h2>

<table>

<tr>
<td><strong>Bénéficiaire</strong></td>
<td>{{ $virement->beneficiaire }}</td>
</tr>

<tr>
<td><strong>Montant</strong></td>
<td>{{ number_format($virement->montant,2,',',' ') }} DH</td>
</tr>

<tr>
<td><strong>Montant en lettres</strong></td>
<td>{{ $virement->montant_lettres }}</td>
</tr>

<tr>
<td><strong>Date</strong></td>
<td>{{ $virement->date_virement }}</td>
</tr>

</table>

</body>

</html>