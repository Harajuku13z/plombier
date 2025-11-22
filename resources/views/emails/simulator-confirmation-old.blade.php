<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Devis Reçue</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Arial', sans-serif; background-color: #f0f9ff;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f9ff; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(37, 99, 235, 0.15);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb 0%, #0284c7 100%); padding: 40px 30px; text-align: center;">
                            <div style="background-color: #ffffff; width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <span style="font-size: 48px;">✅</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Demande de Devis Bien Reçue !
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #bfdbfe; font-size: 16px;">
                                Nous vous recontactons très rapidement
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <p style="margin: 0 0 20px 0; color: #1f2937; font-size: 16px; line-height: 1.6;">
                                Bonjour <strong>{{ $data['name'] ?? 'Client' }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 25px 0; color: #1f2937; font-size: 16px; line-height: 1.6;">
                                Nous avons bien reçu votre demande de devis via notre simulateur en ligne. Votre demande est notre priorité !
                            </p>
                            
                            <!-- Reference Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #eff6ff; border-left: 4px solid #2563eb; border-radius: 8px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0; color: #1e3a8a; font-size: 14px;">
                                            <strong>📋 Numéro de référence :</strong> <span style="font-size: 18px; font-weight: bold;">#{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- What happens next -->
                            <h2 style="color: #1f2937; font-size: 20px; font-weight: bold; margin: 0 0 20px 0;">
                                📅 Prochaines Étapes
                            </h2>
                            
                            <table width="100%" cellpadding="12" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="vertical-align: top; width: 60px;">
                                        <div style="background-color: #2563eb; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px;">1</div>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <h3 style="margin: 0 0 5px 0; color: #1f2937; font-size: 16px; font-weight: bold;">Analyse de votre demande</h3>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Un plombier professionnel étudie votre projet en détail</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div style="background-color: #2563eb; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px;">2</div>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <h3 style="margin: 0 0 5px 0; color: #1f2937; font-size: 16px; font-weight: bold;">Contact sous 2 heures</h3>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Nous vous appelons pour discuter de votre projet (jours ouvrés)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div style="background-color: #2563eb; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px;">3</div>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <h3 style="margin: 0 0 5px 0; color: #1f2937; font-size: 16px; font-weight: bold;">Devis personnalisé gratuit</h3>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Vous recevez un devis détaillé adapté à vos besoins</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Urgence Box -->
                            @if(($data['urgency'] ?? '') === 'emergency')
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fef2f2; border-left: 4px solid #dc2626; border-radius: 8px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0; color: #991b1b; font-size: 15px; font-weight: bold;">
                                            🚨 URGENCE DÉTECTÉE
                                        </p>
                                        <p style="margin: 5px 0 0 0; color: #991b1b; font-size: 14px;">
                                            Votre demande est prioritaire. Nous vous contactons dans les plus brefs délais.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Contact Info -->
                            <p style="margin: 25px 0 15px 0; color: #1f2937; font-size: 16px; line-height: 1.6;">
                                <strong>Besoin de nous contacter ?</strong>
                            </p>
                            <table width="100%" cellpadding="12" cellspacing="0" style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 25px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 8px 0; color: #1f2937; font-size: 15px;">
                                            📞 <strong>Téléphone :</strong> <a href="tel:{{ $companySettings['phone'] }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">{{ $companySettings['phone'] }}</a>
                                        </p>
                                        <p style="margin: 0; color: #1f2937; font-size: 15px;">
                                            📧 <strong>Email :</strong> <a href="mailto:{{ $companySettings['email'] }}" style="color: #2563eb; text-decoration: none;">{{ $companySettings['email'] }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Merci de votre confiance,<br>
                                <strong style="color: #1f2937;">L'équipe {{ $companySettings['name'] }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                Cet email a été envoyé automatiquement suite à votre demande sur notre site web.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

