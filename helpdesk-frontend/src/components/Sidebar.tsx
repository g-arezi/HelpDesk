import React from 'react';
import { Link } from 'react-router-dom';
import './Sidebar.css'; // Assuming you will create a CSS file for styling

const Sidebar: React.FC = () => {
    return (
        <div className="sidebar">
            <h2>Helpdesk</h2>
            <nav>
                <ul>
                    <li>
                        <Link to="/">Home</Link>
                    </li>
                    <li>
                        <Link to="/tickets">Tickets</Link>
                    </li>
                    <li>
                        <Link to="/dashboard">Dashboard</Link>
                    </li>
                </ul>
            </nav>
        </div>
    );
};

export default Sidebar;