# Programação das rodadas

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Descrever acesso administrativo e programação das novas rodadas. A interface pública e a pontuação estão em [Jogo](../Site/Jogo.md).

## Acesso

O Painel atende GET `/admin` e os POSTs `/admin/entrar`, `/admin/salvar`, `/admin/restaurar` e `/admin/sair`, relativos ao prefixo público. GET sem acesso mostra o formulário. Salvar, restaurar e sair exigem sessão administrativa e CSRF válido.

A entrada recebe `senha` e compara com o hash privado. Cada origem tem dez tentativas por 15 minutos, em contador separado das contas do Site. Sucesso renova sessão e CSRF. Saída remove o acesso e também renova ambos. Conta de jogador não concede acesso administrativo.

Sem hash, com tipo de senha incorreto ou senha divergente, a entrada retorna HTTP 422 com mensagem genérica. Tentativas excedentes recebem 429; operações sem acesso ou CSRF recebem 403.

## Parâmetro administrativo

O provedor do Painel registra `ADMIN_SENHA_HASH` → `adminSenhaHash` no leitor comum. Sua finalidade é autenticar a administração; o tipo é texto com hash compatível com `password_verify`. É OPCIONAL e tem padrão vazio, que desabilita a entrada. O valor é secreto e vem do processo ou de `Configuracoes/Aplicacao/.env`, com a precedência da [Inicialização](../Nucleo/Inicializacao.md#gramática-dos-parâmetros). O modelo contém valor vazio e não inclui senha real.

O operador DEVE gerar o hash em PHP local com `password_hash` e `PASSWORD_DEFAULT`, guardar somente o hash na configuração privada e usar a senha original no formulário. É PROIBIDO colocar senha ou hash privado em arquivo público ou versionado.

## Programação

Salvar recebe `total`, `segundos`, `alerta` e a coleção `distribuicao[tema]`. Cada chave de tema corresponde ao acervo.

| Campo | Valores aceitos |
| --- | --- |
| Total | Inteiro de 5 a 48 |
| Segundos | Inteiro de 10 a 120 |
| Alerta | Inteiro de 3 a 30, menor que os segundos da pergunta |
| Distribuição | Todos os temas, quantidade inteira não negativa, sem superar as perguntas disponíveis |
| Soma dos temas | Exatamente igual ao total |

Capacidades são lidas do banco pelo próprio Painel, sem depender de implementação do Site. A atualização substitui atomicamente o documento de configuração. Alterações afetam somente partidas iniciadas depois de salvar; partidas existentes mantêm sua configuração e perguntas.

Restaurar aplica o documento padrão da carga inicial. Não apaga contas, partidas ou ranking. Jogadores fictícios e o apagamento geral do demonstrativo não integram a administração do banco compartilhado.

Sucesso produz HTTP 303 de volta ao Painel. Valores inválidos retornam 422 sem gravação; falhas técnicas retornam 503. Não há interface de alteração das perguntas; mudanças do acervo seguem scripts de manutenção do banco.

## Verificação

Acesso e programação estão ligados aos cenários em [Testes do jogo](../../Testes/Integracao/Jogo/TestesJogo.md).
