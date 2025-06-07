<div id="tab-content-buscar" class="tab-content">
    <h2 style="color:#007bff;">Buscar chamados por e-mail ou telefone</h2>
    <form id="buscar-form" onsubmit="return buscarChamados();" style="background:#f7f7f7;padding:18px 24px;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;max-width:420px;margin-bottom:18px;">
        <div style="margin-bottom:12px;">
            <label for="buscar-email" style="font-weight:bold;">E-mail utilizado:</label>
            <input type="email" id="buscar-email" name="buscar-email" style="width:220px;padding:6px 10px;border-radius:4px;border:1px solid #ccc;">
        </div>
        <div style="margin-bottom:12px;">
            <span style="margin:0 10px;font-weight:bold;">ou</span>
        </div>
        <div style="margin-bottom:18px;">
            <label for="buscar-telefone" style="font-weight:bold;">Telefone utilizado:</label>
            <input type="text" id="buscar-telefone" name="buscar-telefone" style="width:180px;padding:6px 10px;border-radius:4px;border:1px solid #ccc;">
        </div>
        <button type="submit" class="btn" style="width:100%;font-size:16px;">Buscar meus chamados</button>
        <div style="font-size:13px;color:#888;margin-top:10px;">Preencha <b>apenas um</b> dos campos acima para consultar todos os seus chamados.</div>
    </form>
    <div id="resultado-busca" style="margin-top:20px;"></div>
</div>

<script>
function showTab(tab) {
    // ...existing code...
}
async function buscarChamados() {
    const email = document.getElementById('buscar-email').value.trim();
    const telefone = document.getElementById('buscar-telefone').value.trim();
    if (!email && !telefone) {
        document.getElementById('resultado-busca').innerHTML = '<div style="background:#ffeaea;color:#d70022;padding:12px 18px;border-radius:6px;font-weight:bold;">Por favor, informe o <b>e-mail</b> ou <b>telefone</b> utilizado para abrir o chamado.</div>';
        return false;
    }
    const params = new URLSearchParams();
    if (email) params.append('email', email);
    if (telefone) params.append('telefone', telefone);
    const res = await fetch('buscarchamados.php?' + params.toString());
    const data = await res.json();
    let html = '';
    if (data && data.length > 0) {
        html = '<div style="background:#e8f5e9;color:#388e3c;padding:10px 18px;border-radius:6px;font-weight:bold;margin-bottom:18px;">Chamados encontrados:</div>';
        data.forEach(function(ticket, idx) {
            html += `<div style='margin-bottom:18px;padding:16px 18px;background:#f7f7f7;border-radius:8px;box-shadow:0 1px 4px #e0e0e0;'>` +
                `<div style='font-size:15px;'><b>Status:</b> <span style='font-weight:bold;'>${ticket.status}</span></div>` +
                `<div style='font-size:15px;'><b>Assunto:</b> ${ticket.subject}</div>` +
                `<div style='font-size:15px;'><b>Mensagem:</b> ${ticket.message}</div>` +
                `<div style='font-size:15px;'><b>Telefone:</b> ${ticket.telefone || '-'}</div>` +
                `</div>`;
        });
    } else {
        html = '<div style="background:#ffeaea;color:#d70022;padding:12px 18px;border-radius:6px;font-weight:bold;">Nenhum chamado encontrado para o e-mail ou telefone informado.</div>';
    }
    document.getElementById('resultado-busca').innerHTML = html;
    return false;
}


// ...existing code...
</script>