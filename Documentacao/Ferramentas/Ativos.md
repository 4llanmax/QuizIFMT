# Ativos

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Descrever o processamento dos ativos próprios antes da entrega e sua conferência reproduzível. A entrega do servidor está em [Apache](../Ambiente/Apache.md).

## Funcionamento

O apoio PHP remove os comentários de bloco dos originais próprios e processa somente `Principal.css` e `Principal.js`. No CSS, reduz espaços e separadores; no JavaScript, remove espaços nas extremidades das linhas e as une, preservando as instruções escritas com terminadores explícitos. Seu alcance é esse par de arquivos, não uma minificação genérica de distribuições externas.

As versões processadas não recebem cabeçalho documental; os originais conservam seus comentários. O HTML é processado pelo renderizador da aplicação.

## Instalação e configuração

A ferramenta é implementação própria em `Ferramentas/Ativos/Ativos.php`. Os caminhos dos dois originais e de suas versões processadas são fixos no apoio. Não recebe parâmetros privados nem instala dependências.

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | Implementação própria acompanhada pelo Git |
| Origem | Código próprio em `Ferramentas/Ativos/Ativos.php` |
| Licença | [Licença do projeto](../../LICENSE) |
| Justificativa | Entregar somente ativos processados, sem dependência de construção na hospedagem |
| Requisitos | PHP CLI do desenvolvimento |
| Executável | `Ferramentas/Ativos/Ativos.php` |
| Configuração | Caminhos explícitos no executável |
| Integridade | Conferência pelo histórico, diff e teste de correspondência; não há distribuição de terceiro |

## Execução

Da raiz do projeto:

```text
php Ferramentas/Ativos/Ativos.php
php Ferramentas/Ativos/Ativos.php --conferir
```

O primeiro comando atualiza somente as versões `.min`. O segundo compara os arquivos sem gravá-los. Depois de alterar um original, processar os ativos e executar os testes de ambas as versões.

## Resultado

Código 0 indica sucesso. `--conferir` termina com 1 se encontrar ativo desatualizado. Opção desconhecida produz código 2. A conferência de correspondência não substitui os testes de comportamento ou a medição de cobertura.
