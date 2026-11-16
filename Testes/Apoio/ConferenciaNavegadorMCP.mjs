/**
 * Confere no Chromium real a política de envio de formulário e o retorno OAuth entre origens isoladas.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */
import assert from 'node:assert/strict';
import { spawn } from 'node:child_process';
import { createServer } from 'node:http';
import { access, mkdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { setTimeout as aguardar } from 'node:timers/promises';

const raiz = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const pasta = join(raiz, 'Dados/Arquivos/Cache/NavegadorMCP-' + process.pid);
await mkdir(pasta, { recursive: true });
const fonte = await readFile(join(raiz, 'Aplicacao/Nucleo/Transportes/Resposta.php'), 'utf8');
const politicaPadrao = fonte.match(/public const string POLITICA_CONTEUDO\s*=\s*"([^"]+)"/)?.[1] ?? fonte.match(/'Content-Security-Policy'\s*=>\s*"([^"]+)"/)?.[1];
assert.ok(politicaPadrao?.includes("form-action 'self'"), 'A política padrão deve conservar o envio para a própria origem.');
const recebimentos = [];
const mensagens = [];
const processos = [];
const servidores = [];
let navegador;
let websocket;

async function esperar(verificar, descricao) {
    const limite = Date.now() + 10000;
    while (Date.now() < limite) {
        const resultado = await verificar();
        if (resultado) return resultado;
        await aguardar(50);
    }
    throw Error('Tempo esgotado: ' + descricao);
}

async function abrirServidor(atender) {
    const servidor = createServer(atender);
    servidores.push(servidor);
    await new Promise((concluir, recusar) => servidor.once('error', recusar).listen(0, '127.0.0.1', concluir));
    return 'http://127.0.0.1:' + servidor.address().port;
}

try {
    const origemRetorno = await abrirServidor((pedido, resposta) => {
        recebimentos.push(pedido.url);
        resposta.writeHead(200, { 'Content-Type': 'text/html; charset=UTF-8' });
        resposta.end('<!doctype html><title>Retorno autorizado</title><h1>RETORNO_ACEITO</h1>');
    });
    const origemNaoAutorizada = origemRetorno.replace('127.0.0.1', 'localhost');
    const politicaPermitida = politicaPadrao.replace("form-action 'self'", "form-action 'self' " + origemRetorno);
    const origemFormulario = await abrirServidor((pedido, resposta) => {
        const url = new URL(pedido.url, 'http://127.0.0.1');
        const cenario = url.searchParams.get('cenario');
        const politica = cenario === 'padrao' ? politicaPadrao : politicaPermitida;
        if (pedido.method === 'POST') {
            const destino = cenario === 'malicioso' ? origemNaoAutorizada : origemRetorno;
            resposta.writeHead(303, { Location: destino + '/retorno?cenario=' + cenario, 'Content-Security-Policy': politica });
            resposta.end();
            return;
        }
        resposta.writeHead(200, { 'Content-Type': 'text/html; charset=UTF-8', 'Content-Security-Policy': politica });
        resposta.end('<!doctype html><title>Autorizar conexão fictícia</title><form method="post" action="/autorizar?cenario=' + cenario + '"><button type="submit">Autorizar teste</button></form>');
    });
    const candidatos = [process.env.MCP_NAVEGADOR, 'C:/Program Files/Google/Chrome/Application/chrome.exe', 'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe'].filter(Boolean);
    let executavel;
    for (const candidato of candidatos) {
        try { await access(candidato); executavel = candidato; break; } catch {}
    }
    assert.ok(executavel, 'Instale Chromium, Chrome ou Edge, ou informe MCP_NAVEGADOR.');
    navegador = spawn(executavel, ['--headless=new', '--no-first-run', '--no-default-browser-check', '--disable-background-networking', '--remote-debugging-address=127.0.0.1', '--remote-debugging-port=0', '--user-data-dir=' + join(pasta, 'Perfil'), 'about:blank'], { windowsHide: true, stdio: ['ignore', 'ignore', 'pipe'] });
    processos.push(navegador.pid);
    let erroProcesso;
    navegador.once('error', erro => { erroProcesso = erro; });
    navegador.stderr.on('data', dados => mensagens.push(dados.toString()));
    const arquivoPorta = join(pasta, 'Perfil/DevToolsActivePort');
    const depuracao = await esperar(async () => {
        if (erroProcesso) throw erroProcesso;
        try { return (await readFile(arquivoPorta, 'utf8')).trim().split('\n'); } catch { return null; }
    }, 'início do navegador isolado');
    websocket = new WebSocket('ws://127.0.0.1:' + depuracao[0] + depuracao[1]);
    await new Promise((concluir, recusar) => {
        const limite = setTimeout(() => recusar(Error('O ambiente bloqueou a conexão local ao navegador.')), 10000);
        websocket.addEventListener('open', () => { clearTimeout(limite); concluir(); }, { once: true });
        websocket.addEventListener('error', erro => { clearTimeout(limite); recusar(erro); }, { once: true });
    });
    let identificador = 0;
    const pendentes = new Map();
    const eventos = [];
    websocket.addEventListener('message', evento => {
        const mensagem = JSON.parse(evento.data);
        if (mensagem.id) {
            const pendente = pendentes.get(mensagem.id);
            pendentes.delete(mensagem.id);
            if (mensagem.error) pendente?.recusar(Error(mensagem.error.message));
            else pendente?.concluir(mensagem.result);
        } else eventos.push(mensagem);
    });
    const enviar = (method, params = {}, sessionId) => new Promise((concluir, recusar) => {
        const id = ++identificador;
        const limite = setTimeout(() => { pendentes.delete(id); recusar(Error('Sem resposta do navegador: ' + method)); }, 10000);
        pendentes.set(id, { concluir: resultado => { clearTimeout(limite); concluir(resultado); }, recusar: erro => { clearTimeout(limite); recusar(erro); } });
        websocket.send(JSON.stringify({ id, method, params, sessionId }));
    });
    const { product: produto } = await enviar('Browser.getVersion');
    console.log('Navegador isolado: ' + produto + '; PID ' + navegador.pid + '.');
    const resultados = [];
    for (const cenario of ['padrao', 'permitido', 'malicioso']) {
        const { targetId } = await enviar('Target.createTarget', { url: 'about:blank' });
        const { sessionId } = await enviar('Target.attachToTarget', { targetId, flatten: true });
        await enviar('Page.enable', {}, sessionId);
        await enviar('Log.enable', {}, sessionId);
        const inicio = origemFormulario + '/formulario?cenario=' + cenario;
        await enviar('Page.navigate', { url: inicio }, sessionId);
        await esperar(async () => (await enviar('Runtime.evaluate', { expression: 'location.href === ' + JSON.stringify(inicio) + ' && document.readyState === "complete"', returnByValue: true }, sessionId)).result.value, 'formulário ' + cenario);
        await enviar('Runtime.evaluate', { expression: 'document.querySelector("button").click()', userGesture: true }, sessionId);
        if (cenario === 'permitido') {
            await esperar(async () => (await enviar('Runtime.evaluate', { expression: 'document.body.textContent.includes("RETORNO_ACEITO")', returnByValue: true }, sessionId)).result.value, 'retorno autorizado');
        } else {
            await esperar(() => eventos.some(evento => evento.sessionId === sessionId && evento.method === 'Log.entryAdded' && evento.params.entry.text.includes('form-action')), 'bloqueio CSP ' + cenario);
        }
        const estado = (await enviar('Runtime.evaluate', { expression: '({url:location.href,texto:document.body.textContent})', returnByValue: true }, sessionId)).result.value;
        const violacoes = eventos.filter(evento => evento.sessionId === sessionId && evento.method === 'Log.entryAdded' && evento.params.entry.text.includes('form-action')).map(evento => evento.params.entry.text);
        if (cenario === 'permitido') {
            assert.equal(new URL(estado.url).origin, origemRetorno);
            assert.equal(violacoes.length, 0);
        } else {
            assert.ok(violacoes.length > 0);
            assert.ok(!estado.texto.includes('RETORNO_ACEITO'));
            assert.ok(!recebimentos.includes('/retorno?cenario=' + cenario));
        }
        resultados.push({ cenario, retorno: cenario === 'permitido' ? 'aceito' : 'bloqueado', url: estado.url, violacoes });
        console.log(cenario + ': ' + resultados.at(-1).retorno + '; violações CSP: ' + violacoes.length + '.');
        await enviar('Target.closeTarget', { targetId });
    }
    const relatorio = { navegador: produto, executavel, processos, politicaPadrao, politicaPermitida, resultados, recebimentos };
    await writeFile(join(pasta, 'Relatorio.json'), JSON.stringify(relatorio, null, 2) + '\n');
    console.log('Relatório privado: ' + join(pasta, 'Relatorio.json'));
    await enviar('Browser.close');
} finally {
    websocket?.close();
    if (navegador && navegador.exitCode === null) navegador.kill();
    for (const servidor of servidores) {
        servidor.closeAllConnections();
        await new Promise(concluir => servidor.close(concluir));
    }
    await writeFile(join(pasta, 'Chromium.log'), mensagens.join(''));
}
