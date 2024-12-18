<?php
// Variáveis para mensagens de erro
$erroEmail = $erroPassword = $erroNumeroUtente = "";
$erroDataNascimento = $erroTelemovel = $erroNumeroCC = "";
$erroMorada = $erroNome = $erroAPI = "";
$sucesso = false; // Variável de controle de sucesso

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $numero_utente = $_POST['numero_utente'];
    $data_nascimento = $_POST['data_nascimento'];
    $telemovel = $_POST['telemovel'];
    $numero_cc = $_POST['numero_cc'];
    $morada = $_POST['morada'];
    $nome = $_POST['nome'];

    // Validações
    if (empty($email))
        $erroEmail = "O campo de email é obrigatório.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $erroEmail = "Insira um email válido.";

    if (empty($password))
        $erroPassword = "O campo de palavra-passe é obrigatório.";
    if (empty($numero_utente))
        $erroNumeroUtente = "O campo de número de utente é obrigatório.";
    if (empty($data_nascimento))
        $erroDataNascimento = "O campo de data de nascimento é obrigatório.";
    if (empty($telemovel))
        $erroTelemovel = "O campo de número de telemóvel é obrigatório.";
    elseif (!is_numeric($telemovel))
        $erroTelemovel = "O número de telemóvel deve conter apenas números.";
    if (empty($numero_cc))
        $erroNumeroCC = "O campo de número de cartão de cidadão é obrigatório.";
    if (empty($morada))
        $erroMorada = "O campo de morada é obrigatório.";
    if (empty($nome))
        $erroNome = "O campo de nome é obrigatório.";

    if (strlen($numero_utente) != 9)
        $erroNumeroUtente = "O número de utente deve conter 9 dígitos.";
    if (strlen($telemovel) != 9)
        $erroTelemovel = "O número de telemóvel deve conter 9 dígitos.";
    if (strlen($numero_cc) != 8)
        $erroNumeroCC = "O número de cartão de cidadão deve conter 8 dígitos.";
    if (strlen($nome) < 3)
        $erroNome = "O nome deve conter pelo menos 3 caracteres.";
    if (strlen($password) < 6)
        $erroPassword = "A palavra-passe deve conter pelo menos 6 caracteres.";
    if (strlen($morada) < 5)
        $erroMorada = "A morada deve conter pelo menos 5 caracteres.";


    if (
        !$erroEmail && !$erroPassword && !$erroNumeroUtente && !$erroDataNascimento &&
        !$erroTelemovel && !$erroNumeroCC && !$erroMorada && !$erroNome
    ) {
        $dados = compact(
            'email',
            'password',
            'numero_utente',
            'data_nascimento',
            'telemovel',
            'numero_cc',
            'morada',
            'nome'
        );

        $ch = curl_init('http://localhost:3000/api/registar/utilizador');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($dados),
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);

        $resposta = curl_exec($ch);

        if (curl_errno($ch)) {
            $erroSubmissao = "Falha ao conectar à API: " . curl_error($ch);
        } else {
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $resposta = json_decode($resposta);

            if (is_object($resposta) && isset($resposta->id_utente) && is_numeric($resposta->id_utente)) {
                $sucesso = true;
                header("Location: ../login/index.php");
                exit();
            } else {
                $erroSubmissao = $resposta->error;
            }
        }
        curl_close($ch);
    }
}
?>


<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo</title>
    <style>
        /* Estilos gerais */
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
        input[type="password"],
        input[type="email"],
        input[type="tel"],
        input[type="date"] {
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
    </style>
</head>

<body>
    <div class="container">
        <h2>Registo</h2>
        <form method="POST" action="index.php">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
            <div class="erro"><?php echo $erroNome; ?></div>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>">
            <div class="erro"><?php echo $erroEmail; ?></div>

            <label for="password">Palavra-passe:</label>
            <input type="password" id="password" name="password">
            <div class="erro"><?php echo $erroPassword; ?></div>

            <label for="numero_utente">Número de Utente:</label>
            <input type="text" id="numero_utente" name="numero_utente"
                value="<?php echo htmlspecialchars($_POST['numero_utente']); ?>">
            <div class="erro"><?php echo $erroNumeroUtente; ?></div>

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento"
                value="<?php echo htmlspecialchars($_POST['data_nascimento']); ?>">
            <div class="erro"><?php echo $erroDataNascimento; ?></div>

            <label for="numero_cc">Número de Cartão de Cidadão:</label>
            <input type="text" id="numero_cc" name="numero_cc"
                value="<?php echo htmlspecialchars($_POST['numero_cc']); ?>">
            <div class="erro"><?php echo $erroNumeroCC; ?></div>

            <label for="morada">Morada:</label>
            <input type="text" id="morada" name="morada"
                value="<?php echo htmlspecialchars($_POST['morada']); ?>">
            <div class="erro"><?php echo $erroMorada; ?></div>

            <label for="telemovel">Número de Telemóvel:</label>
            <input type="tel" id="telemovel" name="telemovel"
                value="<?php echo htmlspecialchars($_POST['telemovel']); ?>">
            <div class="erro"><?php echo $erroTelemovel; ?></div>

            <input type="submit" value="Registar">
            <div class="erro"><?php echo $erroSubmissao; ?></div>
            <div class="login" vakue="login">
                <p>Já tem uma conta? <a href="../login/">Faça login aqui</a></p>
        </form>
    </div>
</body>

</html>