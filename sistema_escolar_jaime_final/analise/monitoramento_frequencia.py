"""
=================================================
MÓDULO DE MONITORAMENTO DE FREQUÊNCIA
Sistema Escolar
=================================================


Este script analisa o percentual de frequência dos alunos,
identificando casos em risco de reprovação por faltas.
"""

import mysql.connector
from mysql.connector import Error

# Limite mínimo de presença exigido pela instituição (75%)
LIMITE_PRESENCA_MINIMA = 75.0


def conectar_banco():
    """Estabelece a conexão com a base de dados MySQL."""
    return mysql.connector.connect(
        host="localhost", user="root", password="", database="sistema_escolar"
    )


def analisar_frequencia_alunos():
    """Consulta as matrículas e calcula a taxa de frequência de cada estudante."""
    conexao = None
    cursor = None

    try:
        conexao = conectar_banco()
        cursor = conexao.cursor(dictionary=True)

        # Consulta que traz o nome do aluno, disciplina e os dados de aulas/presenças
        # Nota: Caso a tabela 'matriculas' possua as colunas total_aulas e presencas
        sql = """
            SELECT 
                a.nome AS aluno_nome,
                a.matricula AS aluno_matricula,
                d.nome AS disciplina_nome,
                COALESCE(m.total_aulas, 40) AS total_aulas,
                COALESCE(m.presencas, 40) AS presencas
            FROM matriculas m
            INNER JOIN alunos a ON a.id = m.aluno_id
            INNER JOIN disciplinas d ON d.id = m.disciplina_id
            WHERE a.ativo = 1
            ORDER BY a.nome ASC
        """
        cursor.execute(sql)
        registros = cursor.fetchall()

        print("\n" + "=" * 60)
        print("        PAINEL DE MONITORAMENTO DE FREQUÊNCIA          ")
        print("=" * 60)

        if registros:
            alunos_em_risco = 0

            for reg in registros:
                total_aulas = reg["total_aulas"]
                presencas = reg["presencas"]

                # Cálculo do percentual de presença
                pct_frequencia = (presencas / total_aulas * 100) if total_aulas > 0 else 0.0

                # Define o status com base no percentual
                if pct_frequencia >= LIMITE_PRESENCA_MINIMA:
                    status = "[ OK ] Normal"
                elif pct_frequencia >= 70.0:
                    status = "[ ATENÇÃO ] Próximo ao limite"
                    alunos_em_risco += 1
                else:
                    status = "[ CRÍTICO ] Risco de Reprovação"
                    alunos_em_risco += 1

                print(f"Aluno: {reg['aluno_nome']:<25} | Disciplina: {reg['disciplina_nome']:<15}")
                print(
                    f"  Frequência: {pct_frequencia:.1f}% ({presencas}/{total_aulas} aulas) -> Status: {status}"
                )
                print("-" * 60)

            print(f"\nResumo: Total de alertas de frequência emitidos: {alunos_em_risco}")
        else:
            print("Nenhum registro de matrícula encontrado para análise.")

        print("=" * 60 + "\n")

    except Error as erro:
        print(f"Erro ao conectar ou consultar o banco de dados: {erro}")

    finally:
        if cursor:
            cursor.close()
        if conexao and conexao.is_connected():
            conexao.close()


if __name__ == "__main__":
    analisar_frequencia_alunos()
