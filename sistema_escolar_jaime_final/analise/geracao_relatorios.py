"""
=================================================
MÓDULO DE GERAÇÃO DE RELATÓRIOS ACADÊMICOS
Sistema Escolar
=================================================

Este script consulta os dados de alunos, disciplinas e notas no MySQL
e gera um relatório completo exportado para um arquivo de texto (.txt).
"""

import mysql.connector
from mysql.connector import Error
from datetime import datetime


def conectar_banco():
    """Conecta à base de dados MySQL do sistema escolar."""
    return mysql.connector.connect(
        host="localhost", user="root", password="", database="sistema_escolar"
    )


def gerar_relatorio_txt():
    """Busca as informações no banco e exporta para um arquivo de texto."""
    conexao = None
    cursor = None

    # Nome do arquivo de saída
    caminho_arquivo = "relatorio_desempenho_escolar.txt"

    try:
        conexao = conectar_banco()
        cursor = conexao.cursor(dictionary=True)  # Retorna resultados como dicionários

        # Consulta SQL com JOIN para cruzar Alunos, Matrículas e Disciplinas
        sql = """
            SELECT 
                a.nome AS aluno_nome,
                a.matricula AS aluno_matricula,
                d.nome AS disciplina_nome,
                m.nota AS aluno_nota
            FROM matriculas m
            INNER JOIN alunos a ON a.id = m.aluno_id
            INNER JOIN disciplinas d ON d.id = m.disciplina_id
            WHERE a.ativo = 1
            ORDER BY a.nome ASC, d.nome ASC
        """
        cursor.execute(sql)
        registros = cursor.fetchall()

        # Abre o arquivo TXT para escrita (modo 'w' com codificação UTF-8)
        with open(caminho_arquivo, "w", encoding="utf-8") as arq:
            # Cabeçalho do Relatório
            data_atual = datetime.now().strftime("%d/%m/%Y %H:%M:%S")
            arq.write("=====================================================\n")
            arq.write("          RELATÓRIO ACADÊMICO DE DESEMPENHO          \n")
            arq.write("=====================================================\n")
            arq.write(f"Data de Geração: {data_atual}\n\n")

            if registros:
                aluno_atual = ""
                for reg in registros:
                    # Imprime o nome do aluno apenas quando mudar
                    if reg["aluno_nome"] != aluno_atual:
                        aluno_atual = reg["aluno_nome"]
                        arq.write(f"\nAluno: {aluno_atual} (Matrícula: {reg['aluno_matricula']})\n")
                        arq.write("-" * 50 + "\n")

                    nota = reg["aluno_nota"]
                    if nota is not None:
                        nota_float = float(nota)
                        situacao = "Aprovado" if nota_float >= 7.0 else "Em Recuperação"
                        arq.write(
                            f"  - Disciplina: {reg['disciplina_nome']:<20} | Nota: {nota_float:.2f} | Status: {situacao}\n"
                        )
                    else:
                        arq.write(
                            f"  - Disciplina: {reg['disciplina_nome']:<20} | Nota: Pendente\n"
                        )
            else:
                arq.write("Nenhum registro de matrícula ou nota foi encontrado.\n")

            arq.write("\n=====================================================\n")
            arq.write("                 FIM DO RELATÓRIO                    \n")
            arq.write("=====================================================\n")

        print(f"Relatório gerado com sucesso no arquivo: {caminho_arquivo}")

    except Error as erro:
        print(f"Erro ao acessar o banco de dados: {erro}")
    except IOError as erro_file:
        print(f"Erro ao gravar o arquivo de texto: {erro_file}")

    finally:
        if cursor:
            cursor.close()
        if conexao and conexao.is_connected():
            conexao.close()


if __name__ == "__main__":
    gerar_relatorio_txt()
