const db = require('../../config/database');
const path = require('path');

class PageController {
    static home(req, res) {
        res.render('dashboard', { title: 'Dashboard', path: '/dashboard', pendingCustomers: [] });
    }

    static async dashboard(req, res) {
        try {
            // Fetch recent 5 customers for pending list
            const [rows] = await db.query('SELECT * FROM customers ORDER BY created_at DESC LIMIT 5');
            res.render('dashboard', {
                title: 'Dashboard',
                path: '/dashboard',
                pendingCustomers: rows
            });
        } catch (error) {
            console.error(error);
            res.render('dashboard', { title: 'Dashboard', path: '/dashboard', pendingCustomers: [] });
        }
    }

    static login(req, res) {
        res.send('<h1>Login Placeholder</h1>');
    }
}

module.exports = PageController;
