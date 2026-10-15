"""
=================================================
MÓDULO DE PREVISÃO DE DESEMPENHO ACADÊMICO
Sistema Escolar
=================================================

Este script analisa o histórico de notas cadastradas no MySQL
e gera uma estimativa do resultado final do aluno (Aprovado, Recuperação ou Risco Crítico).
"""

import mysql.connector
from mysql.connector import Error

# Definindo constantes para as regras de previsão
NOTA_CORTE_APROVACAO = 7.0
NOTA_CORTE_RECUPERACAO = 5.0


def conectar_banco():
    """Estabelece a conexão com a base de dados MySQL."""
    return mysql.connector.connect(
        host="localhost", user="root", password="", database="sistema_escolar"
    )


def calcular_previsao(nota):
    """
    Aplica a lógica preditiva com base na nota atual do aluno.
    Retorna a tendência e a ação recomendada.
    """
    if nota is None:
        return "Sem dados", "Aguardando lançamento de notas"

    nota_val = float(nota)

    if nota_val >= NOTA_CORTE_APROVACAO:
        return "Aprovação Provável", "Manter o ritmo de estudos"
    elif nota_val >= NOTA_CORTE_RECUPERACAO:
        pontos_faltantes = NOTA_CORTE_APROVACAO - nota_val
        return (
            "Risco de Recuperação",
            f"Precisa de +{pontos_faltantes:.1f} pontos para atingir a média",
        )
    else:
        pontos_faltantes = NOTA_CORTE_APROVACAO - nota_val
        return (
            "Risco Crítico",
            f"Acompanhamento pedagógico urgente (+{pontos_faltantes:.1f} pts necessários)",
        )


def analisar_previsoes():
    """Consulta os dados dos alunos e exibe as previsões acadêmicas."""
    conexao = None
    cursor = None

    try:
        conexao = conectar_banco()
        cursor = conexao.cursor(dictionary=True)

        # Consulta SQL relacionando Alunos, Matrículas e Disciplinas
        sql = """
            SELECT 
                a.nome AS aluno_nome,
                a.matricula AS aluno_matricula,
                d.nome AS disciplina_nome,
                m.nota AS nota_atual
            FROM matriculas m
            INNER JOIN alunos a ON a.id = m.aluno_id
            INNER JOIN disciplinas d ON d.id = m.disciplina_id
            WHERE a.ativo = 1
            ORDER BY a.nome ASC
        """
        cursor.execute(sql)
        registros = cursor.fetchall()

        print("\n" + "=" * 70)
        print("          PAINEL PREDITIVO DE DESEMPENHO ACADÊMICO          ")
        print("=" * 70)

        if registros:
            for reg in registros:
                aluno = reg["aluno_nome"]
                disciplina = reg["disciplina_nome"]
                nota = reg["nota_atual"]

                tendencia, recomendacao = calcular_previsao(nota)

                nota_str = f"{float(nota):.2f}" if nota is not None else "N/A"

                print(f"Aluno: {aluno:<25} | Disciplina: {disciplina:<15}")
                print(f"  Nota Atual: {nota_str:<5} | Previsão: {tendencia}")
                print(f"  Recomendação: {recomendacao}")
                print("-" * 70)
        else:
            print("Nenhum registro encontrado para realizar análises preditivas.")

        print("=" * 70 + "\n")

    except Error as erro:
        print(f"Erro ao conectar ou consultar o banco de dados: {erro}")

    finally:
        if cursor:
            cursor.close()
        if conexao and conexao.is_connected():
            conexao.close()


if __name__ == "__main__":
    analisar_previsoes()
