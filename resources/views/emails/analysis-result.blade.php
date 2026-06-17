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
                                Bonjour cher(e) patient(e),
                            </h2>

                            <p style="color: #444444; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
                                Bonne nouvelle ! Vos resultats d'analyses sont maintenant disponibles.
                            </p>

                            <p style="color: #444444; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
                                A la Clinique Hariri Internationale, votre sante reste notre priorite.
                            </p>

                            <!-- CTA Button -->
                            <table cellpadding="0" cellspacing="0" style="margin: 0 0 30px;">
                                <tr>
                                    <td style="background-color: #2691c5; border-radius: 6px; text-align: center;">
                                        <a href="{{ $pdfUrl }}"
                                           style="display: inline-block; padding: 14px 36px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold;">
                                            Voir le rapport
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9; border-radius: 6px; padding: 20px; margin: 0 0 24px;">
                                <tr>
                                    <td style="padding: 6px 0; color: #666666; font-size: 14px;">
                                        <strong>Date des analyses :</strong> {{ $date }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; color: #666666; font-size: 14px;">
                                        <strong>Reference :</strong> {{ $reference }}
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #444444; font-size: 14px; line-height: 1.6; margin: 0 0 8px;">
                                Pour votre securite et un meilleur suivi, nous vous recommandons de consulter votre medecin avec ces resultats.
                            </p>

                            <p style="color: #444444; font-size: 14px; line-height: 1.6; margin: 0;">
                                Plus d'infos :
                                <a href="tel:{{ $contact }}" style="color: #2691c5; text-decoration: none;">
                                    {{ $contact }}
                                </a>
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
