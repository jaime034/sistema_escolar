"""
=================================================
MÓDULO DE ANÁLISE DE DESEMPENHO ACADÊMICO
Sistema Escolar
=================================================


Este script analisa as notas dos alunos cadastradas no banco MySQL,
calculando a média geral, taxa de aprovação e detalhamento por disciplina.
"""

import mysql.connector
from mysql.connector import Error


def conectar_banco():
    """Cria e retorna a conexão com o banco de dados MySQL."""
    return mysql.connector.connect(
        host="localhost", user="root", password="", database="sistema_escolar"
    )


def analisar_desempenho_geral():
    """Gera o relatório estatístico geral de notas dos alunos."""
    conexao = None
    cursor = None

    try:
        conexao = conectar_banco()
        cursor = conexao.cursor()

        # Consulta SQL para buscar todas as notas cadastradas
        sql_notas = """
            SELECT nota 
            FROM matriculas 
            WHERE nota IS NOT NULL
        """
        cursor.execute(sql_notas)

        # Converte os resultados para float
        notas = [float(linha[0]) for linha in cursor.fetchall()]

        print("\n" + "=" * 40)
        print("      ANÁLISE DE DESEMPENHO GERAL      ")
        print("=" * 40)

        if notas:
            total_alunos_avaliados = len(notas)
            media_geral = sum(notas) / total_alunos_avaliados
            aprovados = len([n for n in notas if n >= 7.0])
            recuperacao = len([n for n in notas if n < 7.0])

            # Exibição dos dados formatados
            print(f"Total de Avaliações : {total_alunos_avaliados}")
            print(f"Média Geral da Escola: {media_geral:.2f}")
            print(
                f"Aprovados (>= 7.0)   : {aprovados} ({aprovados / total_alunos_avaliados * 100:.1f}%)"
            )
            print(
                f"Recuperação (< 7.0)  : {recuperacao} ({recuperacao / total_alunos_avaliados * 100:.1f}%)"
            )
        else:
            print("Nenhuma nota cadastrada no sistema até o momento.")

        print("=" * 40 + "\n")

    except Error as erro:
        print(f"Erro ao conectar ou consultar o banco de dados: {erro}")

    finally:
        # Garante o fechamento do cursor e da conexão
        if cursor:
            cursor.close()
        if conexao and conexao.is_connected():
            conexao.close()


if __name__ == "__main__":
    analisar_desempenho_geral()
