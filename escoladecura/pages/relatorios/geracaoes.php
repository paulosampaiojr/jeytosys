<?php
require '../../conexao.php';

// Recebe o ID da geração
$geracao_id = $_GET['geracao_id'];

// Busca informações da geração
$sql_geracao = "SELECT nome FROM geracao WHERE id = ?";
$stmt = $conn->prepare($sql_geracao);
$stmt->bind_param("i", $geracao_id);
$stmt->execute();
$result_geracao = $stmt->get_result();
$geracao = $result_geracao->fetch_assoc();

// Busca dados de inscrições por gênero e dia da escola
$sql_inscricoes = "SELECT sexo, dia_escola, COUNT(*) as total 
                   FROM inscricoes WHERE geracao_id = ? 
                   GROUP BY sexo, dia_escola";
$stmt_inscricoes = $conn->prepare($sql_inscricoes);
$stmt_inscricoes->bind_param("i", $geracao_id);
$stmt_inscricoes->execute();
$result_inscricoes = $stmt_inscricoes->get_result();

// Agrupa dados para os gráficos
$dataGenero = ['Masculino' => 0, 'Feminino' => 0];
$dataTurma = ['Domingo' => 0, 'Quinta' => 0];

while ($row = $result_inscricoes->fetch_assoc()) {
    if ($row['sexo'] === 'Masculino' || $row['sexo'] === 'Feminino') {
        $dataGenero[$row['sexo']] += $row['total'];
    }
    if ($row['dia_escola'] === 'Domingo' || $row['dia_escola'] === 'Quinta') {
        $dataTurma[$row['dia_escola']] += $row['total'];
    }
}

// Busca lista de alunos
$sql_alunos = "SELECT nome_completo, sexo, dia_escola FROM inscricoes WHERE geracao_id = ?";
$stmt_alunos = $conn->prepare($sql_alunos);
$stmt_alunos->bind_param("i", $geracao_id);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório - <?php echo htmlspecialchars($geracao['nome']); ?></title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Relatório - <?php echo htmlspecialchars($geracao['nome']); ?></h1>

        <!-- Gráficos -->
        <div class="charts">
            <canvas id="chartGenero"></canvas>
            <canvas id="chartTurma"></canvas>
        </div>

        <!-- Lista de Alunos -->
        <h2>Lista de Alunos</h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Gênero</th>
                    <th>Dia da Escola</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_alunos->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nome_completo']); ?></td>
                        <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                        <td><?php echo htmlspecialchars($row['dia_escola']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Dados dos gráficos
        const dataGenero = {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                label: 'Distribuição por Gênero',
                data: [<?php echo $dataGenero['Masculino']; ?>, <?php echo $dataGenero['Feminino']; ?>],
                backgroundColor: ['#4CAF50', '#FF5722']
            }]
        };

        const dataTurma = {
            labels: ['Domingo', 'Quinta'],
            datasets: [{
                label: 'Distribuição por Turma',
                data: [<?php echo $dataTurma['Domingo']; ?>, <?php echo $dataTurma['Quinta']; ?>],
                backgroundColor: ['#3F51B5', '#FFC107']
            }]
        };

        // Configuração dos gráficos
        const configGenero = {
            type: 'pie',
            data: dataGenero,
        };

        const configTurma = {
            type: 'bar',
            data: dataTurma,
        };

        // Renderiza os gráficos
        new Chart(document.getElementById('chartGenero'), configGenero);
        new Chart(document.getElementById('chartTurma'), configTurma);
    </script>
</body>
</html>
