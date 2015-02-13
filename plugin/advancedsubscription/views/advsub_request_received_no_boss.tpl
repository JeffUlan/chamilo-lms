<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitud recibida para el curso {{ session.name }}</title>
    <style type="text/css">
        .titulo {
            color: #93c5cd;
            font-family: "Times New Roman", Times, serif;
            font-size: 24px;
            font-weight: bold;
            border-bottom-width: 2px;
            border-bottom-style: solid;
            border-bottom-color: #93c5cd;
         }
    </style>
</head>

<body>
<table width="700" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><img src="{{ _p.web_plugin }}advancedsubscription/views/img/header.png" width="700" height="20" alt=""></td>
    </tr>
    <tr>
        <td><img src="{{ _p.web_plugin }}advancedsubscription/views/img/line.png" width="700" height="25" alt=""></td>
    </tr>
    <tr>
        <td valign="top"><table width="700" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="50">&nbsp;</td>
                    <td width="394"><img src="{{ _p.web_plugin }}advancedsubscription/views/img/logo-minedu.png" width="230" height="60" alt="Ministerio de Educación"></td>
                    <td width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td style="color: #93c5cd; font-family: Times New Roman, Times, serif; font-size: 24px; font-weight: bold; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: #93c5cd;">Solicitud recibida para el curso {{ session.name }}</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td height="356">&nbsp;</td>
                    <td valign="top"><p>Estimado:</p>
                        <h2>{{ student.complete_name }}</h2>
                        <p>Hemos recibido y registrado su solicitud de inscripción al curso <strong>{{ session.name }}</strong> para iniciarse el <strong>{{ session.date_start }}</strong>.</p>
                        <p>Su inscripción es pendiente de la disponibilidad de cupos. Pronto recibirá los resultados de su aprobación de su solicitud.</p>
                        <p>Gracias.</p>
                        <p><strong>Equipo Forge</strong></p></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="50">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td width="50">&nbsp;</td>
                </tr>
            </table></td>
    </tr>
    <tr>
        <td><img src="{{ _p.web_plugin }}advancedsubscription/views/img/line.png" width="700" height="25" alt=""></td>
    </tr>
    <tr>
        <td><img src="{{ _p.web_plugin }}advancedsubscription/views/img/footer.png" width="700" height="20" alt=""></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
</body>
</html>
