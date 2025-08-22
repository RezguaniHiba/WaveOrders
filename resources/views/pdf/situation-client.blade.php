<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier Client - {{ $client->nom }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2d3748;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #1a365d;
        }
        .header h2 {
            font-size: 14px;
            margin: 4px 0;
            color: #4a5568;
        }
        .header p {
            font-size: 10px;
            color: #718096;
            margin: 0;
        }
        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #2b6cb0;
            margin: 15px 0 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #cbd5e0;
        }
        .client-card {
            background: #f8fafc;
            border-radius: 4px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 12px;
        }
        .client-info-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 6px;
        }
        .info-block {
            display: flex;
            align-items: center;
        }
        .info-label {
            font-weight: 600;
            margin-right: 4px;
            white-space: nowrap;
        }
        .info-value {
            color: #2d3748;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #edf2f7;
        }
        .summary-table tr:last-child td {
            background: #f8fafc;
            font-weight: 600;
        }
        .text-right { text-align: right; }
        .text-danger { color: #c53030; }
        .text-success { color: #276749; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background: #e2e8f0;
            padding: 5px;
            font-size: 10px;
            /*text-transform: uppercase;*/
            text-align: left;
        }
        .data-table td {
            padding: 5px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 5mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
        }
        .note-box {
            background: #fffaf0;
            border-left: 3px solid #ed8936;
            padding: 8px 12px;
            font-size: 10px;
            margin: 12px 0;
            border-radius: 0 4px 4px 0;
        }
        .note-box strong {
            color: #c05621;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }
       .status-en_cours_de_traitement { 
    background: #fffaf0;  /* orange clair */
    color: #c05621;       /* orange foncé */
}

.status-consignation {
    background: #ebf8ff;   /* bleu clair */
    color: #2b6cb0;       /* bleu foncé */
}

.status-partiellement_livree {
    background: #f0fff4;   /* vert clair */
    color: #276749;       /* vert foncé */ 
}

.status-complètement_livree {
    background: #f0fff4;   /* vert clair */
    color: #276749;       /* vert foncé */
    font-weight: 600;     /* plus visible */
}
    </style>
</head>
<body>

    <div class="header">
        <h1>SITUATION FINANCIÈRE CLIENT</h1>
        <h2>{{ $client->nom }}</h2>
        <p>Date de création : {{ $client->date_creation->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">INFORMATIONS CLIENT</div>
    <div class="client-card">
            <div class="info-block">
                <span class="info-label">Commercial responsable:</span>
                <span class="info-value">{{ $client->utilisateur->nom ?? 'Non attribué' }}</span>
            </div>
            <div class="info-block">
                <span class="info-label">Téléphone:</span>
                <span class="info-value">{{ $client->telephone ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-block">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $client->email ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-block" style="flex: 1;">
                <span class="info-label">Adresse:</span>
                <span class="info-value">
                    {{ $client->adresse .',' ?? '' }}
                    @if($client->code_postal)<strong>Code Postal: {{ $client->code_postal .',' ?? '' }}</strong>@endif
                    {{ $client->ville .',' ?? '' }} {{ $client->pays ?? '' }}
                </span>
            </div>
    </div>

    <div class="section-title">SYNTHÈSE FINANCIÈRE</div>
    <div class="summary-card">
        <table class="summary-table">
            <tr>
                <td>Total Commandes Valides (TTC)</td>
                <td class="text-right">{{ number_format($totalCommandes, 2, ',', ' ') }} DH</td>
            </tr>
            <tr>
                <td>Total Payé</td>
                <td class="text-right">{{ number_format($totalPaye, 2, ',', ' ') }} DH</td>
            </tr>
            <tr>
                <td>Solde à régler</td>
                <td class="text-right @if($totalRestant > 0) text-danger @else text-success @endif">
                    {{ number_format($totalRestant, 2, ',', ' ') }} DH
                </td>
            </tr>
        </table>
    </div>

    @if($countBrouillons > 0)
    <div class="note-box">
        <strong>Note :</strong> Ce client a {{ $countBrouillons }} commande(s) en brouillon non incluses dans les totaux.
    </div>
    @endif

    @if($client->commandes->count() > 0)
        <div class="section-title">DÉTAIL DES COMMANDES</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>N° de Commande</th>
                    <th>Date</th>
                    <th>Montant HT</th>
                    <th>TVA</th>
                    <th>Montant TTC</th>
                    <th>Payé</th>
                    <th>Reste</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->commandes->whereNotIn('statut', ['brouillon'])->sortByDesc('date_commande') as $commande)
                    @php
                        $paye = $commande->reglements->sum('montant');
                        $reste = $commande->montant_ttc - $paye;
                        $statusClass = 'status-' . $commande->statut; 
                    @endphp
                    <tr>
                        <td>{{ $commande->numero }}</td>
                        <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($commande->montant_ht, 2, ',', ' ') }} DH</td>
                        <td class="text-right">{{ number_format($commande->montant_tva, 2, ',', ' ') }} DH</td>
                        <td>{{ number_format($commande->montant_ttc, 2, ',', ' ') }} DH</td>
                        <td class="text-right">{{ number_format($paye, 2, ',', ' ') }} DH</td>
                        <td class="text-right @if($reste > 0) text-danger @else text-success @endif">
                            {{ number_format($reste, 2, ',', ' ') }} DH
                        </td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($client->reglements->count() > 0)
    <div class="section-title">HISTORIQUE DES RÈGLEMENTS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Montant</th>
                <th>Mode de paiement</th>
                <th>Type de Facturation</th>
                <th>Payeur</th>
                <th>Commande associée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($client->reglements->sortByDesc('date_reglement') as $reglement)
                <tr>
                    <td>{{ $reglement->date_reglement->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($reglement->montant, 2, ',', ' ') }} DH</td>
                    <td>{{ $reglement->mode_libelle }}</td>
                    <td>{{ $reglement->type_facturation_libelle }}</td>
                    <td>{{ $reglement->clientPayeur->nom ?? $client->nom }}</td>
                    <td>{{ $reglement->commande->numero }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>