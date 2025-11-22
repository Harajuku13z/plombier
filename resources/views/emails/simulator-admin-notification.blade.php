<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Demande Simulateur</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 700px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, {{ setting('primary_color', '#1e40af') }} 0%, {{ setting('secondary_color', '#1e3a8a') }} 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }
        .header .badge {
            display: inline-block;
            background-color: #fbbf24;
            color: #1e3a8a;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 15px;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .alert-box p {
            margin: 0;
            color: #92400e;
            font-weight: 600;
        }
        .client-info {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
        }
        .client-info h2 {
            margin: 0 0 20px;
            color: #1e293b;
            font-size: 20px;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #475569;
            width: 150px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1e293b;
            font-weight: 500;
        }
        .phone-highlight {
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
            border: 2px solid #93c5fd;
        }
        .phone-highlight a {
            color: #1e40af;
            text-decoration: none;
            font-size: 28px;
            font-weight: bold;
        }
        .urgency-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .urgency-emergency {
            background-color: #dc2626;
            color: white;
            animation: pulse 2s infinite;
        }
        .urgency-urgent {
            background-color: #f97316;
            color: white;
        }
        .urgency-normal {
            background-color: #10b981;
            color: white;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, {{ setting('primary_color', '#1e40af') }} 0%, {{ setting('secondary_color', '#1e3a8a') }} 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            margin: 10px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 5px 0;
            color: #64748b;
            font-size: 13px;
        }
        .timestamp {
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 12px 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🔔 Nouvelle Demande de Devis</h1>
            <div class="badge">VIA SIMULATEUR DE PLOMBERIE</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Alert -->
            <div class="alert-box">
                <p>⚡ NOUVELLE DEMANDE ! Un client a complété le simulateur et attend votre réponse.</p>
            </div>

            <!-- Timestamp -->
            <div class="timestamp">
                📅 Reçue le {{ $submission->created_at->format('d/m/Y à H:i') }} • Référence #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
            </div>

            <!-- Client Info -->
            <div class="client-info">
                <h2>👤 Contact Client</h2>
                
                <div class="info-row">
                    <div class="info-label">Nom :</div>
                    <div class="info-value"><strong>{{ $submission->form_data['name'] ?? 'N/A' }}</strong></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Email :</div>
                    <div class="info-value">
                        <a href="mailto:{{ $submission->email }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                            {{ $submission->email }}
                        </a>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Téléphone :</div>
                    <div class="info-value">
                        <strong style="font-size: 18px; color: #1e40af;">{{ $submission->phone }}</strong>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Adresse :</div>
                    <div class="info-value">{{ $submission->form_data['address'] ?? '' }}<br>{{ $submission->postal_code }} {{ $submission->city }}</div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="phone-highlight">
                <p style="margin: 0 0 12px; color: #1e40af; font-size: 15px; font-weight: 600;">👆 Appeler le client maintenant</p>
                <a href="tel:{{ $submission->phone }}">📞 {{ $submission->phone }}</a>
            </div>

            <!-- Project Details -->
            <div class="client-info">
                <h2>🔧 Détails du Projet</h2>
                
                <div class="info-row">
                    <div class="info-label">Travaux demandés :</div>
                    <div class="info-value">
                        <strong style="color: #1e40af;">
                            @if(is_array($submission->work_types))
                                {{ implode(', ', array_map(function($type) use ($workTypes) {
                                    return $workTypes[$type]['name'] ?? $type;
                                }, $submission->work_types)) }}
                            @else
                                {{ $submission->work_types }}
                            @endif
                        </strong>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Niveau d'urgence :</div>
                    <div class="info-value">
                        @php $urgency = $submission->form_data['urgency'] ?? 'normal'; @endphp
                        @if($urgency === 'emergency')
                            <span class="urgency-badge urgency-emergency">🚨 URGENCE (48h)</span>
                        @elseif($urgency === 'urgent')
                            <span class="urgency-badge urgency-urgent">⚡ Urgent (1 semaine)</span>
                        @else
                            <span class="urgency-badge urgency-normal">✓ Normal (2-4 semaines)</span>
                        @endif
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Type de bien :</div>
                    <div class="info-value">{{ $submission->property_type === 'HOUSE' ? 'Maison 🏠' : 'Appartement 🏢' }}</div>
                </div>
                
                @if(!empty($submission->form_data['description']))
                <div style="padding: 20px 0; border-top: 1px solid #e2e8f0; margin-top: 15px;">
                    <div class="info-label" style="margin-bottom: 12px;">💬 Description :</div>
                    <div style="background-color: #f1f5f9; padding: 18px; border-radius: 8px; color: #1e293b; line-height: 1.7; font-size: 15px;">
                        {{ $submission->form_data['description'] }}
                    </div>
                </div>
                @endif
                
                @if(!empty($submission->form_data['photo_paths']) && count($submission->form_data['photo_paths']) > 0)
                <div class="info-row">
                    <div class="info-label">Photos :</div>
                    <div class="info-value">
                        📸 <strong>{{ count($submission->form_data['photo_paths']) }} photo(s)</strong> uploadée(s)
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                            Accédez aux photos via l'administration
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="mailto:{{ $submission->email }}" class="cta-button">
                    ✉️ Envoyer un Email
                </a>
                <a href="tel:{{ $submission->phone }}" class="cta-button">
                    📞 Appeler
                </a>
            </div>

            <div style="background-color: #f0f9ff; border: 2px solid #bae6fd; border-radius: 8px; padding: 20px; text-align: center;">
                <p style="margin: 0; color: #0c4a6e; font-weight: 600; font-size: 15px;">
                    💡 <strong>Conseil :</strong> Contactez le client rapidement pour maximiser vos chances de conversion !
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: bold; color: #1e293b; font-size: 14px;">{{ setting('company_name', 'Plombier Versailles') }}</p>
            <p>{{ setting('company_phone', '07 86 48 65 39') }} • {{ setting('company_email', 'contact@plombier-versailles78.fr') }}</p>
            <p style="margin-top: 15px;">Email généré automatiquement par le simulateur de plomberie</p>
            <p style="font-size: 11px; color: #94a3b8;">Cette notification a été envoyée à l'administrateur du site</p>
        </div>
    </div>
</body>
</html>

