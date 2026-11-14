# PHPUnit

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026 |

## Objetivo

Este documento identifica a instalação e a execução do PHPUnit, conforme [Ferramentas](../04Ferramentas.md).

## Funcionamento

O PHPUnit executa testes PHP e fornece medição de cobertura. Os requisitos gerais estão em [Funções exigidas](../04Ferramentas.md#funções-exigidas); os cenários da automação estão em [Testes de integração da automação](../Testes/Integracao/TestesIntegracaoAutomacao.md).

## Instalação e configuração

`Ferramentas/PHPUnit/PHPUnit.xml` seleciona `Testes/Unitario` e `Testes/Integracao` pelo sufixo `Teste.php`, carrega `Testes/Apoio/Preparacao.php` e desabilita o histórico persistente. A preparação registra o prefixo `Testes`, conforme [Subpastas e espaços de nomes](../02Estrutura.md#subpastas-e-espaços-de-nomes).

A coleta e o bloqueio por cobertura insuficiente não estão preparados. A medição de linhas e ramos exige Xdebug em modo `coverage` e `branchCoverage="true"`, conforme o [manual de cobertura](https://docs.phpunit.de/en/13.3/code-coverage.html) e o esquema da versão instalada. O escopo inclui os arquivos próprios de produção não exercitados, conforme [Alcance e evidências da cobertura](../00Regras.md#alcance-e-evidências-da-cobertura). O Xdebug permanece desabilitado em produção.

## Identificação dos testes

As classes estendem `PHPUnit\Framework\TestCase`; os métodos usam `#[Test]`, de `PHPUnit\Framework\Attributes\Test`, preservando nomes próprios em português, conforme [Formato por elemento](../01Padroes.md#formato-por-elemento) e o [manual do PHPUnit](https://docs.phpunit.de/en/13.3/writing-tests-for-phpunit.html).

## Registro de instalação

| Campo | Conteúdo |
| --- | --- |
| Versão | 13.3.2, identificada por `--version` |
| Origem | [PHAR oficial 13.3.2](https://phar.phpunit.de/phpunit-13.3.2.phar) e [código da versão](https://github.com/sebastianbergmann/phpunit/tree/13.3.2) |
| Licença | [BSD-3-Clause](https://github.com/sebastianbergmann/phpunit/blob/13.3.2/LICENSE), conforme a versão publicada |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Requisitos | PHP >=8.4.1 e extensões DOM, filter, JSON, libxml, mbstring e XMLWriter, conforme o [composer.json da versão](https://github.com/sebastianbergmann/phpunit/blob/13.3.2/composer.json). Xdebug em modo coverage para medir linhas e ramos |
| Executável | `Ferramentas/PHPUnit/PHPUnit.phar` |
| Configuração | `Ferramentas/PHPUnit/PHPUnit.xml`, com preparação em `Testes/Apoio/Preparacao.php`, descoberta dos cenários e escopo de produção em `Aplicacao` e `Publico/index.php`. Coleta, relatórios e reprovação por cobertura insuficiente permanecem pendentes |
| Integridade | SHA-256: `34ec870a6fb7511dbe2a6af95683be4bb0452204bb31507764c37cff8f6ff924`; fonte: https://phar.phpunit.de/phpunit-13.3.2.phar.asc |

A assinatura indicada em `Integridade` autentica o PHAR pela chave de impressão digital `D8406D0D82947747293778314AA394086372C20A`, conforme [Verificação de lançamentos](https://phpunit.de/verify.html). Para repetir a autenticação, usar a assinatura da mesma versão, a chave conferida e `gpg --verify [CAMINHO_ASSINATURA] Ferramentas/PHPUnit/PHPUnit.phar`, em que `[CAMINHO_ASSINATURA]` identifica o arquivo `.asc` obtido da fonte oficial.

## Execução

Executar da raiz:

```text
php Ferramentas/PHPUnit/PHPUnit.phar --configuration Ferramentas/PHPUnit/PHPUnit.xml
```

A invocação executa a suíte configurada; não habilita a coleta de cobertura. Os requisitos de PHP e extensões estão no registro acima.

## Resultado

A suíte exige término com código zero, sem falhas, avisos, cenários arriscados, ignorados ou suíte vazia. O sucesso dos cenários não comprova cobertura integral. A interpretação e o tratamento das ocorrências seguem [Testes e cobertura integral](../00Regras.md#testes-e-cobertura-integral) e [Tratamento de falhas nos testes](../00Regras.md#tratamento-de-falhas-nos-testes).
