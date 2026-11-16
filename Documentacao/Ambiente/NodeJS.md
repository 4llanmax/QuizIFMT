# Node.js

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Identificar a execução dos testes JavaScript e a medição V8 no desenvolvimento. Os cenários e resultados estão em [Testes do jogo](../Testes/Integracao/Jogo/TestesJogo.md).

## Pastas e arquivos no projeto

O Node.js é programa do ambiente, não é incluído no repositório e não é requisito da hospedagem PHP. O workflow seleciona Node 22. A execução local desta alteração usa o executor JavaScript da sessão; o comando Node não é identificado no PATH inicial da cópia.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `Testes/Unitario/Navegador/PrincipalTeste.mjs` | Arquivo existente | Cenários de comportamento e coleta detalhada V8 |
| `Testes/Apoio/ConferenciaJavaScript.mjs` | Arquivo existente | Entrada dos testes e recusa de cobertura insuficiente |
| `Dados/Arquivos/Cache/CoberturaJavaScript/Relatorio.json` | Relatório gerado e não versionado | Resultado das versões original e processada |
| `.github/workflows/Validacao.yml` | Arquivo existente | Seleção do executor no ambiente descartável |

## Escrita

Os arquivos JavaScript próprios usam nomes e comentários de [Padrões](../01Padroes.md#formato-por-elemento). A extensão `.mjs` e os nomes dos módulos `node:` conservam as grafias de módulos do executor. Não há pacote npm, dependência de terceiros ou instalação de ferramentas no servidor da aplicação.

## Execução e resultado

Executar `node Testes/Apoio/ConferenciaJavaScript.mjs` da raiz, usando Node 22 ou executor compatível com os módulos nativos utilizados. O apoio resolve a raiz a partir do próprio arquivo, sem depender de diretório corrente implícito.

A ferramenta usa `vm`, `assert` e `inspector` nativos. A coleta exige instrumentação de blocos; somente contagem de chamadas de função não comprova cobertura de ramos. Cada cenário recebe identificação própria para a compilação e seus contextos permanecem vivos até a leitura dos contadores.

Falha de asserção, ausência de instrumentação ou intervalo não exercitado causa término não zero. A execução comprova os contratos com o navegador substituído e não substitui a conferência visual ou das permissões reais do dispositivo.
