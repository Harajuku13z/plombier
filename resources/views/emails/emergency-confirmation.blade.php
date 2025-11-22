<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre demande d'urgence</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Arial', sans-serif; background-color: #fef2f2;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fef2f2; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(220, 38, 38, 0.2); border: 2px solid #dc2626;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 30px; text-align: center;">
                            <div style="background-color: #ffffff; color: #dc2626; width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-center; margin-bottom: 15px;">
                                <span style="font-size: 48px;">✅</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Demande d'Urgence Reçue
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #fecaca; font-size: 16px;">
                                Nous vous contactons dans les plus brefs délais
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px 0; color: #1f2937; font-size: 16px; line-height: 1.6;">
                                Bonjour <strong>{{ $submission->name }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #1f2937; font-size: 16px; line-height: 1.6;">
                                Nous avons bien reçu votre demande d'intervention urgente. Notre équipe de plombiers professionnels va vous contacter dans les <strong style="color: #dc2626;">15 prochaines minutes</strong>.
                            </p>

                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fef2f2; border-left: 4px solid #dc2626; border-radius: 8px; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                            📋 Référence de votre demande : #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                            📅 Date : {{ $submission->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                        <p style="margin: 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                            🚨 Type d'urgence : {{ ucfirst(str_replace('-', ' ', $emergency_type)) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 25px 0 15px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                ⏱️ Prochaines Étapes
                            </h2>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 15px 0; border-bottom: 1px solid #e5e7eb;">
                                        <div style="color: #dc2626; font-size: 24px; font-weight: bold; margin-bottom: 5px;">1</div>
                                        <div style="color: #1f2937; font-size: 16px; font-weight: 600; margin-bottom: 5px;">Appel Imminent</div>
                                        <div style="color: #6b7280; font-size: 14px;">Un plombier vous contacte dans les 15 prochaines minutes</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 0; border-bottom: 1px solid #e5e7eb;">
                                        <div style="color: #dc2626; font-size: 24px; font-weight: bold; margin-bottom: 5px;">2</div>
                                        <div style="color: #1f2937; font-size: 16px; font-weight: 600; margin-bottom: 5px;">Intervention Rapide</div>
                                        <div style="color: #6b7280; font-size: 14px;">Selon la gravité, intervention possible sous 1 heure</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 0;">
                                        <div style="color: #dc2626; font-size: 24px; font-weight: bold; margin-bottom: 5px;">3</div>
                                        <div style="color: #1f2937; font-size: 16px; font-weight: 600; margin-bottom: 5px;">Service Professionnel</div>
                                        <div style="color: #6b7280; font-size: 14px;">Plombiers certifiés avec matériel professionnel</div>
                                    </td>
                                </tr>
                            </table>

                            @if($submission->photos && count($submission->photos) > 0)
                            <div style="background-color: #f0f9ff; border: 2px solid #bae6fd; border-radius: 8px; padding: 15px; margin: 20px 0;">
                                <p style="margin: 0; color: #0c4a6e; font-weight: 600;">
                                    📸 <strong>{{ count($submission->photos) }} photo(s)</strong> ont été envoyées avec votre demande
                                </p>
                            </div>
                            @endif

                            <!-- Contact Info -->
                            <div style="background-color: #fef3c7; padding: 20px; border-radius: 12px; margin: 25px 0; text-align: center;">
                                <p style="margin: 0 0 10px 0; color: #991b1b; font-size: 14px; font-weight: bold;">
                                    Vous n'avez pas été contacté dans les 30 minutes ?
                                </p>
                                <a href="tel:{{ str_replace(' ', '', setting('company_phone', '07 86 48 65 39')) }}" 
                                   style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 25px; font-weight: bold; font-size: 18px;">
                                    📞 {{ setting('company_phone', '07 86 48 65 39') }}
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #fef2f2; padding: 20px 30px; text-align: center; border-top: 1px solid #fecaca;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 12px;">
                                {{ setting('company_name', 'Plombier Versailles') }}<br>
                                {{ setting('company_address', '35 Rue des Chantiers, 78000 Versailles, France') }}
                            </p>
                            <p style="margin: 0; color: #991b1b; font-size: 12px; font-weight: bold;">
                                Service disponible 24h/24 et 7j/7
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

