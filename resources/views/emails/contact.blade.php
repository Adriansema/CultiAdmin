<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body>
    <h2>Nuevo mensaje de contacto recibido</h2>
    <p><strong>Nombre:</strong> {{ $data['name'] }}</p>
    <p><strong>Correo:</strong> {{ $data['email'] }}</p>
    <p><strong>Mensaje:</strong><br> {{ $data['message'] }}</p>
</body>
</html>
