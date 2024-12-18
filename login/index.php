<?php
session_start();

// Inicializar variáveis de erro
$erroEmail = $erroPassword = "";

// Verificar se o método é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validar email
    if (empty($email)) {
        $erroEmail = "O campo de email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erroEmail = "Insira um email válido.";
    }

    // Validar senha
    if (empty($password)) {
        $erroPassword = "O campo de palavra-passe é obrigatório.";
    }

    // Se não houver erros, processar o login
    if (!$erroEmail && !$erroPassword) {
        // Inicializar cURL
        $url = "http://localhost:3000/api/login/utilizador"; // URL da API Node.js
        $ch = curl_init($url);

        // Configurar cURL
        $data = http_build_query(['email' => $email, 'password' => $password]);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);

        // Executar cURL e capturar a resposta
        $resposta = curl_exec($ch);

        // Verificar se houve erro no cURL
        if (curl_errno($ch)) {
            $erroEmail = "Erro ao conectar ao servidor: " . curl_error($ch);
        }

        curl_close($ch);

        // Verificar a resposta
        if ($resposta) {
            $resposta = json_decode($resposta, true);

            if (isset($resposta['message'])) {
                $erroEmail = $resposta['message'];
            } elseif (isset($resposta['id_perfil'])) {
                $_SESSION['id_perfil'] = $resposta['id_perfil'];

                switch ($resposta['id_perfil']) {
                    case 1: // Utente
                        $_SESSION['id_utente'] = $resposta['id_utente'];
                        header('Location: ../utente/');
                        exit;
                    case 2: // Profissional de Saúde
                        $_SESSION['id_profissional_saude'] = $resposta['id_profissional_saude'];
                        header('Location: ../medico/');
                        exit;
                    case 3: // Funcionário Secretaria
                        $_SESSION['id_secretaria'] = $resposta['id_secretaria'];
                        header('Location: ../secretaria/');
                        exit;
                    case 4: // Administrador
                        $_SESSION['id_administrador'] = $resposta['id_administrador'];
                        header('Location: ../admin/');
                        exit;
                    default:
                        $erroEmail = "Perfil desconhecido.";
                }
            } else {
                $erroEmail = "Resposta inválida do servidor.";
            }
        } else {
            $erroEmail = "Nenhuma resposta do servidor.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .erro {
            color: red;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .sucesso {
            color: green;
            font-size: 12px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="index.php">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <!-- Exibindo erro de email -->
            <?php if (!empty($erroEmail)): ?>
                <div class="erro"><?php echo $erroEmail; ?></div>
            <?php endif; ?>

            <label for="password">Palavra-passe:</label>
            <input type="password" id="password" name="password">
            <!-- Exibindo erro de senha -->
            <?php if (!empty($erroPassword)): ?>
                <div class="erro"><?php echo $erroPassword; ?></div>
            <?php endif; ?>

            <input type="submit" value="Entrar">
            <div class="registo">
                <p>Não tem uma conta? <a href="../registo/">Registe-se aqui</a></p>
            </div>
        </form>
    </div>
</body>
</html>
