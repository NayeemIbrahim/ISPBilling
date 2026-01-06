const db = require('../../config/database');

class CustomerController {

    // --- Views ---

    static async index(req, res) {
        try {
            const [rows] = await db.query('SELECT * FROM customers ORDER BY id DESC');
            res.render('customers/index', { title: 'All Customers', path: '/customers', customers: rows });
        } catch (error) { res.status(500).send(error.message); }
    }

    static createPage(req, res) {
        res.render('customers/create', { title: 'Create Customer', path: '/customers/create' });
    }

    static async editPage(req, res) {
        try {
            const [rows] = await db.query('SELECT * FROM customers WHERE id = ?', [req.params.id]);
            if (rows.length === 0) return res.send('Customer not found');
            res.render('customers/edit', { title: 'Edit Customer', path: '/customers', customer: rows[0] });
        } catch (error) { res.status(500).send(error.message); }
    }

    static searchPage(req, res) { res.redirect('/customers'); }

    // --- API / Actions ---

    static async store(req, res) {
        try {
            const data = req.body;
            const sql = `INSERT INTO customers (
                full_name, company_name, contact_person, email, identification_no, mobile_no, alt_mobile_no, professional_detail,
                district, thana, area, building_name, floor, tj_box, house_no,
                fiber_code, onu_mac, group_name, lazar_info, server_info, connection_date,
                mikrotik_id, ip_address, mac_address, bandwidth, comment,
                package_name, monthly_rent, due_amount, additional_charge, discount, advance_amount, vat_percent, total_amount,
                billing_type, connectivity_type, connection_type, client_type, distribution_point, description, note, connected_by, security_deposit
            ) VALUES (?)`;

            const values = [
                data.full_name, data.company_name, data.contact_person, data.email, data.identification_no, data.mobile_no, data.alt_mobile_no, data.professional_detail,
                data.district, data.thana, data.area, data.building_name, data.floor, data.tj_box, data.house_no,
                data.fiber_code, data.onu_mac, data.group_name, data.lazar_info, data.server_info, data.connection_date || null,
                data.mikrotik_id, data.ip_address, data.mac_address, data.bandwidth, data.comment,
                data.package_name, data.monthly_rent, data.due_amount, data.additional_charge, data.discount, data.advance_amount, data.vat_percent, data.total_amount,
                data.billing_type, data.connectivity_type, data.connection_type, data.client_type, data.distribution_point, data.description, data.note, data.connected_by, data.security_deposit
            ];

            await db.query(sql, [values]);
            res.json({ status: 'success', message: 'Customer created!' });
        } catch (error) { res.status(500).json({ status: 'error', message: error.message }); }
    }

    static async update(req, res) {
        try {
            const id = req.params.id;
            const data = req.body;
            const sql = `UPDATE customers SET 
                full_name=?, company_name=?, contact_person=?, email=?, identification_no=?, mobile_no=?, alt_mobile_no=?, professional_detail=?,
                district=?, thana=?, area=?, building_name=?, floor=?, tj_box=?, house_no=?,
                fiber_code=?, onu_mac=?, group_name=?, lazar_info=?, server_info=?, connection_date=?,
                mikrotik_id=?, ip_address=?, mac_address=?, bandwidth=?, comment=?,
                package_name=?, monthly_rent=?, due_amount=?, additional_charge=?, discount=?, advance_amount=?, vat_percent=?, total_amount=?,
                billing_type=?, connectivity_type=?, connection_type=?, client_type=?, distribution_point=?, description=?, note=?, connected_by=?, security_deposit=?
                WHERE id = ?`;

            const values = [
                data.full_name, data.company_name, data.contact_person, data.email, data.identification_no, data.mobile_no, data.alt_mobile_no, data.professional_detail,
                data.district, data.thana, data.area, data.building_name, data.floor, data.tj_box, data.house_no,
                data.fiber_code, data.onu_mac, data.group_name, data.lazar_info, data.server_info, data.connection_date || null,
                data.mikrotik_id, data.ip_address, data.mac_address, data.bandwidth, data.comment,
                data.package_name, data.monthly_rent, data.due_amount, data.additional_charge, data.discount, data.advance_amount, data.vat_percent, data.total_amount,
                data.billing_type, data.connectivity_type, data.connection_type, data.client_type, data.distribution_point, data.description, data.note, data.connected_by, data.security_deposit,
                id
            ];

            await db.query(sql, values);
            res.json({ status: 'success', message: 'Updated successfully' });
        } catch (error) { res.status(500).json({ status: 'error', message: error.message }); }
    }

    static async delete(req, res) {
        try {
            await db.query('DELETE FROM customers WHERE id = ?', [req.params.id]);
            res.redirect('/customers');
        } catch (error) { res.send('Error deleting customer'); }
    }

    static async search(req, res) {
        try {
            const query = req.query.q;
            let sql = 'SELECT * FROM customers ORDER BY id DESC LIMIT 20';
            let params = [];
            if (query) {
                sql = `SELECT * FROM customers WHERE full_name LIKE ? OR mobile_no LIKE ? OR company_name LIKE ? LIMIT 50`;
                params = [`%${query}%`, `%${query}%`, `%${query}%`];
            }
            const [rows] = await db.query(sql, params);
            res.json({ status: 'success', data: rows });
        } catch (error) { res.status(500).json({ status: 'error', message: error.message }); }
    }

    static async seed(req, res) {
        // Keeping seed simple for now
        res.redirect('/customers');
    }
}

module.exports = CustomerController;
