<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Vencido</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header -->
        <tr>
            <td style="background-color: #d97706; padding: 24px 32px; border-radius: 8px 8px 0 0; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 600;">
                    {{ config('app.name', 'InterSys') }}
                </h1>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="background-color: #ffffff; padding: 32px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">

                <!-- Warning Icon -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="text-align: center; padding-bottom: 24px;">
                            <div style="display: inline-block; width: 56px; height: 56px; background-color: #f59e0b; border-radius: 50%; line-height: 56px; text-align: center;">
                                <span style="color: #ffffff; font-size: 28px;">&#9888;</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <h2 style="color: #111827; font-size: 18px; font-weight: 600; margin: 0 0 16px 0; text-align: center;">
                    Pago Vencido
                </h2>

                <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; text-align: center;">
                    Se ha detectado un pago vencido con los siguientes detalles:
                </p>

                <!-- Details Table -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #fffbeb; border-radius: 6px; margin-bottom: 24px; border: 1px solid #fde68a;">
                    <tr>
                        <td style="padding: 16px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500;">
                                        Cliente
                                    </td>
                                    <td style="padding: 8px 0; color: #111827; font-size: 13px; text-align: right; font-weight: 600;">
                                        {{ $client->nombre ?? 'N/A' }} {{ $client->apellido ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500; border-top: 1px solid #fde68a;">
                                        Monto
                                    </td>
                                    <td style="padding: 8px 0; color: #111827; font-size: 13px; text-align: right; font-weight: 600; border-top: 1px solid #fde68a;">
                                        ${{ number_format($payment->costo ?? 0, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500; border-top: 1px solid #fde68a;">
                                        Días de Atraso
                                    </td>
                                    <td style="padding: 8px 0; color: #d97706; font-size: 13px; text-align: right; font-weight: 600; border-top: 1px solid #fde68a;">
                                        {{ $daysOverdue }} días
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #6b7280; font-size: 13px; font-weight: 500; border-top: 1px solid #fde68a;">
                                        Fecha de Pago
                                    </td>
                                    <td style="padding: 8px 0; color: #111827; font-size: 13px; text-align: right; font-weight: 600; border-top: 1px solid #fde68a;">
                                        {{ $payment->fecha_pago ? \Carbon\Carbon::parse($payment->fecha_pago)->format('d/m/Y') : 'Pendiente' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <p style="color: #6b7280; font-size: 12px; line-height: 1.5; margin: 0; text-align: center;">
                    Por favor, regularice su situación para evitar la suspensión del servicio.
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
