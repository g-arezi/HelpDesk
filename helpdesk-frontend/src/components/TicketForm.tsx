import React, { useState } from 'react';
import { Ticket } from './TicketList';
import './TicketList.css';

interface TicketFormProps {
    onTicketCreated?: (ticket: Ticket) => void;
}

const TicketForm: React.FC<TicketFormProps> = ({ onTicketCreated }) => {
    const [subject, setSubject] = useState('');
    const [message, setMessage] = useState('');
    const [email, setEmail] = useState('');
    const [name, setName] = useState('');
    const [telefone, setTelefone] = useState('');
    const [image, setImage] = useState<File | null>(null);
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setSuccess('');
        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('subject', subject);
            formData.append('message', message);
            formData.append('telefone', telefone);
            if (image) formData.append('image', image);
            let res;
            try {
                res = await fetch(`${process.env.REACT_APP_API_URL}/open.php`, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include',
                });
            } catch (err) {
                setError('Erro de conexão com o servidor. Verifique se o backend está rodando.');
                setLoading(false);
                return;
            }
            const contentType = res.headers.get('content-type');
            let data = null;
            if (res.ok && contentType && contentType.includes('application/json')) {
                try {
                    data = await res.json();
                } catch (e) {
                    setError('Erro ao processar resposta do servidor (JSON inválido).');
                    setLoading(false);
                    return;
                }
                if (data.success) {
                    setSuccess(data.message || 'Chamado criado com sucesso!');
                    setSubject(''); setMessage(''); setEmail(''); setName(''); setTelefone(''); setImage(null);
                    if (onTicketCreated) onTicketCreated({ id: 0, title: subject, status: 'nao_aberto', name, email, subject, message, telefone });
                } else {
                    setError(data.message || 'Erro ao criar chamado.');
                }
            } else if (res.ok) {
                setError('Resposta inesperada do servidor.');
            } else {
                const text = await res.text();
                setError(text || 'Erro ao criar chamado.');
            }
        } catch (err) {
            setError('Erro de conexão.');
        }
        setLoading(false);
    };

    return (
        <form onSubmit={handleSubmit} style={{ marginBottom: 24 }}>
            <h2>Abrir Novo Chamado</h2>
            <input value={name} onChange={e => setName(e.target.value)} placeholder="Nome" required />
            <input value={email} onChange={e => setEmail(e.target.value)} placeholder="E-mail" type="email" required />
            <input value={telefone} onChange={e => setTelefone(e.target.value)} placeholder="Telefone" required />
            <input value={subject} onChange={e => setSubject(e.target.value)} placeholder="Assunto" required />
            <textarea value={message} onChange={e => setMessage(e.target.value)} placeholder="Mensagem" required />
            <input type="file" accept="image/*" onChange={e => setImage(e.target.files && e.target.files[0] ? e.target.files[0] : null)} />
            <button type="submit" disabled={loading}>{loading ? 'Enviando...' : 'Abrir Chamado'}</button>
            {success && <div style={{ color: 'green' }}>{success}</div>}
            {error && <div style={{ color: 'red' }}>{error}</div>}
        </form>
    );
};

export default TicketForm;
