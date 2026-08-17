<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuota Generada</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <!-- Container -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header -->
        <tr>
            <td style="background-color: #1e40af; padding: 24px 32px; border-radius: 8px 8px 0 0; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 600;">
                    {{ config('app.name', 'InterSys') }}
                </h1>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="background-color: #ffffff; padding: 32px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">

                <!-- Success Icon -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="text-align: center; padding-bottom: 24px;">
                            <div style="display: inline-block; width: 56px; height: 56px; background-color: #10b981; border-radius: 50%; line-height: 56px; text-align: center;">
                                <span style="color: #ffffff; font-size: 28px;">&#10003;</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <h2 style="color: #111827; font-size: 18px; font-weight: 600; margin: 0 0 16px 0; text-align: center;">
                    Cuota Mensual Generada
                </h2>

                <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; text-align: center;">
                    Se generó correctamente la cuota del período con los siguientes detalles:
                </p>

                <!-- Details Table -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; border-radius: 6px; margin-bottom: 24px;">
                    <tr>
                        <td style="padding: 16px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500;">
                                        Número de Cuota
                                    </td>
                                    <td style="padding: 8px 0; color: #111827; font-size: 13px; text-align: right; font-weight: 600;">
                                        #{{ $quota->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500; border-top: 1px solid #e5e7eb;">
                                        Período
                                    </td>
                                    <td style="padding: 8px 0; color: #111827; font-size: 13px; text-align: right; font-weight: 600; border-top: 1px solid #e5e7eb;">
                                        {{ $quota->numero }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500; border-top: 1px solid #e5e7eb;">
                                        Clientes Notificados
                                    </td>
                                    <td style="padding: 8px 0; color: #10b981; font-size: 13px; text-align: right; font-weight: 600; border-top: 1px solid #e5e7eb;">
                                        {{ $clientCount }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500; border-top: 1px solid #e5e7eb;">
                                        Fecha de Generación
                                    </td>
                                    <td style="padding: 8px 0; color: #111827; font-size: 13px; text-align: right; font-weight: 600; border-top: 1px solid #e5e7eb;">
                                        {{ $quota->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <p style="color: #6b7280; font-size: 12px; line-height: 1.5; margin: 0; text-align: center;">
                    Los pagos correspondientes han sido creados automáticamente para cada cliente.
                </p>

            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 16px 32px; text-align: center;">
                <p style="color: #9ca3af; font-size: 11px; margin: 0;">
                    Este es un mensaje automático de {{ config('app.name', 'InterSys') }}. No responda a este email.
                </p>
                <p style="color: #9ca3af; font-size: 11px; margin: 4px 0 0 0;">
                    &copy; {{ date('Y') }} {{ config('app.name', 'InterSys') }}. Todos los derechos reservados.
                </p>
            </td>
        </tr>

    </table>

</body>
</html>
