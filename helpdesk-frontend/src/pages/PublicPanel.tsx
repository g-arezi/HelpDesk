import React, { useState } from 'react';
import TicketForm from '../components/TicketForm';
import LoginForm from '../components/LoginForm';
import { FaSearch, FaComments, FaChevronLeft, FaSpinner, FaUser, FaPhone, FaEnvelope, FaCheckCircle, FaHourglassHalf } from 'react-icons/fa';

interface Ticket {
    id: number;
    title: string;
    status: string;
    name?: string;
    email?: string;
    subject?: string;
    message?: string;
    telefone?: string;
}

// Optional: Accept onLogout prop for future integration
interface PublicPanelProps {
    onLogout?: () => void;
    isLoggedIn?: boolean; // for técnico/admin, not used for solicitante
    onLogin?: () => void; // callback for successful técnico/admin login
}

const PublicPanel: React.FC<PublicPanelProps> = ({ onLogout, isLoggedIn, onLogin }) => {
    const [email, setEmail] = useState('');
    const [telefone, setTelefone] = useState('');
    const [tickets, setTickets] = useState<Ticket[]>([]);
    const [selectedTicket, setSelectedTicket] = useState<Ticket | null>(null);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [showLogin, setShowLogin] = useState(false);

    // Busca de chamados integrada com a nova API JSON
    const buscarTickets = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setLoading(true);
        setTickets([]);
        setSelectedTicket(null);
        try {
            const params = new URLSearchParams();
            if (email) params.append('email', email);
            if (telefone) params.append('telefone', telefone);
            const res = await fetch(`${process.env.REACT_APP_API_URL}/buscarchamados.php?${params.toString()}`);
            const data = await res.json();
            if (!data.success) {
                setError(data.error || 'Nenhum chamado encontrado.');
                setTickets([]);
            } else {
                setTickets(data.tickets || []);
            }
        } catch (err) {
            setError('Erro ao buscar chamados.');
        }
        setLoading(false);
    };

    // Função para autenticar via API PHP
    const handleLogin = async (username: string, password: string) => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch(`${process.env.REACT_APP_API_URL}/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ login: username, senha: password })
            });
            const data = await res.json();
            if (data.success) {
                if (onLogin) onLogin();
                setShowLogin(false);
            } else {
                setError(data.error || 'Login inválido.');
            }
        } catch {
            setError('Erro ao autenticar.');
        }
        setLoading(false);
    };

    // Simple navigation bar for public panel
    const NavBar = () => (
        <nav style={{display:'flex',justifyContent:'space-between',alignItems:'center',background:'#1976d2',color:'#fff',padding:'12px 24px',borderRadius:8,marginBottom:24}}>
            <span style={{fontWeight:700,fontSize:20}}>Helpdesk Público</span>
            <div style={{display:'flex',gap:12,alignItems:'center'}}>
                {!isLoggedIn && (
                    <button onClick={()=>setShowLogin(true)} style={{background:'#fff',color:'#1976d2',border:'none',padding:'6px 16px',borderRadius:4,cursor:'pointer',fontWeight:600}}>Painel Técnico/Admin</button>
                )}
                {isLoggedIn && (
                    <button onClick={onLogout} style={{background:'#fff',color:'#1976d2',border:'none',padding:'6px 16px',borderRadius:4,cursor:'pointer',fontWeight:600}}>Logout</button>
                )}
            </div>
        </nav>
    );

    // Novo: loading animado com ícone
    const LoadingSpinner = () => (
        <div style={{display:'flex',justifyContent:'center',alignItems:'center',margin:'24px 0'}}>
            <FaSpinner size={44} color="#1976d2" className="spin" />
            <style>{`.spin{animation:spin 1s linear infinite;}@keyframes spin{100%{transform:rotate(360deg);}}`}</style>
        </div>
    );

    return (
        <div className="public-panel" style={{maxWidth:600,margin:'0 auto',padding:24,background:'#fff',borderRadius:12,boxShadow:'0 2px 16px #0001'}}>
            <NavBar />
            {showLogin && (
                <div style={{marginBottom:24}}>
                    <LoginForm onSuccess={() => { if (onLogin) onLogin(); setShowLogin(false); }} onError={setError} />
                </div>
            )}
            {!showLogin && (
                <>
                    <h1 style={{marginBottom:8,display:'flex',alignItems:'center',gap:10}}><FaComments size={24} color="#1976d2"/> Abrir ou Buscar Chamado</h1>
                    <TicketForm />
                    <hr style={{margin:'32px 0'}} />
                    <h2 style={{display:'flex',alignItems:'center',gap:8}}><FaSearch size={20}/> Buscar meus chamados</h2>
                    <form onSubmit={buscarTickets} style={{marginBottom:18,display:'flex',gap:8,flexWrap:'wrap',alignItems:'center'}}>
                        <span style={{display:'flex',alignItems:'center',gap:4}}><FaEnvelope size={16}/><input value={email} onChange={e=>setEmail(e.target.value)} placeholder="E-mail utilizado" type="email" style={{flex:1,minWidth:180}} /></span>
                        <span style={{display:'flex',alignItems:'center',gap:4}}><FaPhone/><input value={telefone} onChange={e=>setTelefone(e.target.value)} placeholder="Telefone utilizado" style={{flex:1,minWidth:140}} /></span>
                        <button type="submit" disabled={loading} style={{background:'#1976d2',color:'#fff',border:'none',padding:'6px 16px',borderRadius:4,cursor:'pointer',display:'flex',alignItems:'center',gap:6}}><FaSearch size={16}/> Buscar</button>
                    </form>
                    {loading && <LoadingSpinner />}
                    {error && <div style={{color:'#d32f2f',background:'#ffebee',borderRadius:6,padding:'8px 12px',marginBottom:12,textAlign:'center',display:'flex',alignItems:'center',gap:8}}><FaHourglassHalf/>{error}</div>}
                    {tickets.length > 0 && !selectedTicket && (
                        <ul style={{listStyle:'none',padding:0}}>
                            {tickets.map((t) => (
                                <li key={t.id} style={{background:'#f7fafd',marginBottom:12,padding:16,borderRadius:8,boxShadow:'0 1px 4px #0001',transition:'box-shadow 0.2s',position:'relative',overflow:'hidden'}}>
                                    <div style={{display:'flex',alignItems:'center',gap:8,marginBottom:6}}>
                                        <FaCheckCircle size={18} color={t.status==='Resolvido'?'#388e3c':t.status==='Em análise'?'#fbc02d':'#1976d2'} />
                                        <b>Assunto:</b> <span style={{color:'#1976d2'}}>{t.subject}</span>
                                    </div>
                                    <div style={{display:'flex',alignItems:'center',gap:8}}>
                                        <b>Status:</b> <span style={{color:t.status==='Resolvido'?'#388e3c':t.status==='Em análise'?'#fbc02d':'#1976d2'}}>{t.status}</span>
                                    </div>
                                    <div style={{margin:'6px 0'}}><b>Mensagem:</b> <span style={{color:'#333'}}>{t.message}</span></div>
                                    <div style={{display:'flex',alignItems:'center',gap:8}}><FaPhone size={16} color="#888" /><span style={{color:'#555'}}>{t.telefone}</span></div>
                                    <button onClick={()=>setSelectedTicket(t)} style={{marginTop:10,background:'#1976d2',color:'#fff',border:'none',padding:'6px 18px',borderRadius:4,cursor:'pointer',fontWeight:600,boxShadow:'0 1px 2px #0001',display:'flex',alignItems:'center',gap:6,transition:'background 0.2s'}}><FaComments size={16}/> Abrir Chat</button>
                                </li>
                            ))}
                        </ul>
                    )}
                    {selectedTicket && (
                        <div style={{marginTop:24}}>
                            <h3 style={{display:'flex',alignItems:'center',gap:8}}><FaComments size={18}/> Chat do Chamado</h3>
                            <ChatOnly ticket={selectedTicket} />
                            <div style={{display:'flex',gap:8,marginTop:8}}>
                                <button onClick={()=>setSelectedTicket(null)} style={{background:'#888',color:'#fff',border:'none',padding:'4px 12px',borderRadius:4,cursor:'pointer',display:'flex',alignItems:'center',gap:6}}><FaChevronLeft size={16}/> Voltar</button>
                            </div>
                        </div>
                    )}
                </>
            )}
        </div>
    );
};

// Componente só para chat, sem detalhes extras
const ChatOnly: React.FC<{ticket: Ticket}> = ({ ticket }) => {
    const [chatMessages, setChatMessages] = useState<any[]>([]);
    const [chatAuthor, setChatAuthor] = useState('');
    const [chatText, setChatText] = useState('');
    const [chatLoading, setChatLoading] = useState(false);
    React.useEffect(() => { loadChat(); /* eslint-disable-next-line */ }, [ticket.id]);
    function loadChat() {
        setChatLoading(true);
        fetch(`${process.env.REACT_APP_API_URL}/chat.php?id=${ticket.id}`)
            .then(res => res.json())
            .then(data => { setChatMessages(data); setChatLoading(false); });
    }
    async function sendChat(e: React.FormEvent) {
        e.preventDefault();
        setChatLoading(true);
        await fetch(`${process.env.REACT_APP_API_URL}/chat.php?id=${ticket.id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ author: chatAuthor, message: chatText })
        });
        setChatText('');
        loadChat();
    }
    return (
        <div style={{border:'1px solid #b0bec5',padding:16,borderRadius:10,background:'#f9f9fb',boxShadow:'0 1px 4px #0001'}}>
            {chatLoading ? <div style={{textAlign:'center',margin:'18px 0'}}><FaSpinner size={32} color="#1976d2" className="spin" /><style>{`.spin{animation:spin 1s linear infinite;}@keyframes spin{100%{transform:rotate(360deg);}}`}</style></div> : (
                <div style={{maxHeight:220,overflowY:'auto',background:'#fff',padding:10,marginBottom:10,borderRadius:6,border:'1px solid #eee'}}>
                    {chatMessages.length === 0 && <div style={{color:'#888',display:'flex',alignItems:'center',gap:6}}><FaHourglassHalf/> Nenhuma mensagem.</div>}
                    {chatMessages.map((msg, idx) => (
                        <div key={msg.id || idx} style={{marginBottom:10,padding:8,background:'#f7fafd',borderRadius:6,boxShadow:'0 1px 2px #e0e0e0',display:'flex',alignItems:'flex-start',gap:8}}>
                            <FaUser size={18} color="#1976d2" style={{marginTop:2}} />
                            <div>
                                <b style={{color:'#1976d2'}}>{msg.author}</b> <span style={{color:'#888',fontSize:12}}>[{msg.timestamp}]</span><br/>
                                <span style={{color:'#333'}}>{msg.message}</span>
                            </div>
                        </div>
                    ))}
                </div>
            )}
            <form onSubmit={sendChat} style={{display:'flex',gap:8,marginTop:8}}>
                <input value={chatAuthor} onChange={e=>setChatAuthor(e.target.value)} placeholder="Seu nome" required style={{flex:1,padding:8,borderRadius:6,border:'1px solid #b0bec5'}} />
                <input value={chatText} onChange={e=>setChatText(e.target.value)} placeholder="Mensagem" required style={{flex:2,padding:8,borderRadius:6,border:'1px solid #b0bec5'}} />
                <button type="submit" disabled={chatLoading} style={{background:'#1976d2',color:'#fff',border:'none',padding:'8px 18px',borderRadius:6,cursor:'pointer',fontWeight:600,display:'flex',alignItems:'center',gap:6}}><FaComments/> Enviar</button>
            </form>
        </div>
    );
};

export default PublicPanel;
