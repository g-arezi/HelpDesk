import React, { useEffect, useState } from 'react';
import './Dashboard.css';
import { FaLock, FaLockOpen } from 'react-icons/fa';

interface DashboardProps {
    refresh?: number;
    onTicketChange?: () => void;
}

const statusOptions = [
    { value: 'nao_aberto', label: 'Não aberto' },
    { value: 'em_analise', label: 'Em análise' },
    { value: 'resolvido', label: 'Resolvido' },
];

const getFilteredTickets = (tickets: any[], status: string | null) => {
    if (!status) return tickets;
    return tickets.filter(t => t.status === status);
};

const Dashboard: React.FC<DashboardProps> = ({ refresh, onTicketChange }) => {
    const [stats, setStats] = useState({ open: 0, closed: 0 });
    const [period, setPeriod] = useState({ from: '', to: '' });
    const [allTickets, setAllTickets] = useState<any[]>([]);
    const [updating, setUpdating] = useState(false);
    const [statusFilter, setStatusFilter] = useState<string | null>(null);

    // Fetch stats from API
    useEffect(() => {
        fetch(`${process.env.REACT_APP_API_URL}/tickets_api.php`, { credentials: 'include' })
            .then(res => {
                if (!res.ok) throw new Error('Erro ao buscar tickets');
                return res.json();
            })
            .then(data => {
                setAllTickets(data);
                setStats({
                    open: data.filter((t: any) => t.status === 'nao_aberto' || t.status === 'Open').length,
                    closed: data.filter((t: any) => t.status === 'resolvido' || t.status === 'Closed').length,
                });
            })
            .catch(err => {
                setAllTickets([]);
                setStats({ open: 0, closed: 0 });
                // Opcional: exibir erro para o usuário
                // alert('Erro ao buscar tickets: ' + err.message);
            });
    }, [refresh]);

    // Exemplo: alterar status de todos os chamados abertos para resolvido
    const handleBulkStatusChange = async (fromStatus: string, toStatus: string) => {
        setUpdating(true);
        const ticketsToUpdate = allTickets.filter(t => t.status === fromStatus);
        for (const t of ticketsToUpdate) {
            await fetch(`${process.env.REACT_APP_API_URL}/tickets_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id: t.id ?? t._id ?? t.ticket_id ?? allTickets.indexOf(t), status: toStatus })
            });
        }
        setUpdating(false);
        if (onTicketChange) onTicketChange();
    };

    const filteredTickets = getFilteredTickets(allTickets, statusFilter);

    // Nova contagem precisa refletir exatamente o que está na aba Tickets
    const countNaoAberto = allTickets.filter(t => t.status === 'nao_aberto').length;
    const countEmAnalise = allTickets.filter(t => t.status === 'em_analise').length;
    const countResolvido = allTickets.filter(t => t.status === 'resolvido').length;

    return (
        <div className="dashboard-v2">
            <div className="dashboard-header-v2">
                <h2 style={{margin:0,fontWeight:700}}>Dashboard</h2>
                <div className="dashboard-breadcrumb">Gerencial &gt; Dashboard</div>
            </div>
            {/* Painel de contadores clicáveis */}
            <div style={{background:'#fff',borderRadius:12,boxShadow:'0 1px 6px #e0e0e0',padding:'18px 24px',margin:'18px 0 28px 0',display:'flex',gap:32,alignItems:'center',fontSize:18,fontWeight:500}}>
                <span
                    style={{color:'#d70022',cursor:'pointer',textDecoration:statusFilter==='nao_aberto'? 'underline':'none'}}
                    onClick={()=>setStatusFilter(statusFilter==='nao_aberto'?null:'nao_aberto')}
                >
                    <b>Chamados em aberto:</b> <span style={{fontWeight:700}}>{countNaoAberto}</span>
                </span>
                <span
                    style={{color:'#ff9800',cursor:'pointer',textDecoration:statusFilter==='em_analise'? 'underline':'none'}}
                    onClick={()=>setStatusFilter(statusFilter==='em_analise'?null:'em_analise')}
                >
                    <b>Em andamento:</b> <span style={{fontWeight:700}}>{countEmAnalise}</span>
                </span>
                <span
                    style={{color:'#388e3c',cursor:'pointer',textDecoration:statusFilter==='resolvido'? 'underline':'none'}}
                    onClick={()=>setStatusFilter(statusFilter==='resolvido'?null:'resolvido')}
                >
                    <b>Encerrados:</b> <span style={{fontWeight:700}}>{countResolvido}</span>
                </span>
            </div>
            <div className="dashboard-filters-row">
                <select className="dashboard-select">
                    <option>Selecionar</option>
                </select>
                <div className="dashboard-period">
                    <label>Período:</label>
                    <input type="date" value={period.from} onChange={e => setPeriod(p => ({ ...p, from: e.target.value }))} />
                    <span style={{margin:'0 6px'}}>até</span>
                    <input type="date" value={period.to} onChange={e => setPeriod(p => ({ ...p, to: e.target.value }))} />
                    <button className="dashboard-filter-btn">Filtrar</button>
                </div>
            </div>
            <div className="dashboard-cards-row">
                <div className={`dashboard-card open${statusFilter==='nao_aberto'?' active':''}`} style={{cursor:'pointer',opacity:updating?0.6:1, background:'#43a047', color:'#fff'}} onClick={() => setStatusFilter(statusFilter==='nao_aberto'?null:'nao_aberto')} title="Filtrar por Não aberto" tabIndex={0} aria-pressed={statusFilter==='nao_aberto'}>
                    <FaLockOpen size={36} />
                    <div style={{flex:1}}>
                        <div className="dashboard-card-label">NÃO ABERTO</div>
                        <div className="dashboard-card-value">{countNaoAberto}</div>
                    </div>
                </div>
                <div className={`dashboard-card inprogress${statusFilter==='em_analise'?' active':''}`} style={{cursor:'pointer',opacity:updating?0.6:1, background:'#ff9800', color:'#fff'}} onClick={() => setStatusFilter(statusFilter==='em_analise'?null:'em_analise')} title="Filtrar por Em análise" tabIndex={0} aria-pressed={statusFilter==='em_analise'}>
                    <FaLock size={36} />
                    <div style={{flex:1}}>
                        <div className="dashboard-card-label">EM ANÁLISE</div>
                        <div className="dashboard-card-value">{countEmAnalise}</div>
                    </div>
                </div>
                <div className={`dashboard-card closed${statusFilter==='resolvido'?' active':''}`} style={{cursor:'pointer',opacity:updating?0.6:1, background:'#d32f2f', color:'#fff'}} onClick={() => setStatusFilter(statusFilter==='resolvido'?null:'resolvido')} title="Filtrar por Resolvido" tabIndex={0} aria-pressed={statusFilter==='resolvido'}>
                    <FaLock size={36} />
                    <div style={{flex:1}}>
                        <div className="dashboard-card-label">RESOLVIDO</div>
                        <div className="dashboard-card-value">{countResolvido}</div>
                    </div>
                </div>
            </div>
            <div className="dashboard-charts-row">
                <div className="dashboard-chart-box">
                    <div className="dashboard-chart-title">Top 10 atendentes com chamados abertos</div>
                    <div className="dashboard-chart-placeholder">{filteredTickets.length ? '[Gráfico de atendentes]' : 'Nenhum registro foi encontrado'}</div>
                </div>
                <div className="dashboard-chart-box">
                    <div className="dashboard-chart-title">Top produtos</div>
                    <div className="dashboard-chart-placeholder">{filteredTickets.length ? '[Gráfico de produtos]' : '[Gráfico de produtos]'}</div>
                </div>
            </div>
            <div className="dashboard-charts-row">
                <div className="dashboard-chart-box">
                    <div className="dashboard-chart-title">Por prioridade</div>
                    <div className="dashboard-chart-placeholder">{filteredTickets.length ? '[Gráfico de prioridade]' : '[Gráfico de prioridade]'}</div>
                </div>
                <div className="dashboard-chart-box">
                    <div className="dashboard-chart-title">Por categoria</div>
                    <div className="dashboard-chart-placeholder">{filteredTickets.length ? '[Gráfico de categoria]' : '[Gráfico de categoria]'}</div>
                </div>
                <div className="dashboard-chart-box">
                    <div className="dashboard-chart-title">Por tipo problema</div>
                    <div className="dashboard-chart-placeholder">{filteredTickets.length ? '[Gráfico de tipo problema]' : '[Gráfico de tipo problema]'}</div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;