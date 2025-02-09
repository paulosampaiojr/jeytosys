<?php
session_start();
require 'conexao.php'; // Inclua a conexão com o banco de dados

// Verifique a conexão com o banco
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define o fuso horário GMT-5 (Horário de Brasília)
date_default_timezone_set('America/Rio_Branco'); // Essa é a hora de Brasília (GMT-5)

$horario_inscricao = date('Y-m-d H:i:s'); // Formato de data e hora: 'YYYY-MM-DD HH:MM:SS'

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $nome_completo = $_POST['nome_completo'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $sexo = $_POST['sexo'];
    $geracao = $_POST['geracao'];  // ID da geração
    $nome_lider = $_POST['nome_lider'];
    $contato_lider = $_POST['contato_lider'];
    $dia_escola = $_POST['dia_escola'];

    // Verifica se o nome completo já existe na tabela de inscrições
    $sql_check = "SELECT COUNT(*) FROM inscricoes WHERE nome_completo = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $nome_completo);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        // Se o nome já existir, exibe uma mensagem de erro
        echo "<script>alert('Erro: Este nome já está cadastrado.'); window.history.back();</script>";
    } else {
        // Gerar o código da matrícula com base na turma e no ID
        $geracao_codigo = substr($geracao, 0, 3); // Ex: 'Atos 29' vira 'At'
        $dia_codigo = ($dia_escola == 'Domingo') ? 'D' : 'Q'; // 'Domingo' vira 'D', 'Quinta' vira 'Q'

        // Obter o ID da última inscrição para gerar o código de matrícula
        $sql_last_id = "SELECT MAX(id) AS last_id FROM inscricoes";
        $result_last_id = $conn->query($sql_last_id);
        if ($result_last_id) {
            $last_id = $result_last_id->fetch_assoc()['last_id'] + 1; // Incrementa o ID para o novo cadastro

            // Monta o código da matrícula (Ex: 'ATD01')
            $codigo_matricula = strtoupper("G" . $dia_codigo . str_pad($last_id, 2, '0', STR_PAD_LEFT));

            // Inserir os dados no banco de dados, incluindo o código da matrícula e a data e hora da inscrição
            $sql_insert = "INSERT INTO inscricoes (nome_completo, email, telefone, sexo, geracao_id, nome_lider, contato_lider, dia_escola, codigo_matricula, horario_inscricao)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssssisssss", $nome_completo, $email, $telefone, $sexo, $geracao, $nome_lider, $contato_lider, $dia_escola, $codigo_matricula, $horario_inscricao);

            if ($stmt_insert->execute()) {
                // Armazena as informações na sessão após a inscrição ser bem-sucedida
                $_SESSION['inscricao_concluida'] = true;
                $_SESSION['nome_completo'] = $nome_completo;
                $_SESSION['codigo_matricula'] = $codigo_matricula;
                $_SESSION['dia_escola'] = $dia_escola;
                $_SESSION['horario_inscricao'] = $horario_inscricao; // Armazena a hora da inscrição na sessão
                $_SESSION['geracao_id'] = $geracao;  // Armazena o ID da geração

                // Redireciona para a página de confirmação
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                // Erro no cadastro
                echo "<script>alert('Erro ao realizar a inscrição. Tente novamente.'); window.history.back();</script>";
            }
            $stmt_insert->close();
        } else {
            // Erro ao obter o último ID
            echo "<script>alert('Erro ao gerar o código da matrícula. Tente novamente.'); window.history.back();</script>";
        }
    }
}

// Página de Confirmação
if (isset($_SESSION['inscricao_concluida'])) {
    // Recupera os dados da sessão
    $nome_completo = $_SESSION['nome_completo'];
    $codigo_matricula = $_SESSION['codigo_matricula'];
    $dia_escola = $_SESSION['dia_escola'];
    $horario_inscricao = $_SESSION['horario_inscricao']; // Recupera o horário da inscrição da sessão
    $geracao_id = $_SESSION['geracao_id']; // Recupera o ID da geração

    // Consulta para recuperar o nome da geração
    $sql_geracao = "SELECT nome FROM geracao WHERE id = ?";
    $stmt_geracao = $conn->prepare($sql_geracao);
    $stmt_geracao->bind_param("i", $geracao_id);
    $stmt_geracao->execute();
    $stmt_geracao->bind_result($nome_geracao);
    $stmt_geracao->fetch();
    $stmt_geracao->close();
?>
    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição Concluída</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="confirmation-container">
        <div class="container">
            <img src="assets/guerreiros.png" alt="Imagem centralizada" width="50%">
        </div>
        <h1>Inscrição Concluída!</h1>
        <p>Obrigado por se inscrever, <span class="highlight"><?php echo htmlspecialchars($nome_completo); ?></span>!</p>
        <p>Sua matrícula é: <span class="highlight"><?php echo $codigo_matricula; ?></span>.</p>
        <p>Você escolheu estudar no dia: <span class="highlight"><?php echo htmlspecialchars($dia_escola); ?></span>.</p>
        <p>Nome da Geração: <span class="highlight"><?php echo htmlspecialchars($nome_geracao); ?></span></p> <!-- Exibe o nome da geração -->
        <p>Data e hora da inscrição: <span class="highlight"><?php echo $horario_inscricao; ?></span></p>
        <p class="payment-note">
            O pagamento de <strong>R$ 80,00</strong> deve ser realizado no dia da primeira aula. <br>Nos vemos lá!
        </p>
        <p class="payment-note">
            SALVE ESTE COMPROVANTE
        </p>
        <a href="index.php" class="back-button">Voltar ao início</a>
        <a href="editar_inscricao.php" class="edit-button">Editar Dados</a>

    </div>
</body>

<?php
    // Limpa as variáveis de sessão após exibir a página de confirmação
    unset($_SESSION['nome_completo']);
    unset($_SESSION['codigo_matricula']);
    unset($_SESSION['dia_escola']);
    unset($_SESSION['horario_inscricao']);
    unset($_SESSION['geracao_id']);
    unset($_SESSION['inscricao_concluida']);
} else {
    // Se a inscrição não foi concluída, redireciona para a página inicial
    header("Location: index.php");
    exit();
}
?>

</html>
