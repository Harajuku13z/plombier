<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre demande de devis</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, {{ setting('primary_color', '#3b82f6') }} 0%, {{ setting('secondary_color', '#1e40af') }} 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .logo-wrapper {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 20px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid {{ setting('primary_color', '#3b82f6') }};
            padding: 20px;
            margin: 30px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin: 0 0 15px;
            color: #1e3a8a;
            font-size: 18px;
        }
        .info-item {
            padding: 8px 0;
            color: #1e293b;
        }
        .step-box {
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0;
            margin: 15px 0;
            overflow: hidden;
        }
        .step-header {
            background: linear-gradient(135deg, {{ setting('primary_color', '#3b82f6') }} 0%, {{ setting('secondary_color', '#1e40af') }} 100%);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .step-number {
            background-color: rgba(255, 255, 255, 0.3);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            flex-shrink: 0;
            border: 3px solid white;
        }
        .step-title {
            color: white;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .step-content {
            padding: 18px 20px;
            background-color: #f8fafc;
        }
        .step-content p {
            margin: 0;
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
        }
        .contact-box {
            background-color: #f0fdfa;
            border: 2px solid #14b8a6;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .contact-box a {
            color: #0f766e;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
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
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-wrapper">
                <span style="font-size: 48px;">✅</span>
            </div>
            <h1>Demande de Devis Bien Reçue !</h1>
            <p>Nous vous recontactons très rapidement</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Bonjour <strong>{{ $data['name'] ?? 'Client' }}</strong>,
            </div>

            <div class="message">
                <p style="margin: 0 0 15px;">
                    Merci d'avoir utilisé notre <strong>simulateur de devis en ligne</strong>. Nous avons bien reçu votre demande et notre équipe de plombiers professionnels l'analyse dès maintenant.
                </p>
            </div>

            <!-- Reference -->
            <div class="info-box">
                <h3>📋 Votre Référence</h3>
                <p style="margin: 0; font-size: 24px; font-weight: bold; color: #1e3a8a;">
                    #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
                </p>
                <p style="margin: 10px 0 0; color: #64748b; font-size: 14px;">
                    Conservez ce numéro pour toute correspondance
                </p>
            </div>

            <!-- Urgence Alert -->
            @if(($data['urgency'] ?? '') === 'emergency')
            <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0; color: #991b1b; font-weight: bold; font-size: 16px;">
                    🚨 URGENCE DÉTECTÉE
                </p>
                <p style="margin: 10px 0 0; color: #991b1b;">
                    Votre demande est <strong>prioritaire</strong>. Nous vous contactons dans les plus brefs délais.
                </p>
            </div>
            @endif

            <!-- Next Steps -->
            <h3 style="color: #1e293b; font-size: 22px; margin: 35px 0 25px; text-align: center; font-weight: bold;">
                📅 Prochaines Étapes
            </h3>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;">
                <tr>
                    <td>
                        <div class="step-box">
                            <div class="step-header">
                                <h4 class="step-title">⚙️ Analyse de votre demande</h4>
                            </div>
                            <div class="step-content">
                                <p>Un plombier professionnel étudie votre projet en détail et prépare une solution adaptée à vos besoins.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;">
                <tr>
                    <td>
                        <div class="step-box">
                            <div class="step-header">
                                <h4 class="step-title">📞 Contact sous 2 heures</h4>
                            </div>
                            <div class="step-content">
                                <p><strong style="color: #1e40af;">Nous vous appelons rapidement</strong> pour discuter de votre projet et répondre à toutes vos questions (jours ouvrés).</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;">
                <tr>
                    <td>
                        <div class="step-box">
                            <div class="step-header">
                                <h4 class="step-title">💼 Devis gratuit personnalisé</h4>
                            </div>
                            <div class="step-content">
                                <p>Vous recevez un <strong style="color: #10b981;">devis détaillé 100% gratuit</strong> et sans engagement, parfaitement adapté à votre situation.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Contact -->
            <div class="contact-box">
                <p style="margin: 0 0 15px; color: #0f766e; font-size: 16px; font-weight: bold;">
                    Besoin de nous contacter ?
                </p>
                <p style="margin: 0;">
                    📞 <a href="tel:{{ $companySettings['phone'] }}">{{ $companySettings['phone'] }}</a>
                </p>
                <p style="margin: 10px 0 0;">
                    📧 <a href="mailto:{{ $companySettings['email'] }}">{{ $companySettings['email'] }}</a>
                </p>
            </div>

            <div style="color: #64748b; font-size: 14px; line-height: 1.6; margin-top: 30px;">
                <p>Merci de votre confiance,</p>
                <p style="font-weight: bold; color: #1e293b; font-size: 16px;">
                    L'équipe {{ $companySettings['name'] }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $companySettings['name'] }}</strong></p>
            <p>{{ $companySettings['phone'] }} • {{ $companySettings['email'] }}</p>
            <p style="margin-top: 15px; font-size: 12px;">
                Cet email a été envoyé suite à votre demande sur notre site web.
            </p>
        </div>
    </div>
</body>
</html>

