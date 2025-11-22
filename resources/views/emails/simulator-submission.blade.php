<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle demande - Simulateur</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Arial', sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb 0%, #0284c7 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                🔧 Nouvelle Demande de Devis
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #e0f2fe; font-size: 16px;">
                                Via le Simulateur de Prix
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Alert Info -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #dbeafe; border-left: 4px solid #2563eb; border-radius: 8px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0; color: #1e3a8a; font-size: 14px;">
                                            <strong>📋 Référence :</strong> #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
                                            <br>
                                            <strong>📅 Date :</strong> {{ $submission->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Client Info -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 0 0 15px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                👤 Informations Client
                            </h2>
                            <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="color: #6b7280; width: 30%;"><strong>Nom :</strong></td>
                                    <td style="color: #1f2937; font-weight: 600;">{{ $submission->name }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280;"><strong>Email :</strong></td>
                                    <td style="color: #1f2937;">
                                        <a href="mailto:{{ $submission->email }}" style="color: #2563eb; text-decoration: none;">
                                            {{ $submission->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280;"><strong>Téléphone :</strong></td>
                                    <td style="color: #1f2937; font-weight: 600; font-size: 16px;">
                                        <a href="tel:{{ $submission->phone }}" style="color: #2563eb; text-decoration: none;">
                                            {{ $submission->phone }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280;"><strong>Adresse :</strong></td>
                                    <td style="color: #1f2937;">{{ $submission->address }}, {{ $submission->postal_code }} {{ $submission->city }}</td>
                                </tr>
                            </table>

                            <!-- Project Info -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 0 0 15px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                🔧 Détails du Projet
                            </h2>
                            <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="color: #6b7280; width: 30%;"><strong>Type de travaux :</strong></td>
                                    <td style="color: #1f2937; font-weight: 600;">
                                        {{ $workTypes[$data['work_type']]['name'] ?? $data['work_type'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280;"><strong>Urgence :</strong></td>
                                    <td style="color: #1f2937;">
                                        @if($submission->urgency_level == 'emergency')
                                            <span style="background-color: #dc2626; color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold;">🚨 URGENCE (48h)</span>
                                        @elseif($submission->urgency_level == 'urgent')
                                            <span style="background-color: #f97316; color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold;">⚡ Urgent (1 semaine)</span>
                                        @else
                                            <span style="background-color: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold;">✓ Normal (2-4 semaines)</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280;"><strong>Type de bien :</strong></td>
                                    <td style="color: #1f2937;">{{ ucfirst($submission->property_type) }}</td>
                                </tr>
                                @if(!empty($data['description']))
                                <tr>
                                    <td colspan="2" style="color: #6b7280; padding-top: 15px;">
                                        <strong>Description :</strong>
                                        <div style="background-color: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 8px; color: #1f2937;">
                                            {{ $data['description'] }}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </table>

                            <!-- CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="mailto:{{ $submission->email }}" 
                                           style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #0284c7 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 30px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);">
                                            Répondre au Client
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                Email généré automatiquement par le simulateur de prix
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

