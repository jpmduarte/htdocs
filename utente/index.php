<?php
session_start();

if (!isset($_SESSION['id_utente'])) {
    header('Location: login.php');
    exit();
}

$id_utente = $_SESSION['id_utente'];

// pedido api para receber dados de um utente
$backendUrl = "http://localhost:3000/api/informacao/utente/{$id_utente}";

$informacaoUtente = null;

$response = @file_get_contents($backendUrl);

if ($response === FALSE) {
    echo "<p>Erro ao recuperar os dados do utente. Tente novamente mais tarde.</p>";
    exit();
}

$informacaoUtente = json_decode($response, true);

if ($informacaoUtente === NULL) {
    echo "<p>Erro ao processar os dados recebidos. Tente novamente mais tarde.</p>";
    exit();
}

$informacaoUtente['data_nascimento'] = date('d-m-Y', strtotime($informacaoUtente['data_nascimento']));

// pedido api para receber consultas agendadas

$consultasAgendadas = null;

// pedido api para receber consultas realizadas

$consultasRealizadas = null;

// pedido api para enviar uma marcacao

// erro 
$erroAnexoFicheiroProvaPrioridade = '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Utente</title>

    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(to bottom, #e9f8f5, #f4f7f6);
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #4CAF50;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .info-box h2 {
            font-size: 1.8em;
            color: #4CAF50;
            margin-bottom: 15px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .info-box p,
        .info-box ul li {
            font-size: 1.2em;
            margin: 10px 0;
            color: #555;
        }

        .info-box p strong {
            color: #333;
        }

        ul {
            list-style-type: disc;
            margin-left: 20px;
        }

        ul li {
            padding-left: 10px;
            line-height: 1.5;
            position: relative;
        }

        ul li::before {
            content: "•";
            color: #4CAF50;
            font-size: 1.2em;
            position: absolute;
            left: -15px;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2em;
            }

            .info-box {
                padding: 15px;
            }

            .info-box h2 {
                font-size: 1.5em;
            }

            .info-box p,
            .info-box ul li {
                font-size: 1em;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(to bottom, #e9f8f5, #f4f7f6);
            color: #333;
            padding: 20px;
            line-height: 1.6;
        }

        /* Estilo do formulário de pedido */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        form label {
            font-size: 1.2em;
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 5px;
        }

        form input,
        form select,
        form textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        form input:focus,
        form select:focus,
        form textarea:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.5);
            outline: none;
        }

        /* Botão de envio */
        form button {
            background: linear-gradient(90deg, #4CAF50, #45a049);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.2em;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
        }

        form button:hover {
            background: linear-gradient(90deg, #45a049, #4CAF50);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        form button:active {
            transform: translateY(2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Estilo do campo de erro */
        .erro {
            color: #FF6347;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        button {

            background: linear-gradient(90deg, #4CAF50, #45a049);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.2em;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
        }
        /* Responsividade */
        @media (max-width: 768px) {
            form {
                padding: 20px;
            }

            form label {
                font-size: 1em;
            }

            form button {
                font-size: 1em;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body>

    <h1>Bem-vindo, <?php echo htmlspecialchars($informacaoUtente['nome']); ?>!</h1>

    <div class="info-box">
        <h2>Informações do Utente</h2>
        <p><strong>Nome:</strong> <?php echo htmlspecialchars($informacaoUtente['nome']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($informacaoUtente['email']); ?></p>
        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($informacaoUtente['telemovel']); ?></p>
        <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($informacaoUtente['data_nascimento']); ?>
        </p>
        <p><strong>Morada:</strong> <?php echo htmlspecialchars($informacaoUtente['endereco']); ?></p>
    </div>

    <div class="info-box">
        <h2>Consultas Agendadas</h2>
        <ul>
            <?php foreach ($consultasAgendadas as $consulta): ?>
                <li>Especialidade: <?php echo htmlspecialchars($consulta['especialidade']); ?>, Data:
                    <?php echo htmlspecialchars($consulta['data']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="info-box">
        <h2>Consultas Realizadas</h2>
        <ul>
            <?php foreach ($consultasRealizadas as $consulta): ?>
                <li>Especialidade: <?php echo htmlspecialchars($consulta['especialidade']); ?>, Data:
                    <?php echo htmlspecialchars($consulta['data']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="info-box">
        <h2>Pedido de marcação</h2>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <label for="especialidade">Especialidade:</label>
            <select name="especialidade" id="especialidade" required>
                <option value="">--Selecione--</option>
                <option value="Cardiologia">Cardiologia</option>
                <option value="Dermatologia">Dermatologia</option>
                <option value="Oftalmologia">Oftalmologia</option>
                <option value="Ortopedia">Ortopedia</option>
                <option value="Psiquiatria">Psiquiatria</option>
            </select>

            <label for="tipo_marcacao">Tipo de marcação:</label>
            <select name="tipo_marcacao" id="tipo_marcacao" required>
                <option value="">--Selecione--</option>
                <option value="consulta">Consulta</option>
                <option value="cirurgia">cirurgia</option>
            </select>

            <label for="data">Data Preferencial:</label>
            <input type="date" name="data" id="data" required>

            <label for="hora">Hora Preferencial:</label>
            <input type="time" name="hora" id="hora" required>


            <label for="observacoes">Observações (opcional):</label>
            <textarea name="observacoes" id="observacoes" rows="4"
                placeholder="Insira observações ou informações adicionais"></textarea>

            <label for="prioridade">É utente prioritário:</label>
            <select name="prioridade" id="prioridade" required>
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>

            <label for="anexoFicheiroProvaPrioridade">Anexar ficheiro de prova de prioridade:</label>
            <input type="file" name="anexoFicheiroProvaPrioridade" id="anexoFicheiroProvaPrioridade"
                accept=".pdf,.jpg,.jpeg,.png">
            <div class="erro"><?php echo $erroAnexoFicheiroProvaPrioridade; ?></div>

            <button type="submit" class="button">Enviar Pedido</button>
        </form>
    </div>

    <a href="../logout.php" class="button">Sair</a>
</body>

</html>