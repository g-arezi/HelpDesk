import React, { useEffect, useState } from 'react';
import { Ticket } from './TicketList';
import './TicketList.css';

interface TicketDetailProps {
    ticket: Ticket;
}

interface ChatMessage {
    author: string;
    message: string;
    timestamp: string;
}

const statusOptions = [
    { value: 'nao_aberto', label: 'Não aberto' },
    { value: 'em_analise', label: 'Em análise' },
    { value: 'resolvido', label: 'Resolvido' },
];

const TicketDetail: React.FC<TicketDetailProps> = ({ ticket }) => {
    const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
    const [chatAuthor, setChatAuthor] = useState('');
    const [chatText, setChatText] = useState('');
    const [chatLoading, setChatLoading] = useState(false);
    const [showChat, setShowChat] = useState(false);
    const [status, setStatus] = useState(ticket.status);
    const [updating, setUpdating] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => { setStatus(ticket.status); }, [ticket.status, ticket.id]);

    const loadChat = () => {
        setChatLoading(true);
        setError(null);
        fetch(`${process.env.REACT_APP_API_URL}/chat.php?id=${ticket.id}`, { credentials: 'include' })
            .then(res => {
                if (!res.ok) throw new Error('Erro ao buscar mensagens do chat');
                return res.json();
            })
            .then(data => {
                setChatMessages(data);
                setChatLoading(false);
            })
            .catch(() => {
                setError('Erro de conexão ao buscar mensagens do chat. Verifique o backend.');
                setChatLoading(false);
            });
    };

    const sendChat = async (e: React.FormEvent) => {
        e.preventDefault();
        setChatLoading(true);
        await fetch(`${process.env.REACT_APP_API_URL}/chat.php?id=${ticket.id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ author: chatAuthor, message: chatText }),
            credentials: 'include',
        });
        setChatText('');
        loadChat();
    };

    const handleStatusChange = async (newStatus: string) => {
        setUpdating(true);
        setError(null);
        try {
            const res = await fetch(`${process.env.REACT_APP_API_URL}/tickets_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id: ticket.id - 1, status: newStatus })
            });
            if (!res.ok) {
                setError('Erro ao atualizar status do ticket.');
            } else {
                setStatus(newStatus);
            }
        } catch (e) {
            setError('Erro de conexão ao atualizar status. Verifique o backend.');
        }
        setUpdating(false);
    };

    return (
        <div className="ticket-detail">
            <h2>{ticket.title}</h2>
            {error && <div style={{background:'#ffebee',color:'#d32f2f',padding:'8px 16px',borderRadius:6,marginBottom:10,fontWeight:500}}>{error}</div>}
            <p><strong>Status:</strong> {status}
                <select
                    value={status}
                    onChange={e => handleStatusChange(e.target.value)}
                    disabled={updating}
                    style={{marginLeft:8}}
                >
                    {statusOptions.map(opt => (
                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                    ))}
                </select>
            </p>
            <p><strong>Nome:</strong> {ticket.name}</p>
            <p><strong>Email:</strong> {ticket.email}</p>
            <p><strong>Assunto:</strong> {ticket.subject}</p>
            <p><strong>Mensagem:</strong> {ticket.message}</p>
            <p><strong>Telefone:</strong> {ticket.telefone}</p>
            <button onClick={() => { setShowChat(!showChat); if (!showChat) loadChat(); }} style={{marginTop:12}}>
                {showChat ? 'Fechar Chat' : 'Abrir Chat'}
            </button>
            {showChat && (
                <div style={{border:'1px solid #ccc',padding:12,marginTop:12,borderRadius:8}}>
                    <h4>Chat do Ticket #{ticket.id}</h4>
                    {chatLoading ? <div>Carregando mensagens...</div> : (
                        <div style={{maxHeight:200,overflowY:'auto',background:'#fafafa',padding:8,marginBottom:8}}>
                            {chatMessages.length === 0 && <div>Nenhuma mensagem.</div>}
                            {chatMessages.map((msg, idx) => (
                                <div key={idx} style={{marginBottom:6}}>
                                    <b>{msg.author}</b> <span style={{color:'#888',fontSize:12}}>[{msg.timestamp}]</span><br/>
                                    {msg.message}
                                </div>
                            ))}
                        </div>
                    )}
                    <form onSubmit={sendChat} style={{display:'flex',gap:8}}>
                        <input value={chatAuthor} onChange={e=>setChatAuthor(e.target.value)} placeholder="Seu nome" required style={{flex:1}} />
                        <input value={chatText} onChange={e=>setChatText(e.target.value)} placeholder="Mensagem" required style={{flex:2}} />
                        <button type="submit" disabled={chatLoading}>Enviar</button>
                    </form>
                </div>
            )}
        </div>
    );
};

export default TicketDetail;