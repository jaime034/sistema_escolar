"""Regera a documentação dos arquivos do projeto, no formato mostrado no documento.
O nome deste utilitário aparece na estrutura original, mas seu código não foi fornecido.
"""

from pathlib import Path
from datetime import datetime

RAIZ = Path(__file__).resolve().parent
DESTINO = RAIZ / "DOCUMENTACAO_SISTEMA_ESCOLAR.md"
LINGUAGENS = {".php": "php", ".py": "python", ".css": "css", ".js": "javascript", ".sql": "sql"}


def gerar_documentacao():
    arquivos = [
        p
        for p in sorted(RAIZ.rglob("*"))
        if p.is_file()
        and p.suffix in LINGUAGENS
        and p.name != Path(__file__).name
        and "__pycache__" not in p.parts
    ]
    partes = [
        "# DOCUMENTAÇÃO DO SISTEMA ESCOLAR",
        "",
        "Data da geração: " + datetime.now().strftime("%d/%m/%Y %H:%M:%S"),
        "",
        "## Estrutura do Projeto",
        "",
        "```text",
        RAIZ.name + "/",
    ]
    partes.extend(p.relative_to(RAIZ).as_posix() for p in arquivos)
    partes += ["```", ""]
    for arquivo in arquivos:
        partes += [
            "---",
            "",
            "# Arquivo: " + arquivo.relative_to(RAIZ).as_posix(),
            "",
            "**Tamanho:** " + str(arquivo.stat().st_size) + " bytes",
            "",
            "```" + LINGUAGENS[arquivo.suffix],
            arquivo.read_text(encoding="utf-8"),
            "```",
            "",
        ]
    DESTINO.write_text("\n".join(partes), encoding="utf-8")
    print("Documentação gerada em:", DESTINO)


if __name__ == "__main__":
    gerar_documentacao()
