"""
=================================================
MÓDULO DE INDICADORES ACADÊMICOS (KPIs)
Sistema Escolar
=================================================


Este script calcula e exibe os principais indicadores chave de desempenho (KPIs)
da instituição escolar, consultando a base MySQL.
"""

import mysql.connector
from mysql.connector import Error

# Configuração da Nota Mínima de Aprovação (Ajustável)
NOTA_CORTE_APROVACAO = 7.0


def conectar_banco():
    """Estabelece a conexão com a base de dados MySQL."""
    return mysql.connector.connect(
        host="localhost", user="root", password="", database="sistema_escolar"
    )


def calcular_indicadores():
    """Busca dados brutos no MySQL e calcula métricas e KPIs acadêmicos."""
    conexao = None
    cursor = None

    try:
        conexao = conectar_banco()
        cursor = conexao.cursor(dictionary=True)

        # 1. Total de alunos e taxa de ativos
        cursor.execute("SELECT COUNT(*) AS total, SUM(ativo = 1) AS ativos FROM alunos")
        dados_alunos = cursor.fetchone()
        total_alunos = dados_alunos["total"] or 0
        alunos_ativos = dados_alunos["ativos"] or 0
        taxa_atividade = (alunos_ativos / total_alunos * 100) if total_alunos > 0 else 0.0

        # 2. Estatísticas de Notas e Matrículas
        cursor.execute(
            """
            SELECT 
                COUNT(*) AS total_matriculas,
                COUNT(nota) AS notas_lancadas,
                AVG(nota) AS media_geral,
                MAX(nota) AS maior_nota,
                MIN(nota) AS menor_nota,
                SUM(CASE WHEN nota >= %s THEN 1 ELSE 0 END) AS total_aprovados
            FROM matriculas
        """,
            (NOTA_CORTE_APROVACAO,),
        )
        dados_notas = cursor.fetchone()

        total_matriculas = dados_notas["total_matriculas"] or 0
        notas_lancadas = dados_notas["notas_lancadas"] or 0
        media_geral = (
            float(dados_notas["media_geral"]) if dados_notas["media_geral"] is not None else 0.0
        )
        maior_nota = (
            float(dados_notas["maior_nota"]) if dados_notas["maior_nota"] is not None else 0.0
        )
        menor_nota = (
            float(dados_notas["menor_nota"]) if dados_notas["menor_nota"] is not None else 0.0
        )
        total_aprovados = dados_notas["total_aprovados"] or 0

        # Cálculos de Percentuais
        taxa_aprovacao = (total_aprovados / notas_lancadas * 100) if notas_lancadas > 0 else 0.0
        media_disciplinas_por_aluno = (
            (total_matriculas / alunos_ativos) if alunos_ativos > 0 else 0.0
        )

        # 3. Exibição dos Resultados Formatados
        print("\n" + "=" * 50)
        print("          PAINEL DE INDICADORES ACADÊMICOS       ")
        print("=" * 50)

        print("\n[ MÉTRICAS DE ALUNOS ]")
        print(f" Total de Alunos Cadastrados : {total_alunos}")
        print(f" Alunos Ativos               : {alunos_ativos}")
        print(f" Taxa de Alunos Ativos       : {taxa_atividade:.1f}%")

        print("\n[ ENGAJAMENTO ACADÊMICO ]")
        print(f" Total de Matrículas Ativas  : {total_matriculas}")
        print(f" Média Disciplinas/Aluno     : {media_disciplinas_por_aluno:.1f}")

        print("\n[ DESEMPENHO E NOTAS ]")
        print(f" Total de Notas Lançadas     : {notas_lancadas}")
        print(f" Média Geral da Escola       : {media_geral:.2f}")
        print(f" Maior Nota Registrada       : {maior_nota:.2f}")
        print(f" Menor Nota Registrada       : {menor_nota:.2f}")
        print(f" Taxa de Aprovação (>= {NOTA_CORTE_APROVACAO}) : {taxa_aprovacao:.1f}%")

        print("\n" + "=" * 50 + "\n")

    except Error as erro:
        print(f"Erro ao acessar a base de dados: {erro}")

    finally:
        if cursor:
            cursor.close()
        if conexao and conexao.is_connected():
            conexao.close()


if __name__ == "__main__":
    calcular_indicadores()
