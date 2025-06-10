import React from 'react';
import './Header.css'; // Assuming you have a CSS file for styling

interface HeaderProps {
    onLogout?: () => void;
}

const Header: React.FC<HeaderProps> = ({ onLogout }) => {
    return (
        <header className="header">
            <div className="header-content">
                <h1 className="header-title">Helpdesk System</h1>
                <div className="user-info">
                    <span className="user-name">Welcome, Admin</span>
                    {onLogout && (
                        <button onClick={onLogout} style={{marginLeft:16,background:'#d32f2f',color:'#fff',border:'none',padding:'6px 16px',borderRadius:4,cursor:'pointer'}}>Logout</button>
                    )}
                </div>
            </div>
        </header>
    );
};

export default Header;