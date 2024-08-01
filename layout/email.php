<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gmail Simulado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f3f4;
        }
        .header {
            background-color: #4285f4;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #4285f4;
            color: white;
            padding: 10px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        .sidebar {
            width: 200px;
            float: left;
            background-color: #f1f3f4;
            padding: 15px;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .compose {
            margin: 20px 0;
        }
        .compose form {
            display: flex;
            flex-direction: column;
        }
        .compose label {
            margin: 10px 0 5px;
        }
        .compose input, .compose textarea {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .compose button {
            padding: 10px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .compose button:hover {
            background-color: #357ae8;
        }
        .message {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px 0;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gmail Simulado</h1>
    </div>
    <div class="navbar">
        <a href="#">Bandeja de entrada</a>
        <a href="#">Enviados</a>
        <a href="#">Borradores</a>
        <a href="#">Spam</a>
        <a href="#">Papelera</a>
    </div>
    <div class="sidebar">
        <a href="#">Bandeja de entrada</a><br>
        <a href="#">Enviados</a><br>
        <a href="#">Borradores</a><br>
        <a href="#">Spam</a><br>
        <a href="#">Papelera</a><br>
    </div>
    <div class="content">
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $to = $_POST['to'];
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $errorEmail = "ccisneros@academicos.uta.cl";
            $errorMessage = "El correo no se envió a $errorEmail porque el correo no existe.";
            $errorDate = "9 de julio a las 11 pm de la noche";

            if ($to === $errorEmail) {
                echo "<div class='message'>
                        <h2>Error de Envío</h2>
                        <p class='error'>$errorMessage</p>
                        <p>Fecha: $errorDate</p>
                        <a href='gmail_simulado.php'>Volver a la bandeja de entrada</a>
                      </div>";
            } else {
                echo "<div class='message'>
                        <h2>Correo Enviado</h2>
                        <p>El correo se ha enviado correctamente a $to.</p>
                        <a href='gmail_simulado.php'>Volver a la bandeja de entrada</a>
                      </div>";
            }
        } else {
            echo '<div class="compose">
                  <h2>Redactar</h2>
                  <form action="gmail_simulado.php" method="POST">
                      <label for="to">Para:</label>
                      <input type="email" id="to" name="to" required>
                      <label for="subject">Asunto:</label>
                      <input type="text" id="subject" name="subject" required>
                      <label for="message">Mensaje:</label>
                      <textarea id="message" name="message" rows="4" cols="50" required></textarea>
                      <button type="submit">Enviar</button>
                  </form>
                  </div>';
        }
        ?>
    </div>
</body>
</html>
