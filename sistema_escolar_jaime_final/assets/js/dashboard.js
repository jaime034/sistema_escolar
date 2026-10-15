/**
 * Dashboard Interativo - Sistema Escolar
 * Consumo da API de Pendências via Fetch API
 */

document.addEventListener('DOMContentLoaded', () => {
    carregarPendencias();
});

async function carregarPendencias() {
    try {
        const resposta = await fetch('api/pendencias.php');
        const dados = await resposta.json();

        if (dados.status === 'sucesso') {
            console.log('Resumo de Pendências:', dados.resumo);
            // Aqui os alunos podem manipular o DOM para exibir alertas na tela
        }
    } catch (erro) {
        console.error('Erro ao carregar pendências:', erro);
    }
}
