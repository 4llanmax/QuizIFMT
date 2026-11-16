# Node.js

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Identificar a execução dos testes JavaScript e a medição V8 no desenvolvimento. Os cenários e resultados estão em [Testes do jogo](../Testes/Integracao/Jogo/TestesJogo.md).

## Pastas e arquivos no projeto

O Node.js é programa do ambiente, não é incluído no repositório e não é requisito da hospedagem PHP. O workflow seleciona Node 22. O comando Node não é identificado no PATH inicial da cópia. A conferência local do MCP e do pavio usa Node.js 22.23.5 em distribuição oficial Windows x64, extraída em `Dados/Arquivos/Cache/Node22` e ignorada pelo Git, com SHA-256 conferido pelo manifesto da distribuição. Os arquivos dessa distribuição não integram a entrega da aplicação.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `Testes/Unitario/Navegador/PrincipalTeste.mjs` | Arquivo existente | Cenários de comportamento e coleta detalhada V8 |
| `Testes/Apoio/ConferenciaJavaScript.mjs` | Arquivo existente | Entrada dos testes e recusa de cobertura insuficiente |
| `Testes/Apoio/ConferenciaNavegadorMCP.mjs` | Arquivo existente | Conferência de navegação do formulário OAuth em Chromium real e isolado |
| `Dados/Arquivos/Cache/CoberturaJavaScript/Relatorio.json` | Relatório gerado e não versionado | Resultado das versões original e processada |
| `.github/workflows/Validacao.yml` | Arquivo existente | Seleção do executor no ambiente descartável |

## Escrita

Os arquivos JavaScript próprios usam nomes e comentários de [Padrões](../01Padroes.md#formato-por-elemento). A extensão `.mjs` e os nomes dos módulos `node:` conservam as grafias de módulos do executor. Não há pacote npm, dependência de terceiros ou instalação de ferramentas no servidor da aplicação.

## Execução e resultado

Executar `node Testes/Apoio/ConferenciaJavaScript.mjs` da raiz, usando Node 22 ou executor compatível com os módulos nativos utilizados. O apoio resolve a raiz a partir do próprio arquivo, sem depender de diretório corrente implícito.

A ferramenta usa `vm`, `assert` e `inspector` nativos. A coleta exige instrumentação de blocos; somente contagem de chamadas de função não comprova cobertura de ramos. Cada cenário recebe identificação própria para a compilação e seus contextos permanecem vivos até a leitura dos contadores.

Falha de asserção, ausência de instrumentação ou intervalo não exercitado causa término não zero. A execução comprova os contratos com o navegador substituído e não substitui a conferência visual ou das permissões reais do dispositivo.

A conferência da navegação OAuth usa `node Testes/Apoio/ConferenciaNavegadorMCP.mjs`, os módulos nativos HTTP e WebSocket e uma instalação existente de Chrome ou Edge. A variável `MCP_NAVEGADOR` permite informar outro executável Chromium. O apoio preserva as verificações de segurança do navegador e usa perfil próprio e dados fictícios. Seu alcance e os limites estão em [Evidência e limites](../Testes/Integracao/Jogo/TestesJogo.md#evidência-e-limites).
