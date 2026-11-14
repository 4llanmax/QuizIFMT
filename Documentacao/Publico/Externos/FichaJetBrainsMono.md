# Ficha da JetBrains Mono

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 18/10/2026      | Revisado | 12/11/2026 |

## Objetivo

A ficha identifica a fonte JetBrains Mono instalada, sua origem, seus arquivos físicos e os registros de conferência da distribuição. As orientações gerais de organização e uso de recursos de terceiros estão em [Externos](../../05Externos.md).

## Identificação

| Campo | Conteúdo |
| --- | --- |
| Nome | JetBrains Mono |
| Versão | 2.211, declarada como `Version 2.211` na tabela de nomes dos arquivos TTF; versão v24 dos arquivos distribuídos pelo Google Fonts |
| Origem | Pacote de download da família no [Google Fonts](https://fonts.google.com/specimen/JetBrains+Mono), obtido pelo manifesto `https://fonts.google.com/download/list?family=JetBrains+Mono`. O [OFL.txt distribuído](../../../Publico/Ativos/Fontes/JetBrainsMono/OFL.txt) identifica o projeto de origem, [JetBrains Mono](https://github.com/JetBrains/JetBrainsMono) |
| Licença | SIL Open Font License 1.1, conforme o OFL.txt distribuído e a tabela de nomes dos TTF. Termos em [OFL.txt](../../../Publico/Ativos/Fontes/JetBrainsMono/OFL.txt) |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Finalidade | Disponibilizar a família tipográfica monoespaçada JetBrains Mono para a apresentação da aplicação |
| Integração | Não implementada: não há declaração CSS ou componente que carregue a fonte. Os requisitos de integração estão em [Integração e atualização](#integração-e-atualização) |
| Requisitos | Navegador com suporte a WOFF2 e a fontes variáveis e servidor com o tipo de conteúdo `font/woff2` |
| Inclusão | Pendente: registrar a inclusão no projeto |
| Alterações locais | Os dois TTF variáveis foram convertidos para WOFF2, alterando somente o formato. Os TTF variáveis originais permanecem ao lado dos WOFF2, e os TTF estáticos não foram instalados, conforme [Arquivos instalados](#arquivos-instalados). Nenhuma no README.txt. O OFL.txt tem o fim de linha normalizado para LF pelo `.gitattributes` do projeto; seu texto é idêntico ao distribuído |
| Verificação | Comparar os TTF com a distribuição de origem e conferir os resumos dos arquivos instalados, as tabelas, os nomes, as métricas e os glifos dos WOFF2 em relação aos TTF; testar o carregamento quando implementado |

## Arquivos instalados

A pasta `Publico/Ativos/Fontes/JetBrainsMono` contém os TTF variáveis distribuídos, `JetBrainsMono-VariableFont_wght.ttf` e `JetBrainsMono-Italic-VariableFont_wght.ttf`; os WOFF2 convertidos deles, `JetBrainsMono-VariableFont_wght.woff2` e `JetBrainsMono-Italic-VariableFont_wght.woff2`; e os arquivos README.txt e OFL.txt distribuídos. O README descreve os TTF variáveis e a subpasta `static` com 16 TTF estáticos; esta instalação contém somente as fontes variáveis, em TTF e em WOFF2, que abrangem todos os pesos do eixo `wght`. A licença e o README são metadados da distribuição e seguem as condições de entrega de [Ativos](../../02Estrutura.md#ativos).

O caminho omite a versão para permanecer estável e receber uma única versão da fonte por vez, conforme [Pastas de distribuições externas](../../01Padroes.md#pastas-de-distribuições-externas). A versão instalada permanece identificada na ficha.

A conversão foi feita com fontTools 4.67.0 e compressão Brotli, sem subconjunto de glifos nem alteração de tabelas, nomes ou avisos. Os SHA-256 registrados são:

```text
e14de35424c095970d34fbb64b631418c4f3931f304c6d91c055f7e7efece115  JetBrainsMono-VariableFont_wght.woff2, arquivo instalado
c260b30992be72c084341efb860a4a86dd2bf2b0a9322508582884e8d660a5c9  JetBrainsMono-Italic-VariableFont_wght.woff2, arquivo instalado
192a3158a926768d1fa520164f0fb0f40a6ad085435aa854e43eb14cb40d1aab  JetBrainsMono-VariableFont_wght.ttf, original instalado
2d8ac6a590c2df8f63ae44807c40cac63bc481d197c4cb91d48f1a0f587fb32e  JetBrainsMono-Italic-VariableFont_wght.ttf, original instalado
```

Os resumos dos TTF instalados coincidem com os dos arquivos distribuídos pelo Google Fonts. Esses resultados não comprovam o carregamento da fonte pela aplicação.

## Integração e atualização

A integração da fonte DEVE declarar o endereço público dos arquivos utilizados no CSS do componente responsável e conferir o carregamento e a apresentação nos navegadores adotados. Esta regra não declara um CSS ou componente existente.

Ao substituir a fonte, conferir seus metadados, a licença, os resumos e as referências dos consumidores. O procedimento geral está em [Atualização de ferramentas e dependências](../../00Regras.md#atualização-de-ferramentas-e-dependências).
