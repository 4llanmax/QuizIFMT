# PHPStan

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 28/09/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Este documento descreve a análise estática do código PHP com a ferramenta instalada em `Ferramentas/PHPStan`. As funções exigidas, a organização e o procedimento geral das ferramentas de desenvolvimento estão em [Ferramentas](../04Ferramentas.md).

## Funcionamento

O PHPStan cumpre a função de análise estática definida em [Funções exigidas](../04Ferramentas.md#funções-exigidas). Ele verifica tipos, chamadas e contratos sem executar os casos de uso da aplicação.

## Instalação e configuração

O executável fica em `Ferramentas/PHPStan/PHPStan.phar`. Os dados da instalação estão em [Registro de instalação](#registro-de-instalação), com os campos definidos em [Documento de ferramenta](../01Padroes.md#documento-de-ferramenta).

A configuração `Ferramentas/PHPStan/PHPStan.neon` usa nível 5 e compatibilidade PHP 8.5.12. O escopo inclui aplicação, entrada pública e os apoios técnicos indicados no arquivo. Distribuições externas não recebem análise como código próprio. As visões recebem dados explícitos para permitir análise sem supressão de variáveis indefinidas.

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | 2.2.6; consulta por `--version` |
| Origem | [Lançamento 2.2.6](https://github.com/phpstan/phpstan/releases/tag/2.2.6), arquivo phpstan.phar distribuído pelo projeto PHPStan |
| Licença | [MIT](https://github.com/phpstan/phpstan/blob/2.2.6/LICENSE), conforme a versão publicada |
| Justificativa | Conferir tipos, chamadas e contratos do PHP próprio sem executar a aplicação |
| Requisitos | PHP 7.4 ou 8.x, conforme o [composer.json da versão](https://github.com/phpstan/phpstan/blob/2.2.6/composer.json); configuração e recursos exigidos pelas extensões utilizadas |
| Executável | `Ferramentas/PHPStan/PHPStan.phar` |
| Configuração | `Ferramentas/PHPStan/PHPStan.neon`; nível 5, caminhos próprios e cache em `Dados/Arquivos/Cache/PHPStan` |
| Integridade | SHA-256: `abe4d3b38db5c4139e0135d98ea583e65185344050812651d1e9ede56d354e20`; fonte: https://api.github.com/repos/phpstan/phpstan/releases/assets/490705810 |

## Execução

Executar da raiz com a configuração explícita:

```text
php Ferramentas/PHPStan/PHPStan.phar analyse --configuration Ferramentas/PHPStan/PHPStan.neon --memory-limit=512M
```

O nível de análise DEVE ser definido nesse arquivo de configuração. Símbolos carregados de terceiros não alteram o destino nem a responsabilidade do código próprio.

## Resultado

O resultado identifica problemas de tipos, chamadas ou configuração. Ele não substitui testes de comportamento ou de implantação.

A aprovação exige término zero sem erros sobre todo o escopo configurado. O nível e o escopo não são substituídos por padrão implícito. A execução não comprova comportamento, cobertura ou ambiente de produção.

Atualizações e revisão de correções seguem [Atualização de ferramentas e dependências](../00Regras.md#atualização-de-ferramentas-e-dependências).
