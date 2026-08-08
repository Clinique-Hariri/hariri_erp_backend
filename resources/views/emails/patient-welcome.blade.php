<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #2a9a52; padding: 30px 40px; text-align: center;">
                            <img src="https://cliniquehariri.com/assets/img/logo/logo.png"
                                 alt="Clinique Hariri Internationale"
                                 style="max-width: 180px; height: auto;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #2a9a52; margin: 0 0 20px; font-size: 22px;">
                                Bienvenue {{ $patientName }} !
                            </h2>

                            <p style="color: #444444; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
                                Votre dossier patient a ete cree avec succes a la Clinique Hariri Internationale.
                            </p>

                            <p style="color: #444444; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
                                Nous sommes heureux de vous compter parmi nos patients. Votre sante est notre priorite.
                            </p>

                            <!-- Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9; border-radius: 6px; padding: 20px; margin: 0 0 24px;">
                                <tr>
                                    <td style="padding: 6px 0; color: #666666; font-size: 14px;">
                                        <strong>Numero de dossier :</strong> {{ $patientNumber }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; color: #666666; font-size: 14px;">
                                        <strong>Nom :</strong> {{ $patientName }}
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #444444; font-size: 14px; line-height: 1.6; margin: 0;">
                                Conservez votre numero de dossier pour toute communication future avec notre etablissement.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2a9a52; padding: 20px 40px; text-align: center;">
                            <p style="color: #ffffff; font-size: 13px; margin: 0;">
                                Merci pour votre confiance &mdash; Clinique Hariri Internationale.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
