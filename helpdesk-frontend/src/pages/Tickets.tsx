import React from 'react';
import TicketList from '../components/TicketList';
import TicketDetail from '../components/TicketDetail';
import TicketForm from '../components/TicketForm';
import './Tickets.css';
import type { Ticket } from '../components/TicketList';

interface TicketsProps {
    refresh?: number;
    onTicketChange?: () => void;
}

const Tickets: React.FC<TicketsProps> = ({ refresh, onTicketChange }) => {
    const [selectedTicket, setSelectedTicket] = React.useState<Ticket | null>(null);

    const handleTicketSelect = (ticket: Ticket) => {
        setSelectedTicket(ticket);
    };
    const handleTicketCreated = () => {
        if (onTicketChange) onTicketChange();
        setSelectedTicket(null);
    };

    return (
        <div className="tickets-container">
            <h1>Ticket Management</h1>
            <TicketForm onTicketCreated={handleTicketCreated} />
            <div className="tickets-content">
                <TicketList onSelect={handleTicketSelect} onStatusChange={onTicketChange} key={refresh} />
                {selectedTicket && <TicketDetail ticket={selectedTicket} />}
            </div>
        </div>
    );
};

export default Tickets;