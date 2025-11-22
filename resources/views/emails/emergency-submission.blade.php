<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚨 URGENCE PLOMBERIE</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Arial', sans-serif; background-color: #fef2f2;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fef2f2; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(220, 38, 38, 0.2); border: 3px solid #dc2626;">
                    
                    <!-- Header URGENCE -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 30px; text-align: center;">
                            <div style="background-color: #ffffff; color: #dc2626; width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-center; margin-bottom: 15px; animation: pulse 2s infinite;">
                                <span style="font-size: 48px;">🚨</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
                                URGENCE PLOMBERIE
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #fecaca; font-size: 18px; font-weight: bold;">
                                Demande d'intervention immédiate
                            </p>
                        </td>
                    </tr>

                    <!-- Alert Critical -->
                    <tr>
                        <td style="padding: 20px 30px 0 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fee2e2; border-left: 4px solid #dc2626; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                            ⚡ ACTION REQUISE : Contacter le client immédiatement
                                            <br>
                                            📋 Référence : #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
                                            <br>
                                            📅 Date : {{ $submission->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Type d'urgence -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #dc2626, #991b1b); border-radius: 12px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="color: #ffffff; font-size: 16px; margin-bottom: 5px;">Type d'urgence</div>
                                        <div style="color: #ffffff; font-size: 28px; font-weight: bold;">
                                            {{ strtoupper($emergency_type) }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Client Info -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 0 0 15px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                👤 Informations Client
                            </h2>
                            <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="color: #6b7280; width: 30%; font-weight: bold;">Nom :</td>
                                    <td style="color: #1f2937; font-weight: 600; font-size: 18px;">{{ $submission->name }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280; font-weight: bold;">Téléphone :</td>
                                    <td style="background-color: #fef3c7; padding: 10px; border-radius: 8px;">
                                        <a href="tel:{{ $submission->phone }}" style="color: #991b1b; text-decoration: none; font-weight: bold; font-size: 20px;">
                                            📞 {{ $submission->phone }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280; font-weight: bold;">Email :</td>
                                    <td style="color: #1f2937;">
                                        <a href="mailto:{{ $submission->email }}" style="color: #2563eb; text-decoration: none;">
                                            {{ $submission->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6b7280; font-weight: bold;">Adresse :</td>
                                    <td style="color: #1f2937; font-weight: 600;">{{ $submission->address }}</td>
                                </tr>
                            </table>

                            <!-- Description -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 0 0 15px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                📝 Description de l'Urgence
                            </h2>
                            <div style="background-color: #fef2f2; padding: 20px; border-radius: 12px; border-left: 4px solid #dc2626; margin-bottom: 25px;">
                                <p style="margin: 0; color: #1f2937; font-size: 15px; line-height: 1.6; white-space: pre-wrap;">{{ $submission->message }}</p>
                            </div>

                            @if(isset($photoUrls) && count($photoUrls) > 0)
                            <!-- Photos -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 25px 0 15px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                📷 Photos de l'Urgence ({{ count($photoUrls) }})
                            </h2>
                            <div style="margin-bottom: 25px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                @foreach($photoUrls as $index => $photoUrl)
                                                <div style="flex: 0 0 calc(50% - 5px); max-width: 280px; margin-bottom: 10px;">
                                                    <a href="{{ $photoUrl }}" target="_blank" style="display: block; border: 2px solid #dc2626; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                                        <img src="{{ $photoUrl }}" 
                                                             alt="Photo urgence {{ $index + 1 }}" 
                                                             style="width: 100%; height: auto; display: block; max-height: 200px; object-fit: cover;" />
                                                        <div style="background-color: #fef2f2; padding: 8px; text-align: center; font-size: 12px; color: #991b1b; font-weight: bold;">
                                                            Photo {{ $index + 1 }}
                                                        </div>
                                                    </a>
                                                </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif

                            <!-- CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="tel:{{ $submission->phone }}" 
                                           style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff; text-decoration: none; padding: 18px 50px; border-radius: 30px; font-weight: bold; font-size: 18px; box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);">
                                            📞 APPELER LE CLIENT MAINTENANT
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #fef2f2; padding: 20px 30px; text-align: center; border-top: 1px solid #fecaca;">
                            <p style="margin: 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                ⚠️ Email d'urgence - Réponse requise sous 15 minutes
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

