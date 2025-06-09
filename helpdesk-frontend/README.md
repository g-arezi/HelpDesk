# Helpdesk Frontend

This project is a helpdesk application built with React and TypeScript. It provides a user-friendly interface for managing support tickets, including viewing, creating, and resolving tickets.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Folder Structure](#folder-structure)
- [Components](#components)
- [Styles](#styles)
- [License](#license)

## Installation

To get started with the project, follow these steps:

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd helpdesk-frontend
   ```

3. Install the dependencies:
   ```
   npm install
   ```

4. Start the development server:
   ```
   npm start
   ```

The application will be available at `http://localhost:3000`.

## Usage

Once the application is running, you can navigate through the different pages:

- **Home**: The landing page of the application.
- **Tickets**: The interface for managing support tickets.
- **Not Found**: Displays a 404 error message for undefined routes.

## Folder Structure

```
helpdesk-frontend
├── public
│   └── index.html          # Main HTML file
├── src
│   ├── assets              # Static assets (images, fonts, etc.)
│   ├── components          # Reusable components
│   ├── pages               # Page components
│   ├── styles              # Global styles
│   ├── App.tsx             # Main application component
│   └── main.tsx            # Entry point for the React application
├── package.json            # NPM configuration
└── tsconfig.json           # TypeScript configuration
```

## Components

- **Dashboard**: Displays the main dashboard interface with statistics and summaries.
- **Sidebar**: Provides navigation links for the application.
- **TicketList**: Renders a list of support tickets.
- **TicketDetail**: Displays detailed information about a selected support ticket.
- **Header**: Contains the application header with the title and user information.

## Styles

The global styles for the application are defined in `src/styles/global.css`. You can customize the look and feel of the application by modifying this file.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.