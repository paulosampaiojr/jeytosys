<?php
session_start();
require 'conexao.php'; // Inclua a conexão com o banco de dados

// Verifique a conexão com o banco
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define o fuso horário GMT-5 (Horário de Rio Branco)
date_default_timezone_set('America/Rio_Branco'); // Essa é a hora do Acre (GMT-5)

$horario_inscricao = date('Y-m-d H:i:s'); // Formato de data e hora: 'YYYY-MM-DD HH:MM:SS'

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $nome_completo = htmlspecialchars($_POST['nome_completo']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telefone = htmlspecialchars($_POST['telefone']);
    $sexo = htmlspecialchars($_POST['sexo']);
    $geracao = (int)$_POST['geracao'];  // ID da geração (garantindo que seja inteiro)
    $nome_lider = htmlspecialchars($_POST['nome_lider']);
    $contato_lider = htmlspecialchars($_POST['contato_lider']);

    // Verifica se todos os campos obrigatórios foram preenchidos
    if (empty($nome_completo) || empty($email) || empty($telefone) || empty($sexo) || empty($geracao) || empty($nome_lider) || empty($contato_lider)) {
        echo "<script>alert('Erro: Todos os campos são obrigatórios. Verifique os dados e tente novamente.'); window.history.back();</script>";
        exit();
    }

    // Verifica se o nome completo já existe na tabela de inscrições
    $sql_check = "SELECT COUNT(*) FROM inscricoes WHERE LOWER(nome_completo) = LOWER(?)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $nome_completo);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        echo "<script>alert('Erro: Este nome já está cadastrado.'); window.history.back();</script>";
    } else {
        // Obter o ID da última inscrição para gerar o código de matrícula
        $sql_last_id = "SELECT MAX(id) AS last_id FROM inscricoes";
        $result_last_id = $conn->query($sql_last_id);
        if ($result_last_id) {
            $row = $result_last_id->fetch_assoc();
            $last_id = $row['last_id'] ? $row['last_id'] + 1 : 1;
            $codigo_matricula = "ECI-" . str_pad($last_id, 3, '0', STR_PAD_LEFT); // Ex: ECI-001

            // Inserir os dados no banco de dados
            $sql_insert = "INSERT INTO inscricoes (nome_completo, email, telefone, sexo, geracao_id, nome_lider, contato_lider, codigo_matricula, horario_inscricao, termo_inscricao, tipo_formulario_fk)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 3)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssssissss", $nome_completo, $email, $telefone, $sexo, $geracao, $nome_lider, $contato_lider, $codigo_matricula, $horario_inscricao);

            if ($stmt_insert->execute()) {
                // Armazena as informações na sessão
                $_SESSION['inscricao_concluida'] = true;
                $_SESSION['nome_completo'] = $nome_completo;
                $_SESSION['codigo_matricula'] = $codigo_matricula;
                $_SESSION['horario_inscricao'] = $horario_inscricao;
                $_SESSION['geracao_id'] = $geracao;

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<script>alert('Erro ao realizar a inscrição: " . $stmt_insert->error . "'); window.history.back();</script>";
            }
            $stmt_insert->close();
        } else {
            echo "<script>alert('Erro ao gerar o código da matrícula. Tente novamente.'); window.history.back();</script>";
        }
    }
}

// Página de Confirmação
if (isset($_SESSION['inscricao_concluida'])) {
    // Recupera os dados da sessão
    $nome_completo = $_SESSION['nome_completo'];
    $codigo_matricula = $_SESSION['codigo_matricula'];
    $horario_inscricao = $_SESSION['horario_inscricao'];
    $geracao_id = $_SESSION['geracao_id'];

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
            <img src="css/eci.png" alt="Imagem centralizada" width="50%">
        </div>
        <h1>Inscrição Concluída!</h1>
        <p>Obrigado por se inscrever, <span class="highlight"><?php echo htmlspecialchars($nome_completo); ?></span>!</p>
        <p>Sua matrícula é: <span class="highlight"><?php echo htmlspecialchars($codigo_matricula); ?></span>.</p>
        <p>Nome da Geração: <span class="highlight"><?php echo htmlspecialchars($nome_geracao); ?></span></p>
        <p>Inscrição realizada às: <span class="highlight"><?php echo htmlspecialchars($horario_inscricao); ?></span></p>
        <p>
            <input type="checkbox" id="termo_inscricao" name="termo_inscricao" value="1" required checked disabled>
            <span>Concordo que participei do ENCONTRO COM DEUS / UNIVERSIDADE DA VIDA e que, após a primeira aula realizada, não será devolvido o valor da inscrição da Escola de Cura.</span>
        </p>
        <p class="payment-note">
            O pagamento de <strong>R$ 90,00</strong> no link abaixo
        </p>
        <p class="payment-note">
            <!-- <a href="https://loja.infinitepay.io/2pistudio/qhm4614-escola-de-cura"><button>EFETUE INSCRIÇÃO AQUI</button></a> -->
             <a href="https://api. whatsapp.com/send?phone=55"<?php echo .$telefone.?>"&text=Ol%C3%A1<?php echo $nome_completo. ?>0 %20OBRIGADA%20POR%20REALIZAR%20A%20SUA%20INSCRI%C3%87%C3%83O!%20%0APARA%20EFETIVAR%20a%20sua%20inscri%C3%A7%C3%A3o,%20clique%20no%20link%20abaixo%20e%20fa%C3%A7a%20o%20pagamento%0Ahttps://loja.infinitepay.io/2pistudio/qhm4614-escola-de-cura"
        </p>
        <a href="index.php" class="back-button">Voltar ao início</a>
    </div>
</body>
</html>
<?php
    // Limpa as variáveis de sessão
    session_unset();
} else {
    // Redireciona para a página inicial
    header("Location: index.php?erro=inscricao_nao_concluida");
    exit();
}
?>