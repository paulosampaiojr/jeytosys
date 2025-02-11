<?php
require 'conexao.php';

$tipo_formulario_id = 4; // Defina dinamicamente conforme necessário

// Consulta para verificar se o formulário está ativo
$sql = "SELECT tipo_formulario_nome, situacao FROM tipo_formulario WHERE tipo_formulario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tipo_formulario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || $row['situacao'] !== 'ATIVO') {
    $nomeFormulario = $row['tipo_formulario_nome'] ?? "Formulário";
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulário Indisponível</title>
        <link rel="stylesheet" href="css/style.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f8f8f8;
                margin: 0;
            }
            .container {
                text-align: center;
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            }
            h1 {
                color: #d9534f;
            }
            p {
                font-size: 16px;
                color: #555;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                margin-top: 15px;
                text-decoration: none;
                color: white;
                background-color: #0275d8;
                border-radius: 5px;
                transition: 0.3s;
            }
            .btn:hover {
                background-color: #025aa5;
            }
        </style>
        <script>
            setTimeout(() => {
                window.location.href = "index.php"; // Redireciona após 10 segundos
            }, 10000);
        </script>
    </head>
    <body>
        <div class="container">
            <h1>🚧 Formulário Indisponível 🚧</h1>
            <p>O formulário <strong><?php echo htmlspecialchars($nomeFormulario); ?></strong> está temporariamente indisponível.</p>
            <p>Por favor, tente novamente mais tarde ou entre em contato com o suporte.</p>
            <a href="index.php" class="btn">Voltar para a Página Inicial</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Escola de Cura Interiro - Igreja Batista do Bosque">
<meta property="og:description" content="A Escola de Cura Interior é um ministério da Igreja Batista do Bosque que tem como objetivo ajudar as pessoas a encontrarem a cura interior e a liberdade em Cristo.">
<meta property="og:image" content="https://igrejabatistadobosque.online/sistemas/css/eci.png">
<meta property="og:url" content="https://igrejabatistadobosque.online/sistemas/escoladecura">
<meta property="og:type" content="website">

    <title>Inscrição</title>
    <link rel="stylesheet" href="js/css/style.css">
    <style>
        label[for="termo_inscricao"] {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        label[for="termo_inscricao"] input[type="checkbox"] {
            margin: 0;
            flex-shrink: 0;
            width: 5%;
        }

        label[for="termo_inscricao"] span {
            text-align: left;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        function validarTelefone(telefone) {
            return telefone.replace(/\D/g, '').length === 11;
        }

        function validarFormulario(event) {
            const telefone = document.getElementById("telefone").value;
            const email = document.getElementById("email").value;
            const nome_completo = document.getElementById("nome_completo").value.trim();
            const contato_lider = document.getElementById("contato_lider").value.trim();
            const geracao = document.getElementById("geracao").value;
            const nome_lider = document.getElementById("nome_lider").value.trim();

            if (!validarTelefone(telefone)) {
                alert("O campo 'Telefone' deve conter 11 dígitos (incluindo o DDD).");
                event.preventDefault();
                return false;
            }

            if (contato_lider && !validarTelefone(contato_lider)) {
                alert("O campo 'Contato do Líder' deve conter 11 dígitos (incluindo o DDD).");
                event.preventDefault();
                return false;
            }

            if (!validarEmail(email)) {
                alert("Por favor, insira um e-mail válido no formato 'exemplo@dominio.com'.");
                event.preventDefault();
                return false;
            }

            if (nome_completo === "") {
                alert("O campo 'Nome Completo' não pode estar vazio.");
                event.preventDefault();
                return false;
            }

            if (geracao === "") {
                alert("Por favor, selecione uma geração válida.");
                event.preventDefault();
                return false;
            }

            if (nome_lider !== "" && nome_lider.length < 3) {
                alert("O campo 'Nome do Líder' deve ter pelo menos 3 caracteres.");
                event.preventDefault();
                return false;
            }

            return true;
        }

        $(document).ready(function() {
            $('#telefone, #contato_lider').mask('(00) 00000-0000');
        });
    </script>
</head>
<body>
    <div class="form-container">
        <div class="container">
            <img src="css/eci.png" alt="Imagem centralizada" width="100%">
        </div>
        <h1>Formulário de Inscrição</h1>
        <form action="processa_inscricao.php" method="POST" onsubmit="return validarFormulario(event)">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" id="nome_completo" name="nome_completo" required placeholder="Digite seu nome completo">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="exemplo@dominio.com">

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" required placeholder="(99) 99999-9999">

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="" disabled selected>Selecione o sexo</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
            </select>

            <label for="geracao">Geração:</label>
            <select id="geracao" name="geracao" required>
                <option value="" disabled selected>Selecione uma geração</option>
                <?php
                require 'conexao.php';
                $sql = "SELECT id, nome FROM geracao";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['nome'])."</option>";
                    }
                } else {
                    echo "<option disabled>Nenhuma geração disponível</option>";
                }
                $conn->close();
                ?>
            </select>

            <label for="nome_lider">Nome do Líder:</label>
            <input type="text" id="nome_lider" name="nome_lider" placeholder="Nome do líder">

            <label for="contato_lider">Contato do Líder:</label>
            <input type="tel" id="contato_lider" name="contato_lider" placeholder="(99) 99999-9999">


            <label for="termo_inscricao">
                <input type="checkbox" id="termo_inscricao" name="termo_inscricao" value="1" required>
                <span>Concordo que participei do ENCONTRO COM DEUS / UNIVERSIDADE DA VIDA e que, após a primeira aula realizada, não será devolvido o valor da inscrição da Escola de Cura.</span>
            </label>

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>
