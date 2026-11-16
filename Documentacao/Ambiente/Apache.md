# Apache

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Identificar os arquivos de configuração de entrega e seus limites de exposição. A montagem dos arquivos na hospedagem está em [Hospedagem compartilhada](../Implantacao/Hospedagem.md).

## Pastas e arquivos no projeto

A conferência local utiliza Apache 2.4.65, instalado pelo Wamp. A aplicação não inclui o servidor.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `Publico/.htaccess` | Arquivo existente | Encaminhamento dinâmico, tipos e bloqueio de ativos não autorizados |
| `Publico/index.php` | Arquivo fixo existente | Única entrada dinâmica |
| `Publico/Manifesto.webmanifest` | Arquivo de protocolo existente | Manifesto de instalação, público e sem dados privados |
| `Dados/Arquivos/Cache/ApacheConferencia.conf` | Apoio local gerado e não versionado | Configuração isolada da conferência local |

## Escrita

`.htaccess` conserva o nome reconhecido pelo Apache. Seu cabeçalho usa comentários iniciados por `#`, com os campos de [Documentação nos arquivos](../01Padroes.md#documentação-nos-arquivos-com-comentários). O manifesto conserva extensão e chaves do protocolo; seu conteúdo é JSON sem comentários. Os demais nomes próprios seguem [Formato por elemento](../01Padroes.md#formato-por-elemento).

## Contrato de entrega

O servidor usa somente `Publico` como raiz de entrega. Requisições dinâmicas são encaminhadas a `index.php`. O ambiente DEVE permitir `.htaccess`, `mod_rewrite`, `mod_mime` e as diretivas utilizadas. A hospedagem DEVE ter HTTPS e repassar sua condição ao PHP. A aplicação não exige mudança global de versão, módulo de aplicação persistente ou acesso administrativo ao servidor.

A lista estática autorizada contém `Principal.min.css`, `Principal.min.js`, `Bomba.svg`, o manifesto, as fontes variáveis WOFF2 de Manrope e Orbitron e a fonte sólida WOFF2 do Font Awesome 7.3.1. Os caminhos exatos têm uma única definição em `.htaccess`; o apoio da prévia PHP lê essa mesma lista.

Originais CSS e JavaScript, metadados, licenças, READMEs, listagem e outros arquivos de Ativos ficam bloqueados. Nenhum PHP de Ativos é autorizado. O manifesto usa `application/manifest+json`; folhas, scripts, imagens e fontes recebem seus tipos correspondentes.

A prévia com servidor embutido usa `Testes/Apoio/Servidor.php` para a mesma lista de ativos, mas não substitui a conferência das diretivas do Apache. Originais retornam 404 nessa prévia e 403 no Apache.

A configuração do processo PHP DEVE permitir persistência de sessões. Seu destino é privado, fora da entrega pública e administrado pelo ambiente.
