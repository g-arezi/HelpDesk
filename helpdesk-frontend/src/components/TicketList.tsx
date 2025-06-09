import React, { useEffect, useState } from 'react';
import './TicketList.css';

export interface Ticket {
    id: number;
    title: string;
    status: string;
    name?: string;
    email?: string;
    subject?: string;
    message?: string;
    telefone?: string;
}

interface TicketListProps {
    onSelect: (ticket: Ticket) => void;
    onStatusChange?: () => void;
}

const statusOptions = [
    { value: 'nao_aberto', label: 'Não aberto' },
    { value: 'em_analise', label: 'Em análise' },
    { value: 'resolvido', label: 'Resolvido' },
];

const TicketList: React.FC<TicketListProps> = ({ onSelect, onStatusChange }) => {
    const [tickets, setTickets] = useState<Ticket[]>([]);
    const [updating, setUpdating] = useState<number | null>(null);
    const [feedback, setFeedback] = useState<{type: 'success'|'error', message: string}|null>(null);

    useEffect(() => {
        fetch(`${process.env.REACT_APP_API_URL}/tickets_api.php`, { credentials: 'include' })
            .then(res => {
                if (!res.ok) throw new Error('Erro ao buscar tickets');
                return res.json();
            })
            .then(data => {
                const mapped = (data || []).map((t: any, idx: number) => {
                    let status = t.status;
                    if (!status || status === 'Desconhecido') status = 'nao_aberto';
                    return {
                        id: idx + 1,
                        title: t.subject || t.title || 'Sem título',
                        status,
                        name: t.name,
                        email: t.email,
                        subject: t.subject,
                        message: t.message,
                        telefone: t.telefone,
                    };
                });
                setTickets(mapped);
            })
            .catch(() => {
                setFeedback({type: 'error', message: 'Erro de conexão ao buscar tickets. Verifique o backend.'});
            });
    }, [updating]);

    const handleStatusChange = async (ticket: Ticket, newStatus: string) => {
        setUpdating(ticket.id);
        try {
            const res = await fetch(`${process.env.REACT_APP_API_URL}/tickets_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id: ticket.id - 1, status: newStatus })
            });
            if (res.ok) {
                setFeedback({type: 'success', message: 'Status atualizado com sucesso!'});
            } else {
                const data = await res.json().catch(() => ({}));
                setFeedback({type: 'error', message: data.error || 'Erro ao atualizar status.'});
            }
        } catch (e) {
            setFeedback({type: 'error', message: 'Erro de conexão ao atualizar status.'});
        }
        setTimeout(() => setFeedback(null), 2500);
        setUpdating(null);
        if (onStatusChange) onStatusChange();
    };

    return (
        <div className="ticket-list">
            <h2>Support Tickets</h2>
            {feedback && (
                <div style={{
                    background: feedback.type === 'success' ? '#e8f5e9' : '#ffebee',
                    color: feedback.type === 'success' ? '#388e3c' : '#d32f2f',
                    padding: '8px 16px',
                    borderRadius: 6,
                    marginBottom: 10,
                    fontWeight: 500
                }}>{feedback.message}</div>
            )}
            <ul>
                {tickets.map(ticket => (
                    <li
                        key={ticket.id}
                        className={`ticket-item ${ticket.status.toLowerCase()}`}
                        onClick={() => onSelect(ticket)}
                        style={{ cursor: 'pointer' }}
                    >
                        <h3>{ticket.title}</h3>
                        <p>Status: {ticket.status}</p>
                        <select
                            value={ticket.status}
                            onChange={e => handleStatusChange(ticket, e.target.value)}
                            disabled={updating === ticket.id}
                        >
                            {statusOptions.map(opt => (
                                <option key={opt.value} value={opt.value}>{opt.label}</option>
                            ))}
                        </select>
                        <p><strong>Nome:</strong> {ticket.name}</p>
                        <p><strong>Email:</strong> {ticket.email}</p>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default TicketList;