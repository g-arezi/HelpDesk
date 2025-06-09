import React, { useState } from 'react';

interface LoginFormProps {
    onSuccess?: (user?: string) => void;
    onError?: (error: string) => void;
}

const LoginForm: React.FC<LoginFormProps> = ({ onSuccess, onError }) => {
    const [login, setLogin] = useState('');
    const [senha, setSenha] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        try {
            const res = await fetch(`${process.env.REACT_APP_API_URL}/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ login, senha }).toString(),
                credentials: 'include',
            });
            const text = await res.text();
            if (res.ok && !text.includes('Login ou senha inválidos')) {
                if (onSuccess) onSuccess(login);
            } else {
                setError('Login ou senha inválidos!');
                if (onError) onError('Login ou senha inválidos!');
            }
        } catch {
            setError('Erro de conexão.');
            if (onError) onError('Erro de conexão.');
        }
        setLoading(false);
    };

    return (
        <form onSubmit={handleSubmit} style={{ margin: 24, background: '#fff', padding: 24, borderRadius: 8, boxShadow: '0 1px 4px #e0e0e0' }}>
            <h2>Login</h2>
            <input value={login} onChange={e => setLogin(e.target.value)} placeholder="Usuário" required />
            <input value={senha} onChange={e => setSenha(e.target.value)} placeholder="Senha" type="password" required />
            <button type="submit" disabled={loading}>{loading ? 'Entrando...' : 'Entrar'}</button>
            {error && <div style={{ color: 'red', marginTop: 8 }}>{error}</div>}
        </form>
    );
};

export default LoginForm;
