# PHPCBF

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026 |

## Objetivo

Este documento descreve a correção automática de problemas de forma do código PHP com a ferramenta instalada em `Ferramentas/PHPCBF`. As funções exigidas, a organização e o procedimento geral das ferramentas de desenvolvimento estão em [Ferramentas](../04Ferramentas.md).

## Funcionamento

O PHPCBF cumpre a função de correção automática definida em [Funções exigidas](../04Ferramentas.md#funções-exigidas). Ele aplica as correções automáticas disponíveis no conjunto de verificações do PHPCS.

## Instalação e configuração

O executável fica em `Ferramentas/PHPCBF/PHPCBF.phar`. Os dados da instalação estão em [Registro de instalação](#registro-de-instalação), com os campos definidos em [Documento de ferramenta](../01Padroes.md#documento-de-ferramenta).

A execução DEVE utilizar o mesmo arquivo de padrão do PHPCS e um caminho explícito de código próprio. O destino prescrito é `Ferramentas/PHPCS/PHPCS.xml`, ainda ausente. É PROIBIDO duplicar esse padrão na pasta do PHPCBF, conforme [Organização](../04Ferramentas.md#organização).

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | 4.0.4; consulta por `--version` |
| Origem | [PHP_CodeSniffer 4.0.4](https://github.com/PHPCSStandards/PHP_CodeSniffer/releases/tag/4.0.4), arquivo phpcbf.phar |
| Licença | [BSD-3-Clause](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.0.4/licence.txt), preservada no PHAR distribuído |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Requisitos | PHP >=7.2.0 e extensões libxml, SimpleXML, tokenizer e XMLWriter, conforme o [composer.json da versão](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.0.4/composer.json); mesmo padrão de forma utilizado pelo PHPCS |
| Executável | `Ferramentas/PHPCBF/PHPCBF.phar` |
| Configuração | Pendente: criar o padrão compartilhado `Ferramentas/PHPCS/PHPCS.xml`, conforme o documento do PHPCS |
| Integridade | SHA-256: `0b3b8c5571543e94452f6edec0d7e924b287a64c13b73d8dcf3d25f0ee004948`; fonte: https://api.github.com/repos/PHPCSStandards/PHP_CodeSniffer/releases/assets/503395391 |

## Execução

A fórmula de invocação abaixo exige o padrão compartilhado, atualmente ausente. A execução DEVE partir da raiz do projeto. `[CAMINHO_CODIGO]` é um parâmetro substituível pelo arquivo ou diretório próprio a corrigir, com aspas se o caminho contém espaços. O bloco expressa a sintaxe da ferramenta; não representa uma execução realizada:

```text
php Ferramentas/PHPCBF/PHPCBF.phar --standard=Ferramentas/PHPCS/PHPCS.xml [CAMINHO_CODIGO]
```

É PROIBIDO executar a correção sem configuração e escopo próprios identificados.

## Resultado

A execução altera os arquivos para os quais encontra correções automáticas. É OBRIGATÓRIO revisar a diferença produzida e executar novamente o PHPCS sobre o escopo corrigido. Problemas sem correção automática continuam exigindo edição.

O término do corretor não constitui aprovação de forma. Essa aprovação depende do PHPCS com o padrão compartilhado e os critérios de [Configuração das verificações](../04Ferramentas.md#configuração-das-verificações). O padrão permanece ausente; não há correção do projeto comprovada pela presença deste executável.

Atualizações e revisão de correções seguem [Atualização de ferramentas e dependências](../00Regras.md#atualização-de-ferramentas-e-dependências).
