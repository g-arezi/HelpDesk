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

<!-- Switch de modo claro/noturno -->
<div class="mode-switch light" id="modeSwitch">
    <span class="icon" id="modeIcon">🌞</span>
    <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
    <span id="modeLabel">Claro</span>
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

// Novo switch de modo
const modeSwitch = document.getElementById('modeSwitch');
const modeToggle = document.getElementById('modeToggle');
const modeIcon = document.getElementById('modeIcon');
const modeLabel = document.getElementById('modeLabel');
function setMode(night) {
    document.body.classList.toggle('night', night);
    modeSwitch.classList.toggle('light', !night);
    modeSwitch.classList.toggle('night', night);
    modeToggle.checked = night;
    if(night) {
        modeIcon.textContent = '🌙';
        modeLabel.textContent = 'Noturno';
        localStorage.setItem('nightMode','1');
    } else {
        modeIcon.textContent = '🌞';
        modeLabel.textContent = 'Claro';
        localStorage.removeItem('nightMode');
    }
}
modeToggle.addEventListener('change', function() {
    setMode(this.checked);
});
// Inicialização
if(localStorage.getItem('nightMode')) setMode(true);
else setMode(false);
// Remove botão antigo
var oldBtn = document.getElementById('nightToggle');
if(oldBtn) oldBtn.remove();
</script>

<style>
/* Night/Light mode switcher - canto inferior esquerdo */
.mode-switch {
    position: fixed;
    left: 18px;
    bottom: 18px;
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 8px;
    background: #232a36;
    border-radius: 18px;
    padding: 6px 14px 6px 10px;
    box-shadow: 0 2px 12px #0003;
    color: #fff;
    font-size: 1.05rem;
    font-weight: 500;
    border: 1px solid #232a36;
    transition: background 0.3s, color 0.3s;
}
.mode-switch.light {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #b3c6e0;
}
.mode-switch input[type="checkbox"] {
    width: 36px;
    height: 20px;
    appearance: none;
    background: #bdbdbd;
    outline: none;
    border-radius: 12px;
    position: relative;
    transition: background 0.3s;
    cursor: pointer;
}
.mode-switch input[type="checkbox"]:checked {
    background: #1976d2;
}
.mode-switch input[type="checkbox"]::before {
    content: '';
    position: absolute;
    left: 3px;
    top: 3px;
    width: 14px;
    height: 14px;
    background: #fff;
    border-radius: 50%;
    transition: left 0.3s;
}
.mode-switch input[type="checkbox"]:checked::before {
    left: 19px;
}
.mode-switch .icon {
    font-size: 1.1em;
}
</style>