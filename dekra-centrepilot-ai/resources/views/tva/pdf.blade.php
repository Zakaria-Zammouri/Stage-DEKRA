<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>État TVA {{ $mois->translatedFormat('F Y') }}</title>

<style>
body{ font-family: DejaVu Sans, sans-serif; font-size:13px; color:#333; margin:40px; }
h2{ text-align:center; border-bottom:3px solid #00853F; padding-bottom:15px; margin-bottom:30px; }
table{ width:100%; border-collapse:collapse; }
th, td{ border:1px solid #ccc; padding:8px; }
th{ background:#f5f5f5; text-align:left; }
.text-right{ text-align:right; }
.total-row{ font-weight:bold; background:#f0f0f0; }
</style>
</head>

<body>

<h2>État récapitulatif de TVA — {{ $mois->translatedFormat('F Y') }}</h2>

<table>
    <thead>
        <tr>
            <th>Type de véhicule</th>
            <th class="text-right">Nb factures</th>
            <th class="text-right">Total HT</th>
            <th class="text-right">Total TVA</th>
            <th class="text-right">Total TTC</th>
        </tr>
    </thead>
    <tbody>
        @foreach($recap['lignes'] as $ligne)
            <tr>
                <td>{{ $ligne['type'] }}</td>
                <td class="text-right">{{ $ligne['nombre'] }}</td>
                <td class="text-right">{{ number_format($ligne['ht'], 2, ',', ' ') }} DH</td>
                <td class="text-right">{{ number_format($ligne['tva'], 2, ',', ' ') }} DH</td>
                <td class="text-right">{{ number_format($ligne['ttc'], 2, ',', ' ') }} DH</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td>Total</td>
            <td></td>
            <td class="text-right">{{ number_format($recap['total_ht'], 2, ',', ' ') }} DH</td>
            <td class="text-right">{{ number_format($recap['total_tva'], 2, ',', ' ') }} DH</td>
            <td class="text-right">{{ number_format($recap['total_ttc'], 2, ',', ' ') }} DH</td>
        </tr>
    </tbody>
</table>

</body>
</html>