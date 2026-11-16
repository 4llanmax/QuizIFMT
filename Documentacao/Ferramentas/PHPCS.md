# PHPCS

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 28/09/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Este documento descreve a verificação da forma de escrita do código PHP com a ferramenta instalada em `Ferramentas/PHPCS`. As funções exigidas, a organização e o procedimento geral das ferramentas de desenvolvimento estão em [Ferramentas](../04Ferramentas.md).

## Funcionamento

O PHPCS cumpre a função de verificação da forma do código definida em [Funções exigidas](../04Ferramentas.md#funções-exigidas). Ele compara o código próprio com o conjunto de verificações configurado. Arquivos distribuídos por terceiros ficam fora do escopo de formatação.

## Instalação e configuração

O executável fica em `Ferramentas/PHPCS/PHPCS.phar`. Os dados da instalação estão em [Registro de instalação](#registro-de-instalação), com os campos definidos em [Documento de ferramenta](../01Padroes.md#documento-de-ferramenta).

A configuração `Ferramentas/PHPCS/PHPCS.xml` usa PSR-12 no PHP de produção e nos testes e apoios indicados. O limite de comprimento de linha não se aplica à marcação das visões e às instruções SQL. A correção automática usa a mesma configuração. As convenções próprias de idioma e nomes também recebem conferência de referências e estrutura.

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | 4.0.4; consulta por `--version` |
| Origem | [PHP_CodeSniffer 4.0.4](https://github.com/PHPCSStandards/PHP_CodeSniffer/releases/tag/4.0.4), arquivo phpcs.phar |
| Licença | [BSD-3-Clause](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.0.4/licence.txt), preservada no PHAR distribuído |
| Justificativa | Conferir a forma do PHP próprio com padrão explícito |
| Requisitos | PHP >=7.2.0 e extensões libxml, SimpleXML, tokenizer e XMLWriter, conforme o [composer.json da versão](https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.0.4/composer.json); verificações adicionais exigem os requisitos próprios declarados por cada uma |
| Executável | `Ferramentas/PHPCS/PHPCS.phar` |
| Configuração | `Ferramentas/PHPCS/PHPCS.xml`, com PSR-12, escopo próprio e exceção do comprimento de linha |
| Integridade | SHA-256: `4b010cd21d8bc8a17e2504792e3c77ef8259126f24caf7eafe7eddef1fa871e9`; fonte: https://api.github.com/repos/PHPCSStandards/PHP_CodeSniffer/releases/assets/503395408 |

## Execução

Executar da raiz:

```text
php Ferramentas/PHPCS/PHPCS.phar --standard=Ferramentas/PHPCS/PHPCS.xml
```

O arquivo de padrão DEVE definir também os caminhos próprios verificados e os recursos de terceiros excluídos da formatação.

## Resultado

O resultado informa arquivo, posição e verificação não atendida. A aprovação de forma não comprova o comportamento da aplicação.

A aprovação exige código zero, sem erros ou avisos, no padrão e no escopo explícitos. A aprovação de forma não substitui a conferência dos nomes próprios nem os testes de comportamento.

Atualizações e revisão de correções seguem [Atualização de ferramentas e dependências](../00Regras.md#atualização-de-ferramentas-e-dependências).

O apoio de automação `.github/Verificacoes/Validacao.php` mantém sua classe global e sua entrada CLI no mesmo arquivo, conforme o contrato existente fora de Aplicacao e Testes. Somente esse arquivo recebe as exceções explícitas de namespace e de efeitos na declaração do padrão; as demais verificações permanecem aplicadas.
