import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Switch } from 'react-router-dom';
import Home from './pages/Home';
import Tickets from './pages/Tickets';
import NotFound from './pages/NotFound';
import Header from './components/Header';
import Sidebar from './components/Sidebar';
import LoginForm from './components/LoginForm';
import PublicPanel from './pages/PublicPanel';
import Dashboard from './components/Dashboard';
import './styles/global.css';

const App: React.FC = () => {
  const [isLogged, setIsLogged] = useState(false);
  const [ticketsRefresh, setTicketsRefresh] = useState(0);

  const handleLogin = () => {
    setIsLogged(true);
  };

  const handleLogout = async () => {
    try {
      const res = await fetch(`${process.env.REACT_APP_API_URL}/logout.php`, { credentials: 'include' });
      if (!res.ok) {
        alert('Erro ao fazer logout. Tente novamente.');
        return;
      }
      setIsLogged(false);
    } catch (e) {
      alert('Erro de conexão com o servidor. Verifique se o backend está rodando.');
    }
  };

  // Função para ser passada para TicketForm, Dashboard, etc
  const handleTicketsChange = () => {
    setTicketsRefresh(r => r + 1);
  };

  if (!isLogged) {
    // Usuário comum: painel público
    return <PublicPanel onLogin={handleLogin} />;
  }

  // Técnico/admin: painel completo
  return (
    <Router>
      <div className="app-container">
        <Header onLogout={handleLogout} />
        <div className="main-content">
          <Sidebar />
          <Switch>
            <Route path="/" exact component={Home} />
            <Route path="/tickets" render={() => <Tickets refresh={ticketsRefresh} onTicketChange={handleTicketsChange} />} />
            <Route path="/dashboard" render={() => <Dashboard refresh={ticketsRefresh} onTicketChange={handleTicketsChange} />} />
            <Route component={NotFound} />
          </Switch>
        </div>
      </div>
    </Router>
  );
};

export default App;